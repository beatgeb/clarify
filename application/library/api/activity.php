<?php

lock();

// Activity API
define('API_ACTIVITY_STREAM', 'activity.stream');

switch ($action) {

	case API_ACTIVITY_STREAM:
		$stream = $route[4];
		switch ($stream) {
			case OBJECT_TYPE_USER:
				$id = intval($route[5]);
				if (userid() != $id) { die(); }
				$stream = "stream." . $stream . "." . $id;
				$activity_ids = $cache->get($stream);
				$activities = array();
				if (is_array($activity_ids)) {
					foreach($activity_ids as $id) {
						$activity = $cache->get('activity.' . $id);
						$activities[] = $activity;
					}
				}
				header('Content-Type: application/json');
				echo json_encode(array('items' => $activities));
				break;
			case OBJECT_TYPE_PROJECT:
				$id = intval($route[5]);
				permission($id, 'VIEW');
				$stream = "stream." . $stream . "." . $id;
				$activity_ids = $cache->get($stream);
				$activities = array();
				if (is_array($activity_ids)) {
					foreach($activity_ids as $id) {
						$activity = $cache->get('activity.' . $id);
						$activities[] = $activity;
					}
				}
				header('Content-Type: application/json');
				echo json_encode(array('items' => $activities));
				break;
		}
		break;

}