<?php

lock();

// Typography API
define('API_TYPOGRAPHY_ADD', 'font.add');
define('API_TYPOGRAPHY_GET', 'font.get');
define('API_TYPOGRAPHY_DELETE', 'font.delete');
define('API_TYPOGRAPHY_UPDATE', 'font.update');
define('API_TYPOGRAPHY_MOVE', 'font.move');
define('API_TYPOGRAPHY_RESIZE', 'font.resize');

switch ($action) {
    
    case API_TYPOGRAPHY_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        $width = intval($route[7]);
        $height = intval($route[8]);
        if ($screen < 1) { die('Please provide a screen id'); }

        $font = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'nr' => 1,
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        );
        $id = $db->insert('font', $font);
        $font['id'] = $id;

        // increase count on screen
        $db->query("UPDATE screen SET count_font = count_font + 1 WHERE id = " . $screen . "");
        
        // return result
        header('Content-Type: application/json');
        echo json_encode($font);
        break;
    
    case API_TYPOGRAPHY_UPDATE:
        //$font_family = $route[7];
        //$font_size = intval($route[8]);
        //$font_color = substr($route[9],0,6);
        
        // check if font already exists in the library
        

        break;

    case API_TYPOGRAPHY_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'VIEW');
        $data = $db->data("
            SELECT 
                f.id, f.creator, f.nr, f.x, f.y, f.width, f.height,
                pf.name, pf.family, pf.size, pf.line_height,
                pc.name as color_name, pc.hex as color_hex
            FROM font f 
                LEFT JOIN project_font pf ON (pf.id = f.font)
                LEFT JOIN project_color pc ON (pc.id = pf.color)
            WHERE f.screen = " . $screen['id']
        );
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
    case API_TYPOGRAPHY_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a font id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => $x,
            'y' => $y
        );
        $db->update('font', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_TYPOGRAPHY_RESIZE:
        $id = intval($route[4]);
        $width = intval($route[5]);
        $height = intval($route[6]);
        if ($id < 1) { die('Please provide a measure id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'width' => $width,
            'height' => $height
        );
        $db->update('font', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_TYPOGRAPHY_DELETE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a font id'); }
        $font = $db->single('SELECT screen FROM font WHERE id = ' . $id . ' AND creator = ' . userid());
        if (!$font) { die(); }
        $db->delete('font', array('id' => $id));
        $db->query("UPDATE screen SET count_font = count_font - 1 WHERE id = " . $font['screen'] . "");
        echo json_encode(array('RESULT' => 'OK'));
        break;

}