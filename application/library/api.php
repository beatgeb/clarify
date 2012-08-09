<?php

/**
 * Log activity.
 *
 * @param $title Activity title
 * @param $actor Actor ID
 * @param $actor_type Actor type
 * @param $verb Verb
 * @param $object Object ID
 * @param $object_type Object type
 * @param $target Target ID
 * @param $target_type Target type
 */
function activity_add($title, $actor, $actor_type, $actor_title, $verb, $object, $object_type, $object_title, $target = null, $target_type = null, $target_title = null) {
    global $db;
    global $cache;

    // create activity object
    $activity = array(
        'created' => date('Y-m-d H:i:s'),
        'creator' => userid(),
        'actor' => $actor,
        'actor_type' => $actor_type,
        'actor_title' => $actor_title,
        'verb' => $verb,
        'object' => $object,
        'object_type' => $object_type,
        'object_title' => $object_title,
        'target' => $target,
        'target_type' => $target_type,
        'target_title' => $target_title,
        'title' => $title
    );
    
    // log activity into database
    $id = $db->insert('activity', $activity);

    // cache activity in memcache
    $cache->set('activity.' . $id, $activity);

    // push activity into streams
    stream_activity_add($actor_type, $actor, $id);
    stream_activity_add($object_type, $object, $id);
    if ($target > 0) {
        stream_activity_add($target_type, $target, $id);
    }
}

/**
 * Add an activity to a specific stream.
 *
 * @param $type Type of the object
 * @param $id ID of the object
 * @param $activity_id ID of the activity itself
 */
function stream_activity_add($type, $id, $activity_id) {
    global $cache;
    $stream = 'stream.' . $type . '.' . $id;
    $entries = $cache->get($stream);
    if (sizeof($entries) > 200) {
        array_shift($entries);
    }
    $entries[] = $activity_id;
    $cache->set($stream, $entries);
}

/**
 * Check project permission for current user
 */
function permission($project, $permission) {
    if (!has_permission($project, $permission)) {
        die('Permission denied.');
    }
}

/**
 * Check project permission for current user
 */
function has_permission($project, $permission) {
    $allowed = false;
    if (isset($_SESSION['user']['permissions']['project'][$project])) {
        $p = $_SESSION['user']['permissions']['project'][$project];
        switch ($permission) {
            case 'EDIT':
                if ($p == 'EDIT' || $p == 'ADMIN') {
                    $allowed = true;
                }
                break;
            case 'VIEW':
                if ($p == 'VIEW' || $p == 'COMMENT' || $p == 'EDIT' || $p == 'ADMIN') {
                    $allowed = true;
                }
                break;
            case 'COMMENT':
                if ($p == 'COMMENT' || $p == 'EDIT' || $p == 'ADMIN') {
                    $allowed = true;
                }
                break;
            case 'ADMIN':
                if ($p == 'ADMIN') {
                    $allowed = true;
                }
                break;
        }
    }
    return $allowed;
}