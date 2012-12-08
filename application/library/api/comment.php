<?php

lock();

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
        if ($max === null) { die(); }
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
        $db->query("UPDATE screen SET count_comment = count_comment + 1 WHERE id = " . $screen . "");
        $comment['id'] = $id;

        // add to activity stream
        activity_add(
            '{actor} left a comment on screen {target}', 
            userid(), OBJECT_TYPE_USER, user('name'), 
            ACTIVITY_VERB_COMMENT, 
            $id, OBJECT_TYPE_COMMENT, "", 
            $screen, OBJECT_TYPE_SCREEN, 'Title'
        );

        echo json_encode($comment);
        break;
    
    case API_COMMENT_REMOVE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a comment id'); }
        $comment = $db->single('SELECT screen FROM comment WHERE id = ' . $id . ' AND creator = ' . userid());
        if (!$comment) { die(); }
        $db->delete('comment', array('id' => $id));
        $db->query("UPDATE screen SET count_comment = count_comment - 1 WHERE id = " . $comment['screen'] . "");
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
        $db->update('comment', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_COMMENT_CLEAR:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $db->delete('comment', array('screen' => $screen, 'creator' => userid()));
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
        $db->update('comment', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_COMMENT_UPDATE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a comment id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'content' => addslashes(strip_tags(stripslashes($_REQUEST['content'])))
        );
        $db->update('comment', $data, array('id' => $id, 'creator' => userid()));
        $data = $db->single("SELECT id, creator, nr, content FROM comment WHERE id = " . $id);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
    case API_COMMENT_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'VIEW');
        $query = "SELECT id, creator, nr, x, y, w, h, content FROM comment WHERE screen = " . $screen['id'];
        $data = $db->data($query);
        if($data) $data[0]['content'] = stripslashes($data[0]['content']);
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
}