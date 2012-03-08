<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$components = $db->data("SELECT * FROM library_component ORDER BY name ASC");
$behaviours = $db->data("SELECT * FROM library_behaviour ORDER BY name ASC");
$options = $db->data("SELECT * FROM library_behaviour_option ORDER BY name ASC", "behaviour", false);
$events = $db->data("SELECT * FROM library_behaviour_event ORDER BY name ASC", "behaviour", false);

?>
<!DOCTYPE html>
<html class="mod modLayout skinLayoutLibrary">
<head>
    <title>Interactive Frontend Styleguide</title>
    <link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css' />
    <? require 'partials/head.php' ?>
</head>
<body>
    <h1>Library</h1>
    <h2>Add Component</h2>
    <form class="form-add-component" method="post">
        <input class="component-vendor" type="text" value="Namics" />
        <input class="component-name" type="text" value="" />
        <input class="btn" type="submit" value="Add Component" />
    </form>
    <h2>Add Behaviour</h2>
    <form class="form-add-behaviour" method="post">
        <input class="behaviour-vendor" type="text" value="Namics" />
        <input class="behaviour-name" type="text" value="" />
        <input class="btn" type="submit" value="Add Behaviour" />
    </form>
    <h2>Add Behaviour Option</h2>
    <form class="form-add-behaviour-option" method="post">
        <select class="behaviour-option-behaviour">
            <? foreach ($behaviours as $behaviour) { ?>
            <option value="<?= $behaviour['id'] ?>"><?= $behaviour['name'] ?> (<?= $behaviour['vendor'] ?>)</option>
            <? } ?>
        </select>
        <input class="behaviour-option-name" type="text" value="" />
        <select class="behaviour-option-type">
            <option>number</option>
            <option>string</option>
            <option>selector</option>
            <option>boolean</option>
            <option>number|object</option>
        </select>
        <input class="behaviour-option-default" type="text" value="" />
        <input class="behaviour-option-description" type="text" value="" />
        <input class="btn" type="submit" value="Add Behaviour Option" />
    </form>
    <h2>Add Behaviour Event</h2>
    <form class="form-add-behaviour-event" method="post">
        <select class="behaviour-event-behaviour">
            <? foreach ($behaviours as $behaviour) { ?>
            <option value="<?= $behaviour['id'] ?>"><?= $behaviour['name'] ?> (<?= $behaviour['vendor'] ?>)</option>
            <? } ?>
        </select>
        <input class="behaviour-event-name" type="text" value="" />
        <input class="behaviour-event-description" type="text" value="" />
        <input class="btn" type="submit" value="Add Behaviour Event" />
    </form>
    <h2>Components</h2>
    <? foreach ($components as $component) { ?>
    <div><?= $component['name'] ?> (<?= $behaviour['vendor'] ?>)</div>
    <? } ?>
    <br />
    <h2>Behaviours</h2>
    <? foreach ($behaviours as $behaviour) { ?>
        <div><strong><?= $behaviour['name'] ?> (<?= $behaviour['vendor'] ?>)</strong></div>
        <? if (isset($options[$behaviour['id']])) { ?>
        <? foreach ($options[$behaviour['id']] as $option) { ?>
        <div>Option: <strong><?= $option['name'] ?></strong> - <i><?= $option['description'] ?> (Default: <?= $option['value_default'] ?>)</i></div>
        <? } ?>
        <? } ?>
        <? if (isset($events[$behaviour['id']])) { ?>
        <? foreach ($events[$behaviour['id']] as $event) { ?>
        <div>Event: <strong><?= $event['name'] ?></strong> - <i><?= $event['description'] ?></i></div>
        <? } ?>
        <? } ?>
    <? } ?>
    <? require 'partials/foot.php'; ?>
    <script type="text/javascript">
    $(document).ready(function() {
        $('.form-add-component').submit(function() {
            $.ajax({
                url: "?view=api&action=library.component.add",
                dataType: 'json',
                data: "name=" + $('.component-name').val() + "&vendor=" + $('.component-vendor').val(),
                success: function(data){
                    alert('done');
                }
            });
            return false;
        });
        $('.form-add-behaviour').submit(function() {
            $.ajax({
                url: "?view=api&action=library.behaviour.add",
                dataType: 'json',
                data: "name=" + $('.behaviour-name').val() + "&vendor=" + $('.behaviour-vendor').val(),
                success: function(data){
                    alert('done');
                }
            });
            return false;
        });
        $('.form-add-behaviour-option').submit(function() {
            $.ajax({
                url: "?view=api&action=library.behaviour.option.add",
                dataType: 'json',
                data: "behaviour=" + $('.behaviour-option-behaviour').val() + "&name=" + $('.behaviour-option-name').val() + "&type=" + $('.behaviour-option-type').val() + "&default=" + $('.behaviour-option-default').val() + "&description=" + $('.behaviour-option-description').val(),
                success: function(data){
                    alert('done');
                }
            });
            return false;
        });
        $('.form-add-behaviour-event').submit(function() {
            $.ajax({
                url: "?view=api&action=library.behaviour.event.add",
                dataType: 'json',
                data: "name=" + $('.behaviour-event-name').val() + "&description=" + $('.behaviour-event-description').val(),
                success: function(data){
                    alert('done');
                }
            });
            return false;
        });
    });
    </script>
</body>
</html>