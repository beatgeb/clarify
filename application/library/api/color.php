<?php

switch ($action) {
    
    case API_COLOR_REMOVE:
        $color = $db->single("
            SELECT c.color, s.project, pc.id 
            FROM color c 
                LEFT JOIN project_color pc ON pc.id = c.color 
                LEFT JOIN screen s ON s.id = c.screen 
            WHERE c.id = '" . $id . "' 
            LIMIT 1
        ");
        $result = array();
        $db->delete('color', array('id' => $id));
        $count = $db->exists('color', array('color' => $color['color']));
        if ($count < 1) {
            $db->delete('project_color', array('id' => $color['id']));
            $result['remove'] = $color['id'];
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        break;
    
    case API_COLOR_GET:
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT c.id, c.x, c.y, pc.r, pc.g, pc.b, pc.alpha, pc.hex FROM color c LEFT JOIN project_color pc ON pc.id = c.color WHERE screen = " . $screen);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
    case API_COLOR_ADD:
        
        // explicitly use a library color
        if (isset($_REQUEST['color'])) {
            $color = $db->single("SELECT * FROM project_color WHERE id = '" . intval($_REQUEST['color']) . "' LIMIT 1");
            $r = $color['r'];
            $g = $color['g'];
            $b = $color['b'];
            $a = $color['alpha'];
            $hex = $color['hex'];
        } else {
            $r = intval($_REQUEST['r']);
            $g = intval($_REQUEST['g']);
            $b = intval($_REQUEST['b']);
            $a = intval($_REQUEST['a']);
            $hex = substr($_REQUEST['hex'],1,6);
        }
        
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $screen['project'],
            'r' => $r,
            'g' => $g,
            'b' => $b,
            'alpha' => $a,
            'hex' => $db->escape($hex)
        );
        $result = 'EXISTING';
        $existing = $db->single('
            SELECT id 
            FROM project_color 
            WHERE project = ' . $screen['project'] . ' AND r = ' . $r . ' AND g = ' . $g . ' AND b = ' . $b . ' AND alpha = ' . $a . '
        ');
        $id = $existing['id'];
        if (!$existing) {
            $id = $db->insert('project_color', $data);
            $result = 'NEW';
        }
        
        // add reference to color
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'color' => $id,
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y'])
        );
        $id = $db->insert('color', $data);
        $data['id'] = $id;
        $data['r'] = $r;
        $data['g'] = $g;
        $data['b'] = $b;
        $data['hex'] = $hex;
        $data['alpha'] = $a;
        $data['result'] = $result;
        
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
}