<?php

// Project API
define('API_PROJECT_ADD', 'project.add');

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
    
}