<?php

lock();

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
        $screen = $db->single("SELECT id, project FROM screen WHERE id = " . $screen . "");
        if (!$screen) { die(); }
        permission($screen['project'], 'EDIT');
        $measure = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        );
        $id = $db->insert('measure', $measure);
        $db->query("UPDATE screen SET count_measure = count_measure + 1 WHERE id = " . $screen['id'] . "");
        $measure['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode($measure);
        break;
        
    case API_MEASURE_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'VIEW');
        $data = $db->data("SELECT id, x, y, width, height FROM measure WHERE screen = '" . $screen['id'] . "'");
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
        $db->update('measure', $data, array('id' => $id, 'creator' => userid()));
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
        $db->update('measure', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_MEASURE_DELETE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a measure id'); }
        $measure = $db->single('SELECT screen FROM measure WHERE id = ' . $id . ' AND creator = ' . userid());
        if (!$measure) { die(); }
        $db->delete('measure', array('id' => $id));
        $db->query("UPDATE screen SET count_measure = count_measure - 1 WHERE id = " . $measure['screen'] . "");
        echo json_encode(array('RESULT' => 'OK'));
        break;
    
}