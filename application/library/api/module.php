<?php

lock();

// Module API
define('API_MODULE_ADD', 'module.add');
define('API_MODULE_GET', 'module.get');
define('API_MODULE_MOVE', 'module.move');
define('API_MODULE_RESIZE', 'module.resize');
define('API_MODULE_REMOVE', 'module.remove');
define('API_MODULE_RENAME', 'module.rename');

switch ($action) {
    case API_MODULE_REMOVE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a module id'); }
        $module = $db->single("
            SELECT m.screen, m.module, s.project, pm.id
            FROM module m
                LEFT JOIN project_module pm ON pm.id = m.module
                LEFT JOIN screen s ON s.id = m.screen
            WHERE m.id = '" . $id . "' AND m.creator = '" . userid() . "'
            LIMIT 1
        ");
        if (!$module) { die(); }
        $result = array();
        $db->delete('module', array('id' => $id));
        $count = $db->exists('module', array('module' => $module['module']));
        if ($count < 1) {
            $db->delete('project_module', array('id' => $module['id'], 'creator' => userid()));
            $result['remove'] = $module['id'];
        }
        $db->query("UPDATE screen SET count_module = count_module - 1 WHERE id = " . $module['screen'] . "");
        header('Content-Type: application/json');
        echo json_encode($result);
        break;

    case API_MODULE_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT m.id, m.x, m.y, m.width, m.height, pm.name, pm.skin FROM module m LEFT JOIN project_module pm ON pm.id = m.module WHERE m.screen = " . $screen . " AND m.creator = " . userid());
        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case API_MODULE_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        $width = intval($route[7]);
        $height = intval($route[8]);
        $module = intval($route[9]);
        if ($screen < 1) { die('Please provide a screen id'); }

        // explicitly use a library module
        if ($module > 0) {
            $module = $db->single("SELECT * FROM project_module WHERE id = '" . $module . "' AND creator = " . userid() . " LIMIT 1");
            $name = $module['name'];
            $skin = $module['skin'];
        } else {
            $name = $route[9];
            $skin = '';
        }

        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "' AND creator = " . userid());
        if (!$screen) { die(); }
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $screen['project'],
            'name' => $name,
            'skin' => $skin
        );
        $result = 'EXISTING';
        $existing = $db->single('
            SELECT id
            FROM project_module
            WHERE project = ' . $screen['project'] . ' AND name = "' . $name . '" AND skin = "' . $skin . '"
        ');

        $id = $existing['id'];
        if (!$existing) {
            $id = $db->insert('project_module', $data);
            $result = 'NEW';
        }

        // update module count for screen
        $db->query("UPDATE screen SET count_module = count_module + 1 WHERE id = " . $screen['id'] . "");

        // add reference to module
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'module' => $id,
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        );
        $id = $db->insert('module', $data);
        $data['id'] = $id;
        $data['result'] = $result;
        $data['name'] = $name;
        $data['skin'] = $skin;

        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case API_MODULE_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a module id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => $x,
            'y' => $y
        );
        $db->update('module', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_MODULE_RESIZE:
        $id = intval($route[4]);
        $width = intval($route[5]);
        $height = intval($route[6]);
        if ($id < 1) { die('Please provide a module id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'width' => $width,
            'height' => $height
        );
        $db->update('module', $data, array('id' => $id, 'creator' => userid()));
        break;

    case API_MODULE_RENAME:
        $id = intval($route[4]);
        $name = $route[5];
        $skin = $route[6];
        if ($id < 1) { die('Please provide a module id'); }
        $existing = $db->single('
            SELECT m.module, count(m.id) as count
            FROM module m, module m2
            WHERE m.id = "' . $id . '" AND m.module = m2.module'
        );
        $project = $db->single('SELECT project FROM project_module WHERE id = ' . $existing['module'] . ' AND creator = ' . userid());
        $result = 'EXISTING';
        $count = $db->single('
            SELECT id
            FROM project_module
            WHERE project = ' . $project['project'] . ' AND name = "' . $name . '" AND skin = "' . $skin . '"
        ');
        if (!$count) {
            $result = 'NEW';
        }
        if($existing['count'] < 2) {
            // just rename the project module
            $remove = true;
            $data = array(
                'modified' => date('Y-m-d H:i:s'),
                'modifier' => userid(),
                'name' => $name,
                'skin' => $skin
            );
            $moduleId = $existing['module'];
            $db->update('project_module', $data, array('id' => $moduleId, 'creator' => userid()));
        }
        else {
            $remove = false;
            if($result == 'NEW') {
                // create new project module
                $entry = array(
                    'created' => date('Y-m-d H:i:s'),
                    'creator' => userid(),
                    'name' => $name,
                    'skin' => $skin,
                    'project' => $project['project']
                );
                $moduleId = $db->insert('project_module', $entry);
            }
            else {
                $moduleId = $existing['module'];
            }
            // set the new reference
            $data = array(
                'modified' => date('Y-m-d H:i:s'),
                'modifier' => userid(),
                'module' => $moduleId
            );
            $db->update('module', $data, array('id' => $id, 'creator' => userid()));
        }
        $data = array('result' => $result, 'remove' => $remove, 'id' => $moduleId, 'name' => $name);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
}