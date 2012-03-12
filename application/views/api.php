<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

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