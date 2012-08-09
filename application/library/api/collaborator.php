<?php

lock();

// Collaborator API
define('API_COLLABORATOR_ADD', 'collaborator.add');
define('API_COLLABORATOR_REMOVE', 'collaborator.remove');

switch ($action) {

    case API_COLLABORATOR_ADD:
        if (filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL)) {
            $project_id = intval($_REQUEST['project']);
            $data = array(
                'created' => date('Y-m-d H:i:s'),
                'creator' => userid(),
                'email' => $db->escape($_REQUEST['email']),
                'project' => $project_id,
            );
            $id = $db->insert('project_collaborator', $data);

            // read project details
            $project = $db->single("SELECT name FROM project WHERE id = '" . $project_id . "' AND creator = '" . userid() . "' LIMIT 1");

            // send e-mail invitation to collaborator
            $email = new Mail_Postmark();
            $email->addTo('roger.dudler@gmail.com', 'Roger Dudler')
                ->subject('Roger invites you to join the "' . $project['name'] . '" project!')
                ->messagePlain('Click here')
                ->send();

            $data['id'] = $id;
            echo json_encode($data);
            break;
        } else {
            header('The goggles, they do nawtink!', true, 400);
            echo 'Please enter a valid e-mail address. You know, with @ and all that stuff...';
            break;
        }

    case API_COLLABORATOR_REMOVE:
        $id = intval($route[4]);
        $db->query("DELETE FROM project_collaborator WHERE id = '" . $id . "' AND creator = '" . userid() . "'");
        header('Content-Type: application/json');
        $data = array();
        echo json_encode($data);
        break;

}