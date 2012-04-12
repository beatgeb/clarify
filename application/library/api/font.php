<?php

lock();

// Font API
define('API_FONT_ADD', 'color.add');
define('API_FONT_GET', 'color.get');
define('API_FONT_REMOVE', 'color.remove');

switch ($action) {
    
    case API_FONT_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $font_family = $route[7];
        $font_size = intval($route[8]);
        $font_color = substr($route[9],0,6);
        
        // check if color already exists in the library
        
        
        $font = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'x' => $x,
            'y' => $y,
            'size' => $font_size,
            'family' => $font_family,
            'color' => 1
        );
        $id = $db->insert('font', $font);
        $font['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode($font);
        break;
    
    case API_FONT_GET:
        header('Content-Type: application/json');
        echo json_encode($result);
        break;
    
    case API_FONT_REMOVE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a color instance id'); }
        $color = $db->single("
            SELECT c.screen, c.color, s.project, pc.id 
            FROM color c 
                LEFT JOIN project_color pc ON pc.id = c.color 
                LEFT JOIN screen s ON s.id = c.screen 
            WHERE c.id = '" . $id . "' AND c.creator = '" . userid() . "'
            LIMIT 1
        ");
        if (!$color) { die(); }
        $result = array();
        $db->delete('color', array('id' => $id));
        $count = $db->exists('color', array('color' => $color['color']));
        if ($count < 1) {
            $db->delete('project_color', array('id' => $color['id'], 'creator' => userid()));
            $result['remove'] = $color['id'];
        }
        $db->query("UPDATE screen SET count_color = count_color - 1 WHERE id = " . $color['screen'] . "");
        header('Content-Type: application/json');
        echo json_encode($result);
        break;

}