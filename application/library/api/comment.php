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
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'EDIT');
        $max = $db->single("SELECT MAX(nr) as current FROM comment WHERE screen = '" . $screen['id'] . "'");
        if ($max === null) { die(); }
        $nr = $max['current'] + 1;
        $comment = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'nr' => intval($nr),
            'x' => $x,
            'y' => $y
        );
        $id = $db->insert('comment', $comment);
        $db->query("UPDATE screen SET count_comment = count_comment + 1 WHERE id = " . $screen['id'] . "");
        $comment['creator_name'] = user('name');
        $comment['id'] = $id;

        // add to activity stream
        activity_add(
            '{actor} left a comment on screen {target}', 
            userid(), OBJECT_TYPE_USER, user('name'), 
            ACTIVITY_VERB_COMMENT, 
            $id, OBJECT_TYPE_COMMENT, "", 
            $screen['id'], OBJECT_TYPE_SCREEN, 'Title'
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
        $screen = $db->single("SELECT s.project FROM comment c LEFT JOIN screen s ON s.id = c.screen WHERE c.id = '" . $id . "'");
        permission($screen['project'], 'VIEW');
        $db->update('comment', $data, array('id' => $id));
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
            'content' => strip_tags(stripslashes($_REQUEST['content']))
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
        $data = $db->data("
            SELECT 
                c.id, 
                c.creator, 
                c.nr, 
                c.x, 
                c.y, 
                c.w, 
                c.h, 
                c.content, 
                c.creator, 
                u.name as creator_name,
                u.email as creator_email
            FROM comment c 
                LEFT JOIN user u ON u.id = c.creator
            WHERE c.screen = " . $screen['id']
        );
        foreach($data as $key => $comment) {
            $data[$key]['content'] = $comment['content'];
            $data[$key]['creator_image'] = gravatar($comment['creator_email'], null);
            unset($data[$key]['creator_email']);
        }
        json($data);
        break;
    
}