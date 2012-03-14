<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$api = $route[2];
$action = $api . '.' . $route[3];
require LIBRARY . 'api/' . strtolower($api) . '.php';

?>