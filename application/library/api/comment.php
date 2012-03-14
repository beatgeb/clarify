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
        $data = $db->data("SELECT id, creator, nr, x, y, w, h, content FROM comment WHERE screen = " . $screen . " AND layer = " . $layer . "");
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
}