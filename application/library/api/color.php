<?php

lock();

// Color API
define('API_COLOR_ADD', 'color.add');
define('API_COLOR_GET', 'color.get');
define('API_COLOR_REMOVE', 'color.remove');
define('API_COLOR_EXPORT', 'color.export');
define('API_COLOR_UPDATE', 'color.update');

switch ($action) {
    
    case API_COLOR_UPDATE:

        require LIBRARY . 'color.php';

        $id = intval($route[4]);
        $hex = substr($route[5],0,6);
        $name_css = $route[6];
        $name = urldecode($route[7]);

        // check permission
        $color = $db->single("SELECT s.project, c.color FROM color c LEFT JOIN screen s ON s.id = c.screen WHERE c.id = '" . $id . "'");
        permission($color['project'], 'EDIT');

        // build color values
        $colorHandler = new ColorHandler();
        $hsl = $colorHandler->HtmltoHsl("#" . $hex);
        $rgb = hexToRgbArray("#" . $hex);

        // update data
        $data = array(
            'name' => $name, 
            'name_css' => $name_css,
            'hex' => $hex, 
            'hue' => $hsl['h'] . "", 
            'saturation' => $hsl['s'] . "", 
            'lightness' => $hsl['l'] . "", 
            'r' => $rgb['r'] . "", 
            'g' => $rgb['g'] . "", 
            'b' => $rgb['b'] . ""
        );

        $db->update('project_color', $data, array('id' => $color['color']));
        $result = array('success' => true);
        header('Content-Type: application/json');
        echo json_encode($result);
        break;

    case API_COLOR_REMOVE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a color instance id'); }
        $color = $db->single("
            SELECT c.screen, c.color, s.project, pc.id 
            FROM color c 
                LEFT JOIN project_color pc ON pc.id = c.color 
                LEFT JOIN screen s ON s.id = c.screen 
            WHERE c.id = '" . $id . "'
            LIMIT 1
        ");
        if (!$color) { die(); }
        permission($color['project'], 'EDIT');

        $result = array();
        $db->delete('color', array('id' => $id));
        $count = $db->exists('color', array('color' => $color['color']));
        if ($count < 1) {
            $db->delete('project_color', array('id' => $color['id']));
            $result['remove'] = $color['id'];
        }
        $db->query("UPDATE screen SET count_color = count_color - 1 WHERE id = " . $color['screen'] . "");
        header('Content-Type: application/json');
        echo json_encode($result);
        break;
    
    case API_COLOR_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'VIEW');
        $data = $db->data("SELECT c.id, c.x, c.y, pc.r, pc.g, pc.b, pc.alpha, pc.hex, pc.name, pc.name_css FROM color c LEFT JOIN project_color pc ON pc.id = c.color WHERE c.screen = " . $screen['id']);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
    case API_COLOR_ADD:
        require LIBRARY . 'color.php';

        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($screen < 1) { die('Please provide a screen id'); }
        
        // check permission
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'EDIT');

        // explicitly use a library color
        if (sizeof($route) < 9) {
            $color = intval($route[7]);
            if ($color < 1) { die('Please provide a reference color'); }
            $color = $db->single("SELECT * FROM project_color WHERE id = '" . $color . "' LIMIT 1");
            $r = $color['r'];
            $g = $color['g'];
            $b = $color['b'];
            $a = $color['alpha'];
            $hex = $color['hex'];
        } else {
            $r = intval($route[7]);
            $g = intval($route[8]);
            $b = intval($route[9]);
            $a = intval($route[10]);
            $hex = substr($route[11],0,6);
        }

        $colorHandler = new ColorHandler();
        $hsl = $colorHandler->HtmltoHsl("#".$hex);
        $match = $colorHandler->getColorMatch("#".$hex);

        // handle if name with other hex-value already exists
        $sameColors = $db->single("
            SELECT count(id) AS number
            FROM project_color
            WHERE project = " . $screen['project'] ." AND hex <> '" . $hex . "' AND (name LIKE '" . $match[0] . "-%' OR name = '". $match[0] . "')
        ");
        $existing = $db->single('
            SELECT id, name
            FROM project_color
            WHERE project = ' . $screen['project'] . ' AND r = ' . $r . ' AND g = ' . $g . ' AND b = ' . $b . ' AND alpha = ' . $a . '
        ');
        if ($sameColors['number'] > 0 && !$existing) {
            $match[0] = $match[0]."-".$sameColors['number'];
        }
        else if ($existing) {
            $match[0] = $existing['name'];
        }

        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $screen['project'],
            'r' => $r,
            'g' => $g,
            'b' => $b,
            'alpha' => $a,
            'hex' => $db->escape($hex),
            'hue' => $hsl['h'],
            'saturation' => $hsl['s'],
            'lightness' => $hsl['l'],
            'name' => $match[0],
            'name_css' => slug($match[0])
        );
        $result = 'EXISTING';
        $id = $existing['id'];
        if (!$existing) {
            $id = $db->insert('project_color', $data);
            $result = 'NEW';
        }
        
        // update color count for screen
        $db->query("UPDATE screen SET count_color = count_color + 1 WHERE id = " . $screen['id'] . "");
        
        // add reference to color
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'color' => $id,
            'x' => $x,
            'y' => $y
        );
        $id = $db->insert('color', $data);
        $data['id'] = $id;
        $data['r'] = $r;
        $data['g'] = $g;
        $data['b'] = $b;
        $data['hex'] = $hex;
        $data['alpha'] = $a;
        $data['result'] = $result;
        $data['name'] = $match[0];
        $data['name_css'] = slug($match[0]);
        $data['match'] = $match[1];

        // add to activity stream
        activity_add(
            '{actor} picked {object} on screen {target}', 
            userid(), OBJECT_TYPE_USER, user('name'), 
            ACTIVITY_VERB_PICK, 
            $id, OBJECT_TYPE_COLOR, '#' . $hex, 
            $screen['id'], OBJECT_TYPE_SCREEN, 'Title'
        );

        header('Content-Type: application/json');
        echo json_encode($data);
        break;

    case API_COLOR_EXPORT:
        $project = intval($route[4]);
        $type = strtolower($route[5]);

        if ($project < 1) {
            die('Please provide a project id');
        }

        if (empty($type)) {
            die('Please provide a type to export to');
        }

        switch($type) {

            case 'aco':
                // get the aco-library
                require_once LIBRARY . 'thirdparty/aco/aco.class.php';

                // collect all the colors from the project including names
                $colors = $db->data("
                    SELECT
                        p.`name` project,
                        pc.`name`,
                        pc.r,
                        pc.g,
                        pc.b
                    FROM
                        project_color pc
                    LEFT JOIN
                        project p ON (p.id = pc.project)
                    WHERE
                        pc.project = '" . $project . "' AND pc.creator = '" . userid() . "'
                ");

                if (!$colors) {
                    die();
                }

                $aco = new acofile();

                // assign project-name as file-name
                $aco->acofile($colors[0]['project'] . '.aco');

                foreach($colors as $color) {
                    $aco->add($color['name'], $color['r'], $color['g'], $color['b']);
                }

                $aco->outputAcofile();

                break;
        }

        
}