<?php

// Library API
define('API_LIBRARY_COMPONENT_ADD', 'library.component.add');
define('API_LIBRARY_BEHAVIOUR_ADD', 'library.behaviour.add');
define('API_LIBRARY_BEHAVIOUR_OPTION_ADD', 'library.behaviour.option.add');
define('API_LIBRARY_BEHAVIOUR_EVENT_ADD', 'library.behaviour.event.add');

switch ($action) {
        
    case API_LIBRARY_BEHAVIOUR_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $_REQUEST['name'],
            'vendor' => $_REQUEST['vendor'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_behaviour', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_LIBRARY_BEHAVIOUR_EVENT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'behaviour' => $_REQUEST['behaviour'],
            'name' => $_REQUEST['name'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_behaviour_event', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_LIBRARY_BEHAVIOUR_OPTION_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $_REQUEST['name'],
            'behaviour' => $_REQUEST['behaviour'],
            'value_type' => $_REQUEST['type'],
            'value_default' => $_REQUEST['default'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_behaviour_option', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_LIBRARY_COMPONENT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $_REQUEST['name'],
            'vendor' => $_REQUEST['vendor'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_component', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
}