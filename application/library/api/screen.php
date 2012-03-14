<?php

// Screen API
define('API_SCREEN_DELETE', 'screen.delete');
define('API_SCREEN_UPLOAD', 'screen.upload');
define('API_SCREEN_IMAGE', 'screen.image');
define('API_SCREEN_THUMBNAIL', 'screen.thumbnail');
define('API_SCREEN_EMBED', 'screen.embed');

switch ($action) {
    
    case API_SCREEN_DELETE:
        $screen = intval($route[4]);
        // TODO: load colors referenced by this screen and delete
        //       color form library if it doesn't exist on another
        //       screen
        $db->delete('color', array('screen' => $screen));
        $db->delete('comment', array('screen' => $screen));
        $db->delete('measure', array('screen' => $screen));
        $db->delete('screen', array('id' => $screen));
        break;
    
    case API_SCREEN_UPLOAD:
        $project = intval($route[4]);
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
            
    case API_SCREEN_THUMBNAIL:
        $screen = intval($route[4]);
        $reqwidth = intval($route[5]);
        $key = md5($screen . '-' . $reqwidth);
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
            header('Content-Type: image/png');
            echo file_get_contents($target);
        }
        break;
    
    case API_SCREEN_IMAGE:
        $screen = intval($route[4]);
        $reqwidth = intval($route[5]);
        $version = 1;
        $key = md5($screen . '-' . $reqwidth . '-' . $version);
        $screen = $db->single("SELECT * FROM screen WHERE id = '" . $screen . "' LIMIT 1");
        $filename =  UPLOAD . 'screens/' . $screen['project'] . '/' . $screen['id'] . '.' . $screen['ext'];
        $target =  UPLOAD . 'screens/' . $screen['project'] . '/' . $screen['id'] . '/' . $key . '.png';
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