<?php

// Measure API
define('API_MEASURE_ADD', 'measure.add');
define('API_MEASURE_GET', 'measure.get');
define('API_MEASURE_MOVE', 'measure.move');
define('API_MEASURE_RESIZE', 'measure.resize');
define('API_MEASURE_DELETE', 'measure.delete');

switch ($action) {
        
    case API_MEASURE_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        $width = intval($route[7]);
        $height = intval($route[8]);
        if ($screen < 1) { die('Please provide a screen id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $measure = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        );
        $id = $db->insert('measure', $measure);
        $measure['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode($measure);
        break;
        
    case API_MEASURE_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT id, x, y, width, height FROM measure WHERE screen = '" . $screen . "'");
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
    case API_MEASURE_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a measure id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => $x,
            'y' => $y
        );
        $db->update('measure', $data, array('id' => $id));
        break;
    
    case API_MEASURE_RESIZE:
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
        $db->update('measure', $data, array('id' => $id));
        break;
    
    case API_MEASURE_DELETE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a measure id'); }
        $db->delete('measure', array('id' => $id));
        echo json_encode(array('RESULT' => 'OK'));
        break;
    
}