<?php

lock();

// Project API
define('API_COLLABORATOR_SEARCH', 'collaborator.search');
define('API_COLLABORATOR_ADD', 'collaborator.add');
define('API_COLLABORATOR_DELETE', 'collaborator.delete');

switch ($action) {
/*
    case API_COLLABORATOR_SEARCH:
        $result = $db->data("
            SELECT
              id,
              email
            FROM
              user
            WHERE
              email LIKE '%" . $_REQUEST['q'] . "%'
        ");

        echo json_encode($result);
        break;
*/
    case API_COLLABORATOR_ADD:
        if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
            $data = array(
                'created' => date('Y-m-d H:i:s'),
                'creator' => userid(),
                'email' => $db->escape($_REQUEST['email']),
                'project' => $db->escape($_REQUEST['project']),
            );
            $id = $db->insert('project_collaborator', $data);
            $data['id'] = $id;
            echo json_encode($data);
            break;
        } else {
            //header('The goggles, they do nawtink!', true, 400);
            echo 'Please enter a valid e-mail address. You know, with @ and all that stuff...';
            break;
        }

    case API_COLLABORATOR_DELETE:
        // TODO: all
        break;

}