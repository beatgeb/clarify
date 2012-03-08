<?php

/**
 * Clarify.
 * 
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

$action = $_REQUEST['action'];

// TODO: Split API actions into different classes.
//       Actions are always structured with a dot.

/*
$action_data = explode('.', $action);
if (sizeof($action_data) > 0) {
    require LIBRARY . 'api/' . strtolower($action_data[0]) . '.php';
}
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
switch ($action) {
    
    case API_PROJECT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $db->escape($_REQUEST['name'])
        );
        $id = $db->insert('project', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_COMMENT_ADD:
        $max = $db->single("SELECT MAX(nr) as current FROM comment WHERE screen = '" . $screen . "'");
        $nr = $max['current'] + 1;
        $comment = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'layer' => $layer,
            'nr' => intval($nr),
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y'])
        );
        $id = $db->insert('comment', $comment);
        $comment['id'] = $id;
        echo json_encode($comment);
        break;
    
    case API_COMMENT_REMOVE:
        $db->delete('comment', array('id' => $id));
        break;
    
    case API_COMMENT_MOVE:
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y'])
        );
        $db->update('comment', $data, array('id' => $id));
        break;
    
    case API_COMMENT_CLEAR:
        $db->delete('comment', array('screen' => $screen));
        break;
    
    case API_COMMENT_RESIZE:
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'w' => intval($_REQUEST['w']),
            'h' => intval($_REQUEST['h'])
        );
        $db->update('comment', $data, array('id' => $id));
        break;
    
    case API_COMMENT_UPDATE:
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'content' => $db->escape($_REQUEST['content']),
            'w' => intval($_REQUEST['w']),
            'h' => intval($_REQUEST['h'])
        );
        $db->update('comment', $data, array('id' => $id));
        break;
    
    case API_COMMENT_GET:
        if ($screen < 1) { die('Please provide a screen id'); }
        if ($layer < 1) { die('Please provide a layer id'); }
        $data = $db->data("SELECT id, created, creator, nr, x, y, w, h, content FROM comment WHERE screen = " . $screen . " AND layer = " . $layer . "");
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
    case API_SCREEN_DELETE:
        // TODO: load colors referenced by this screen and delete
        //       color form library if it doesn't exist on another
        //       screen
        $db->delete('color', array('screen' => $screen));
        $db->delete('comment', array('screen' => $screen));
        $db->delete('measure', array('screen' => $screen));
        $db->delete('screen', array('id' => $screen));
        break;
    
    case API_SCREEN_UPLOAD:
        require LIBRARY . 'upload.php';
        $upload_dir = APP . '/../public/upload/screens/' . $project . '/';
        $upload_dir_thumbs = APP . '/../public/upload/screens/' . $project . '/thumbnails';
        @mkdir($upload_dir_thumbs, 0777, true);
        $options = array(
            'script_url' => R . '?view=api&action=screen.upload',
            'upload_dir' => $upload_dir,
            'upload_url' => R . 'upload/screens/' . $project . '/',
            'image_versions' => array(
                'thumbnail' => array(
                    'upload_dir' => $upload_dir . 'thumbnails/',
                    'upload_url' => R . 'upload/screens/' . $project . '/thumbnails/',
                    'max_width' => 200,
                    'max_height' => 1000
                 )
            )
        );
        
        $upload_handler = new UploadHandler($options);
        $upload_handler->project = $project;

        header('Pragma: no-cache');
        header('Cache-Control: private, no-cache');
        header('Content-Disposition: inline; filename="files.json"');
        header('X-Content-Type-Options: nosniff');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: X-File-Name, X-File-Type, X-File-Size');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'OPTIONS':
                break;
            case 'HEAD':
            case 'GET':
                $upload_handler->get();
                break;
            case 'POST':
                $upload_handler->post();
                break;
            case 'DELETE':
                $upload_handler->delete();
                break;
            default:
                header('HTTP/1.1 405 Method Not Allowed');
        }
        break;
    
    case API_COLOR_REMOVE:
        $color = $db->single("
            SELECT c.color, s.project, pc.id 
            FROM color c 
                LEFT JOIN project_color pc ON pc.id = c.color 
                LEFT JOIN screen s ON s.id = c.screen 
            WHERE c.id = '" . $id . "' 
            LIMIT 1
        ");
        $result = array();
        $db->delete('color', array('id' => $id));
        $count = $db->exists('color', array('color' => $color['color']));
        if ($count < 1) {
            $db->delete('project_color', array('id' => $color['id']));
            $result['remove'] = $color['id'];
        }
        header('Content-Type: application/json');
        echo json_encode($result);
        break;
    
    case API_COLOR_GET:
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT c.id, c.x, c.y, pc.r, pc.g, pc.b, pc.alpha, pc.hex FROM color c LEFT JOIN project_color pc ON pc.id = c.color WHERE screen = " . $screen);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
    case API_COLOR_ADD:
        
        // explicitly use a library color
        if (isset($_REQUEST['color'])) {
            $color = $db->single("SELECT * FROM project_color WHERE id = '" . intval($_REQUEST['color']) . "' LIMIT 1");
            $r = $color['r'];
            $g = $color['g'];
            $b = $color['b'];
            $a = $color['alpha'];
            $hex = $color['hex'];
        } else {
            $r = intval($_REQUEST['r']);
            $g = intval($_REQUEST['g']);
            $b = intval($_REQUEST['b']);
            $a = intval($_REQUEST['a']);
            $hex = substr($_REQUEST['hex'],1,6);
        }
        
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $screen['project'],
            'r' => $r,
            'g' => $g,
            'b' => $b,
            'alpha' => $a,
            'hex' => $db->escape($hex)
        );
        $result = 'EXISTING';
        $existing = $db->single('
            SELECT id 
            FROM project_color 
            WHERE project = ' . $screen['project'] . ' AND r = ' . $r . ' AND g = ' . $g . ' AND b = ' . $b . ' AND alpha = ' . $a . '
        ');
        $id = $existing['id'];
        if (!$existing) {
            $id = $db->insert('project_color', $data);
            $result = 'NEW';
        }
        
        // add reference to color
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'color' => $id,
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y'])
        );
        $id = $db->insert('color', $data);
        $data['id'] = $id;
        $data['r'] = $r;
        $data['g'] = $g;
        $data['b'] = $b;
        $data['hex'] = $hex;
        $data['alpha'] = $a;
        $data['result'] = $result;
        
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
    case API_MEASURE_ADD:
        $measure = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y']),
            'width' => intval($_REQUEST['width']),
            'height' => intval($_REQUEST['height'])
        );
        $id = $db->insert('measure', $measure);
        $measure['id'] = $id;
        header('Content-Type: application/json');
        echo json_encode($measure);
        break;
        
    case API_MEASURE_GET:
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT id, x, y, width, height FROM measure WHERE screen = '" . $screen . "'");
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
        
    case API_MEASURE_MOVE:
        $id = intval($_REQUEST['id']);
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => intval($_REQUEST['x']),
            'y' => intval($_REQUEST['y'])
        );
        $db->update('measure', $data, array('id' => $id));
        break;
    
    case API_MEASURE_RESIZE:
        $id = intval($_REQUEST['id']);
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'width' => intval($_REQUEST['width']),
            'height' => intval($_REQUEST['height'])
        );
        $db->update('measure', $data, array('id' => $id));
        break;
    
    case API_MEASURE_DELETE:
        $id = intval($_REQUEST['id']);
        $db->delete('measure', array('id' => $id));
        echo json_encode(array('RESULT' => 'OK'));
        break;
    
    case API_SCREEN_THUMBNAIL:
        $key = md5($screen . '-' . intval($_REQUEST['width']));
        $screen = $db->single("SELECT id, project, type, ext FROM screen WHERE id = '" . $screen . "' LIMIT 1");
        $filename =  UPLOAD . 'screens/' . $screen['project'] . '/' . $screen['id'] . '.' . $screen['ext'];
        $target =  CACHE . 'screens/' . $screen['project'] . '/' . $screen['id'] . '/' . $key;
        if (is_file($target)) {
            header('Content-Type: image/png');
            echo file_get_contents($target);
        } else {
            if (!is_dir(dirname($target))) {
                @mkdir(dirname($target), 0777, true);
            }
            $w = intval($_REQUEST['width']);
            list($width, $height) = getimagesize($filename);
            $r = $width / $height;
            $newheight = $w / $r;
            $newwidth = $w;
            switch ($screen['type']) {
                case 'image/jpeg':
                case 'image/jpg':
                    $src = imagecreatefromjpeg($filename);
                    break;
                case 'image/png':
                    $src = imagecreatefrompng($filename);
                    break;
            }
            $dst = imagecreatetruecolor($newwidth, $newheight);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
            imagepng($dst, $target);
            header('Content-Type: image/png');
            echo file_get_contents($target);
        }
        break;
    
    case API_SCREEN_IMAGE:
        
        $version = 1;
        $key = md5($screen . '-' . $layer . '-' . $version);
        $screen = $db->single("SELECT * FROM screen WHERE id = '" . $screen . "' LIMIT 1");
        $filename =  UPLOAD . 'screens/' . $screen['project'] . '/' . $screen['id'] . '.' . $screen['ext'];
        $target =  UPLOAD . 'screens/' . $screen['project'] . '/' . $screen['id'] . '/' . $key . '.png';
        if (!is_dir(dirname($target))) {
            @mkdir(dirname($target), 0777, true);
        }
        
        // Get new dimensions
        $factor = intval($_REQUEST['width']) / $screen['width'];
        
        // Load comments for this screen and layer
        $comments = $db->data("SELECT x, y, nr FROM comment WHERE screen = '" . $screen['id'] . "'");
        
        $image = new Imagick($filename);
        $image->thumbnailImage($_REQUEST['width'], 0);
        
        // Draw comments
        $draw = new ImagickDraw(); 
        $draw->setFont('Nimbus-Sans-Bold');
        if ($_REQUEST['width'] <= 300) {
            $draw->setFontSize(9);
            $radius = 5;
        } else if ($_REQUEST['width'] < 800) {
            $draw->setFontSize(12);
            $radius = 7;
            $offset = 1;
        } else {
            $draw->setFontSize(14);
            $radius = 10;
            $offset = 2;
        }
        
        foreach ($comments as $comment) {
            $draw->setFillColor('black');
            $x = $comment['x'] * $factor;
            $y = $comment['y'] * $factor;
            $draw->circle(
                $x + $radius, 
                $y + $radius, 
                $x + $radius*2, 
                $y + $radius*2
            ); 
            $draw->setFillColor('white');
            $draw->setTextAlignment(2);
            $draw->annotation($x + $radius, $y + $radius + $radius/2 +1, $comment['nr']);
        }
        $image->drawImage($draw);
        
        // Output
        header('Content-Type: image/png');
        echo $image;
        break;
        
    case API_LIBRARY_BEHAVIOUR_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $_REQUEST['name'],
            'vendor' => $_REQUEST['vendor'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_behaviour', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_LIBRARY_BEHAVIOUR_EVENT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'behaviour' => $_REQUEST['behaviour'],
            'name' => $_REQUEST['name'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_behaviour_event', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_LIBRARY_BEHAVIOUR_OPTION_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $_REQUEST['name'],
            'behaviour' => $_REQUEST['behaviour'],
            'value_type' => $_REQUEST['type'],
            'value_default' => $_REQUEST['default'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_behaviour_option', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
    case API_LIBRARY_COMPONENT_ADD:
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'name' => $_REQUEST['name'],
            'vendor' => $_REQUEST['vendor'],
            'description' => $_REQUEST['description']
        );
        $id = $db->insert('library_component', $data);
        $data['id'] = $id;
        echo json_encode($data);
        break;
    
}

?>