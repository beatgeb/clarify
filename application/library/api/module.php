<?php

lock();

// Module API
define('API_MODULE_ADD', 'module.add');
define('API_MODULE_GET', 'module.get');
define('API_MODULE_MOVE', 'module.move');
define('API_MODULE_RESIZE', 'module.resize');
define('API_MODULE_REMOVE', 'module.remove');
define('API_MODULE_RENAME', 'module.rename');
define('API_MODULE_RECAPTURE', 'module.recapture');

switch ($action) {
    case API_MODULE_REMOVE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a module id'); }
        $module = $db->single("
            SELECT m.screen, m.module, s.project, pm.id, s.ext
            FROM module m
                LEFT JOIN project_module pm ON pm.id = m.module
                LEFT JOIN screen s ON s.id = m.screen
            WHERE m.id = '" . $id . "'
            LIMIT 1
        ");
        if (!$module) { die(); }
        permission($module['project'], 'EDIT');
        $result = array();
        $db->delete('module', array('id' => $id));
        $count = $db->exists('module', array('module' => $module['module']));
        if ($count < 1) {
            $path =  'upload/modules/'.$module['project'].'/'.md5($module['id'].config('security.general.hash')).'.png';
            unlink(TERRIFIC . $path);

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
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'VIEW');
        $data = $db->data("SELECT m.id, m.module, m.x, m.y, m.width, m.height, pm.name, pm.skin FROM module m LEFT JOIN project_module pm ON pm.id = m.module WHERE m.screen = " . $screen['id']);
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

        // check permissions
        $screen = $db->single("SELECT id, project, ext FROM screen WHERE id = '" . $screen . "'");
        if (!$screen) { die(); }
        //permission($screen['project'], 'EDIT');

        // explicitly use a library module
        if ($module > 0) {
            $refmodule = $db->single("SELECT * FROM project_module WHERE id = '" . $module . "' LIMIT 1");
            $name = $refmodule['name'];
            $skin = $refmodule['skin'];
        } else {
            $name = $route[9];
            $skin = '';
        }

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

        // crop module thumbnail
        require LIBRARY . 'image.php';
        $path =  'upload/modules/'.$screen['project'].'/'.md5($id.config('security.general.hash')).'.png';
        cropScreen($screen['id'], array( 'x' => $x, 'y' => $y, 'width' => $width, 'height' => $height), array('width' => 150, 'height' => 120), $path);
        $thumbnail = R .$path;

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

        // add to activity stream
        activity_add(
            '{actor} defined module {object} on screen {target}', 
            userid(), OBJECT_TYPE_USER, user('name'), 
            ACTIVITY_VERB_DEFINE, 
            $id, OBJECT_TYPE_MODULE, $name, 
            $screen['id'], OBJECT_TYPE_SCREEN, 'Screen Title'
        );

        $data['id'] = $id;
        $data['result'] = $result;
        $data['name'] = $name;
        $data['skin'] = $skin;
        $data['thumbnail'] = $thumbnail;

        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case API_MODULE_RECAPTURE:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        $width = intval($route[7]);
        $height = intval($route[8]);
        $module = intval($route[9]);
        if ($screen < 1) { die('Please provide a screen id'); }

        // crop module thumbnail
        $screen = $db->single("SELECT id, project, ext FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'EDIT');

        require LIBRARY . 'image.php';
        $path =  'upload/modules/'.$screen['project'].'/'.md5($module.config('security.general.hash')).'.png';
        unlink(TERRIFIC . $path);
        cropScreen($screen['id'], array( 'x' => $x, 'y' => $y, 'width' => $width, 'height' => $height), array('width' => 150, 'height' => 120), $path);
        $thumbnail = R .$path;

        $data = array();
        $data['id'] = $module;
        $data['thumbnail'] = $thumbnail;

        header('Content-Type: application/json');
        echo json_encode($data);
        break;

        break;
    case API_MODULE_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a module id'); }

        $module = $db->single("
            SELECT m.screen, m.module, s.project, pm.id, s.ext
            FROM module m
                LEFT JOIN project_module pm ON pm.id = m.module
                LEFT JOIN screen s ON s.id = m.screen
            WHERE m.id = '" . $id . "'
            LIMIT 1
        ");
        if (!$module) { die(); }
        permission($module['project'], 'EDIT');
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

        $module = $db->single("
            SELECT m.screen, m.module, s.project, pm.id, s.ext
            FROM module m
                LEFT JOIN project_module pm ON pm.id = m.module
                LEFT JOIN screen s ON s.id = m.screen
            WHERE m.id = '" . $id . "'
            LIMIT 1
        ");
        if (!$module) { die(); }
        permission($module['project'], 'EDIT');

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
        $name = $_REQUEST['name'];
        $skin = null;
        if ($id < 1) { die('Please provide a module id'); }

        $module = $db->single("
            SELECT m.screen, m.module, s.project, pm.id, s.ext
            FROM module m
                LEFT JOIN project_module pm ON pm.id = m.module
                LEFT JOIN screen s ON s.id = m.screen
            WHERE m.id = '" . $id . "'
            LIMIT 1
        ");
        if (!$module) { die(); }
        permission($module['project'], 'EDIT');
        
        //  rename the project module
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'name' => $db->escape($name),
            'skin' => $skin
        );
        $db->update('project_module', $data, array('id' => $id, 'creator' => userid()));

        $data = array('id' => $id, 'name' => $name, 'skin' => $skin);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
}