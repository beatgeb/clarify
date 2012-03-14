<?php

// Comment API
define('API_COMMENT_ADD', 'comment.add');
define('API_COMMENT_REMOVE', 'comment.remove');
define('API_COMMENT_MOVE', 'comment.move');
define('API_COMMENT_CLEAR', 'comment.clear');
define('API_COMMENT_RESIZE', 'comment.resize');
define('API_COMMENT_UPDATE', 'comment.update');
define('API_COMMENT_GET', 'comment.get');

switch ($action) {
    
    case API_COMMENT_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $max = $db->single("SELECT MAX(nr) as current FROM comment WHERE screen = '" . $screen . "'");
        $nr = $max['current'] + 1;
        $comment = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen,
            'nr' => intval($nr),
            'x' => $x,
            'y' => $y
        );
        $id = $db->insert('comment', $comment);
        $comment['id'] = $id;
        echo json_encode($comment);
        break;
    
    case API_COMMENT_REMOVE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a comment id'); }
        $db->delete('comment', array('id' => $id));
        break;
    
    case API_COMMENT_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a comment id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => $x,
            'y' => $y
        );
        $db->update('comment', $data, array('id' => $id));
        break;
    
    case API_COMMENT_CLEAR:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $db->delete('comment', array('screen' => $screen));
        break;
    
    case API_COMMENT_RESIZE:
        $id = intval($route[4]);
        $width = intval($route[5]);
        $height = intval($route[6]);
        if ($id < 1) { die('Please provide a comment id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'w' => $width,
            'h' => $height
        );
        $db->update('comment', $data, array('id' => $id));
        break;
    
    case API_COMMENT_UPDATE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a comment id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'content' => $db->escape($_REQUEST['content'])
        );
        $db->update('comment', $data, array('id' => $id));
        break;
    
    case API_COMMENT_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $data = $db->data("SELECT id, creator, nr, x, y, w, h, content FROM comment WHERE screen = " . $screen . "");
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
}