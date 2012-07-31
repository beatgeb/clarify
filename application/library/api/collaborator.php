<?php

lock();

// Project API
define('API_COLLABORATOR_SEARCH', 'collaborator.search');
define('API_COLLABORATOR_ADD', 'collaborator.add');
define('API_COLLABORATOR_DELETE', 'collaborator.delete');

switch ($action) {

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

    case API_COLLABORATOR_ADD:
        if (preg_match('/^(?!(?>"?(?>[^"\\\]|\\\[ -~])"?){255,})(?!"?(?>[^"\\\]|\\\[ -~]){65,}"?@)(?>([!#-\'*+\/-9=?^-~-]+)(?>\.(?1))*|"(?>[ !#-\[\]-~]|\\\[ -~])*")@(?!.*[^.]{64,})(?>([a-z0-9](?>[a-z0-9-]*[a-z0-9])?)(?>\.(?2)){0,126}|\[(?:(?>IPv6:(?>([a-f0-9]{1,4})(?>:(?3)){7}|(?!(?:.*[a-f0-9][:\]]){8,})((?3)(?>:(?3)){0,6})?::(?4)?))|(?>(?>IPv6:(?>(?3)(?>:(?3)){5}:|(?!(?:.*[a-f0-9]:){6,})(?5)?::(?>((?3)(?>:(?3)){0,4}):)?))?(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?6)){3}))\])$/iD', $_REQUEST['email'])) {
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
            header('The goggles, they do nawtink!', true, 400);
            echo 'Please enter a valid e-mail address. You know, with @ and all that stuff...';
            break;
        }

    case API_COLLABORATOR_DELETE:
        // TODO: all
        break;

}