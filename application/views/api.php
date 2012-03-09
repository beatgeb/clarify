<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

// Project API
define('API_PROJECT_ADD', 'project.add');

// Comment API
define('API_COMMENT_ADD', 'comment.add');
define('API_COMMENT_REMOVE', 'comment.remove');
define('API_COMMENT_MOVE', 'comment.move');
define('API_COMMENT_CLEAR', 'comment.clear');
define('API_COMMENT_RESIZE', 'comment.resize');
define('API_COMMENT_UPDATE', 'comment.update');
define('API_COMMENT_GET', 'comment.get');

// Screen API
define('API_SCREEN_DELETE', 'screen.delete');
define('API_SCREEN_UPLOAD', 'screen.upload');
define('API_SCREEN_IMAGE', 'screen.image');
define('API_SCREEN_THUMBNAIL', 'screen.thumbnail');
define('API_SCREEN_EMBED', 'screen.embed');

// Color API
define('API_COLOR_ADD', 'color.add');
define('API_COLOR_GET', 'color.get');
define('API_COLOR_REMOVE', 'color.remove');

// Measure API
define('API_MEASURE_ADD', 'measure.add');
define('API_MEASURE_GET', 'measure.get');
define('API_MEASURE_MOVE', 'measure.move');
define('API_MEASURE_RESIZE', 'measure.resize');
define('API_MEASURE_DELETE', 'measure.delete');

// Library API
define('API_LIBRARY_COMPONENT_ADD', 'library.component.add');
define('API_LIBRARY_BEHAVIOUR_ADD', 'library.behaviour.add');
define('API_LIBRARY_BEHAVIOUR_OPTION_ADD', 'library.behaviour.option.add');
define('API_LIBRARY_BEHAVIOUR_EVENT_ADD', 'library.behaviour.event.add');

$project = $_REQUEST['project'] > 0 ? intval($_REQUEST['project']) : 1;
$layer = $_REQUEST['layer'] > 0 ? intval($_REQUEST['layer']) : null;
$screen = $_REQUEST['screen'] > 0 ? intval($_REQUEST['screen']) : null;
$id = $_REQUEST['id'] > 0 ? intval($_REQUEST['id']) : null;

// I know, this is super-simple, non rocket-architecture stuff, but after I know
// where the thing is going, there is enough time to split things up and add
// a matching architecture. Keep it simple stupid.
$action = $_REQUEST['action'];
$action_data = explode('.', $action);
if (sizeof($action_data) > 0) {
    require LIBRARY . 'api/' . strtolower($action_data[0]) . '.php';
}

?>