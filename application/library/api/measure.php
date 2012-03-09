<?php

switch ($action) {
        
    case API_MEASURE_ADD:
        $measure = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y']),
            'width' => intval($_REQUEST['width']),
            'height' => intval($_REQUEST['height'])
        );
        $id = $db->insert('measure', $measure);
        $measure['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode($measure);
        break;
        
    case API_MEASURE_GET:
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT id, x, y, width, height FROM measure WHERE screen = '" . $screen . "'");
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
    case API_MEASURE_MOVE:
        $id = intval($_REQUEST['id']);
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y'])
        );
        $db->update('measure', $data, array('id' => $id));
        break;
    
    case API_MEASURE_RESIZE:
        $id = intval($_REQUEST['id']);
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'width' => intval($_REQUEST['width']),
            'height' => intval($_REQUEST['height'])
        );
        $db->update('measure', $data, array('id' => $id));
        break;
    
    case API_MEASURE_DELETE:
        $id = intval($_REQUEST['id']);
        $db->delete('measure', array('id' => $id));
        echo json_encode(array('RESULT' => 'OK'));
        break;
    
}