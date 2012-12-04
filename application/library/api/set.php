<?php

lock();

// Project API
define('API_SET_ADD', 'set.add');
define('API_SET_CREATE', 'set.create');
define('API_SET_DELETE', 'set.delete');

switch ($action) {
    
    case API_SET_DELETE:
        $set_id = intval($route[4]);
        $set = $db->single("SELECT project FROM `set` WHERE id = " . $set_id);
        permission($set['project'], 'EDIT');
        $db->delete('set', array('id' => $set_id));
        $db->delete('set_screen', array('set' => $set_id));
        break;

    case API_SET_CREATE:
        $project_id = intval($route[4]);
        $name = urldecode($route[5]);
        permission($project_id, 'EDIT');
        $set = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $project_id,
            'name' => $name,
            'slug' => slug($name)
        );
        $id = $db->insert('set', $set);
        $set['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode(array('success' => true, 'set' => $set));
        break;

    case API_SET_ADD:
    	$set_id = intval($route[4]);
    	$screen_id = intval($route[5]);
    	$set = $db->single("SELECT project, screen_count FROM `set` WHERE id = " . $set_id);
    	permission($set['project'], 'EDIT');
    	$data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'set' => $set_id,
            'screen' => $screen_id
        );
    	$db->insert('set_screen', $data);
    	$db->query("UPDATE `set` SET screen_count = screen_count + 1 WHERE id = " . $set_id);
    	$set = $db->single('
	        SELECT
	            `set`.id,
	            `set`.screen_count,
	            `set`.name,
	            `set`.project,
	            s.id as screen_id,
	            s.ext as screen_ext
	        FROM
	            `set` 
	            LEFT JOIN set_screen ss ON ss.`set` = `set`.id
	            LEFT JOIN screen s ON s.id = ss.screen
	        WHERE
	            `set`.id = ' . $set_id . '
	        GROUP BY
	            `set`.id
	    ');
	    $set['image'] = '/upload/screens/' . $set['project'] . '/thumbnails/' . md5($set['screen_id'] . config('security.general.hash')) . '.' . $set['screen_ext'];
    	header('Content-Type: application/json');
        echo json_encode(array('success' => true, 'set' => $set));
    	break;
    	
}