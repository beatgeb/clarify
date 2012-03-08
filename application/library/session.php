<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/**
 * Open a session
 * @param path - session save path
 * @param id - session id
 */
function sess_open($path, $id) {
    global $cache;
    return true;
}

/**
 * Close a session
 */
function sess_close() {
	global $cache;
	if (is_object($cache)) {
	    $cache->close();
	}
    return true;
}

/**
 * Read a session with the given id
 * @param id session id
 */
function sess_read($id) {
	global $cache;
    $cachedata = $cache->get('ect_session_' . $id);
	return $cachedata;
}

/**
 * Write data to a session with given id
 * @param id - session id
 * @param data - session data
 */
function sess_write($id, $data) {
    global $cache;
	$user_id = $_SESSION['user']['id'];
    $cachedata = $cache->get('ect_session_' . $id);
	$cache->set('ect_session_' . $id, $data, false);
	$cache->set('ect_user_online_' . $user_id, true, false);
	$online = $cache->get('ect_online');
	if (!is_array($online)) {
		$online = array($user_id => true);
		$cache->set('ect_online', $online);
	} else {
		if (!array_key_exists($user_id, $online)) {
			$online[$user_id] = true;
			$cache->set('ect_online', $online);
		}
	}
}

/**
 * Destroy session with the given id
 * @param id - session id
 */
function sess_destroy($id) {
	global $cache;
	$user_id = $_SESSION['user']['id'];
	$cache->delete('ect_user_online_' . $user_id);
    $cache->delete('ect_session_' . $id);
	$online = $cache->get('ect_online');
	if (is_array($online)) {
		if (in_array($user_id, $online)) {
			unset($online[$user_id]);
			$cache->set('ect_online', $online);
		}
	}
}

/**
 * Run garbage collector on sessions
 * @param maxlifetime - maximum session lifetime in seconds
 */
function sess_gc($maxlifetime) {
	
}

?>