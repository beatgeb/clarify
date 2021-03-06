<?php

// Screen API
define('API_SCREEN_DELETE', 'screen.delete');
define('API_SCREEN_UPLOAD', 'screen.upload');
define('API_SCREEN_IMAGE', 'screen.image');
define('API_SCREEN_THUMBNAIL', 'screen.thumbnail');
define('API_SCREEN_EMBED', 'screen.embed');
define('API_SCREEN_SETTING', 'screen.setting');
define('API_SCREEN_REPLACE', 'screen.replace');
define('API_SCREEN_LIST', 'screen.list');

switch ($action) {

    case API_SCREEN_LIST:
        lock();
        $project_id = intval($route[4]);
        permission($project_id, 'VIEW');
        $screens = $db->data('
            SELECT 
                id, 
                project, 
                title, 
                width, 
                height, 
                ext, 
                created, 
                count_comment, 
                count_measure, 
                count_color, 
                count_font, 
                count_module
            FROM screen 
            WHERE project = ' . $project_id . ' 
            ORDER BY title ASC
        ');
        while(list($key, $screen) = each($screens)) {
            $screens[$key]['image_url_thumbnail'] = config('application.domain') . R . 'upload/screens/' . $project_id . '/thumbnails/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
            $screens[$key]['image_url'] = config('application.domain') . R . 'upload/screens/' . $project_id . '/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
        }
        header('Content-Type: application/json');
        echo json_encode(array('success' => true, 'screens' => $screens));
        break;
    
    case API_SCREEN_SETTING:
        lock();
        $screen = intval($route[4]);

        $screen = $db->single("SELECT id, project FROM screen WHERE id = " . $screen . "");
        if (!$screen) { die(); }

        // check permissions
        permission($screen['project'], 'EDIT');

        $setting = $route[5];
        switch ($setting) {
            case 'embeddable':
                $value = intval($route[6]);
                if ($value == 'true') {
                    $db->update('screen', array('embeddable' => 'TRUE'), array('id' => $screen['id']));
                } else {
                    $db->update('screen', array('embeddable' => 'FALSE'), array('id' => $screen['id']));
                }
                break;
            case 'title':
                $value = $route[6];
                $db->update('screen', array('title' => urldecode($value)), array('id' => $screen['id']));
                break;
        }
        break;
    
    case API_SCREEN_DELETE:
        lock();
        $screen = intval($route[4]);
        $screen = $db->single("SELECT id, project FROM screen WHERE id = " . $screen . "");
        if (!$screen) { die(); }

        // check permissions
        permission($screen['project'], 'EDIT');

        // TODO: load colors referenced by this screen and delete
        //       color form library if it doesn't exist on another
        //       screen
        $db->delete('color', array('screen' => $screen['id']));
        $db->delete('comment', array('screen' => $screen['id']));
        $db->delete('measure', array('screen' => $screen['id']));

        // check if screen is part of a set
        $relations = $db->data("SELECT `set` FROM set_screen WHERE screen = " . $screen['id']);
        foreach ($relations as $relation) {
            $db->query("UPDATE `set` SET screen_count = screen_count - 1 WHERE id = " . $relation['set'] . " LIMIT 1");
        }
        $db->delete('set_screen', array('screen' => $screen['id']));

        // delete screen & update screen count on project
        $db->delete('screen', array('id' => $screen['id']));
        $db->query("UPDATE project SET screen_count = screen_count - 1 WHERE id = " . $screen['project']);
        $response = $screen;
        json($response);
        break;

    case API_SCREEN_REPLACE: 
        lock();
        $screen = intval($route[4]);
        $screen = $db->single("SELECT id, project FROM screen WHERE id = " . $screen . "");
        if (!$screen) { die(); }

        // check permissions
        $project = $screen['project'];
        permission($screen['project'], 'EDIT');

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
            ),
            'param_name' => 'files_replace'
        );
        
        $upload_handler = new UploadHandler($options);
        $upload_handler->project = $project;
        $upload_handler->replace = true;
        $upload_handler->screen = $screen['id'];

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
    
    case API_SCREEN_UPLOAD:
        lock();
        // TODO: CLEANUP UPLOAD CODE, make it ourself, no third-party library
        $project = intval($route[4]);
        $set = null;
        if (isset($route[5])) {
            $set = intval($route[5]);
        }
        
        // check permission
        permission($project, 'EDIT');

        // prepare upload
        $p = $db->single("SELECT id FROM project WHERE id = " . $project);
        if (!$p) { die(); }
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
        $upload_handler->set = $set;

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
            
    case API_SCREEN_THUMBNAIL:
        $screen = intval($route[4]);
        $reqwidth = intval($route[5]);
        $version = intval($route[6]);
        $key = md5($screen . '-' . $reqwidth . '-' . $version);
        $folder = substr($key, 0, 3);
        $target =  CACHE . 'screens/' . $folder . '/' . $screen['id'] . '/' . $key;
        if (is_file($target) && isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && $_SERVER['HTTP_IF_MODIFIED_SINCE'] == date('r', filemtime($target))) {
            header('Last-modified: ' . date(DATERFC_1123, filemtime($target)), true, 304);
            exit;
        }

        $screen = $db->single("SELECT id, project, created, modified, embeddable, type, ext FROM screen WHERE id = '" . $screen . "' LIMIT 1");
        if (!$screen) { die(); }

        // check project permissions
        if (!$screen['embeddable'] && !has_permission($screen['project'], 'VIEW')) {
            die();
        }

        $modified = !$screen['modified'] ? $screen['created'] : $screen['modified'];
        $filename =  UPLOAD . 'screens/' . $screen['project'] . '/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
        
        header('Content-Type: image/png');
        header('Cache-Control: private');
        header('Expires: ' . date('r', strtotime('+1 year')));
        header('Pragma: public');
        
        if (is_file($target)) {
            header('Last-modified: ' . date('r', filemtime($target)));
            readfile($target);
        } else {
            header('Last-modified: ' . date('r'));
            if (!is_dir(dirname($target))) {
                @mkdir(dirname($target), 0777, true);
            }
            $w = $reqwidth;
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
            echo file_get_contents($target);
        }
        break;
    
    case API_SCREEN_IMAGE:
        lock();
        $screen = intval($route[4]);
        $reqwidth = intval($route[5]);
        $version = 1;
        $key = md5($screen . '-' . $reqwidth . '-' . $version);
        $screen = $db->single("SELECT * FROM screen WHERE id = '" . $screen . "' AND embeddable = 'TRUE' LIMIT 1");
        if (!$screen) { die(); }
        $filename =  UPLOAD . 'screens/' . $screen['project'] . '/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
        $target =  UPLOAD . 'screens/' . $screen['project'] . '/' . md5($screen['id'] . config('security.general.hash')) . '/' . $key . '.png';
        if (!is_dir(dirname($target))) {
            @mkdir(dirname($target), 0777, true);
        }
        
        // Get new dimensions
        $factor = $reqwidth / $screen['width'];
        
        // Load comments for this screen and layer
        $comments = $db->data("SELECT x, y, nr FROM comment WHERE screen = '" . $screen['id'] . "'");
        
        $image = new Imagick($filename);
        $image->thumbnailImage($reqwidth, 0);
        
        // Draw comments
        $draw = new ImagickDraw(); 
        $draw->setFont('Nimbus-Sans-Bold');
        if ($reqwidth <= 300) {
            $draw->setFontSize(9);
            $radius = 5;
        } else if ($reqwidth < 800) {
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
    
}