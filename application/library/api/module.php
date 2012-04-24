<?php

lock();

// Module API
define('API_MODULE_ADD', 'module.add');
define('API_MODULE_GET', 'module.get');
define('API_MODULE_MOVE', 'module.move');
define('API_MODULE_RESIZE', 'module.resize');
define('API_MODULE_DELETE', 'module.delete');

switch ($action) {
        
    case API_MODULE_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        $width = intval($route[7]);
        $height = intval($route[8]);
        if ($screen < 1) { die('Please provide a screen id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $screen = $db->single("SELECT id FROM screen WHERE id = " . $screen . " AND creator = " . userid());
        if (!$screen) { die(); }
        $module = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        );
        $id = $db->insert('module', $module);
        $db->query("UPDATE screen SET count_module = count_module + 1 WHERE id = " . $screen['id'] . "");
        $module['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode($module);
        break;
        
    case API_MODULE_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT id, x, y, width, height FROM module WHERE screen = '" . $screen . "' AND creator = " . userid());
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
    case API_MODULE_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a module id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => $x,
            'y' => $y
        );
        $db->update('module', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_MODULE_RESIZE:
        $id = intval($route[4]);
        $width = intval($route[5]);
        $height = intval($route[6]);
        if ($id < 1) { die('Please provide a module id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'width' => $width,
            'height' => $height
        );
        $db->update('module', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_MODULE_DELETE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a module id'); }
        $module = $db->single('SELECT screen FROM module WHERE id = ' . $id . ' AND creator = ' . userid());
        if (!$module) { die(); }
        $db->delete('module', array('id' => $id));
        $db->query("UPDATE screen SET count_module = count_module - 1 WHERE id = " . $module['screen'] . "");
        echo json_encode(array('RESULT' => 'OK'));
        break;
    
}