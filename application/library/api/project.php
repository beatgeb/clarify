<?php

// Project API
define('API_PROJECT_ADD', 'project.add');
define('API_PROJECT_DELETE', 'project.delete');

switch ($action) {
    
    case API_PROJECT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $db->escape($_REQUEST['name'])
        );
        $id = $db->insert('project', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_PROJECT_DELETE:
        $project = intval($route[4]);
        $screens = $db->data("SELECT id FROM screen WHERE project = " . $project . " AND creator = " . userid());
        // TODO: load colors referenced by this screen and delete
        //       color form library if it doesn't exist on another
        //       screen
        foreach ($screens as $screen) {
            $db->delete('color', array('screen' => $screen['id']));
            $db->delete('comment', array('screen' => $screen['id']));
            $db->delete('measure', array('screen' => $screen['id']));
            $db->delete('screen', array('id' => $screen['id']));
        }
        $db->delete('project', array('id' => $project, 'creator' => userid()));
        echo json_encode(array('result' => 'OK'));
        break;
    
}