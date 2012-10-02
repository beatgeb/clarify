<?php

lock();

// Project API
define('API_PROJECT_ADD', 'project.add');
define('API_PROJECT_DELETE', 'project.delete');
define('API_PROJECT_SETTING', 'project.setting');

switch ($action) {
    
    case API_PROJECT_SETTING:
        lock();
        $project = intval($route[4]);
        $setting = $route[5];
        switch ($setting) {
            case 'name':
                $value = $route[6];
                $db->update('project', array('name' => urldecode($value)), array('id' => $project, 'creator' => userid()));
                break;
        }
        break;

    case API_PROJECT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $db->escape($_REQUEST['name']),
            'slug' => slug($_REQUEST['name'])
        );
        $id = $db->insert('project', $data);

        // add to activity stream
        activity_add(
            '{actor} created a new project {object}', 
            userid(), OBJECT_TYPE_USER, user('name'), 
            ACTIVITY_VERB_CREATE, 
            $id, OBJECT_TYPE_PROJECT, $data['name']
        );
        
        // invalidate permission cache
        $cache->delete('projects-' . userid());

        $data['id'] = $id;
        $data['url'] = R . 'project/' . userid() . '/' . $data['slug'] . '/';
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