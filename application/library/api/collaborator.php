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
            $project = $db->single("SELECT id, slug, name FROM project WHERE id = '" . $project_id . "' AND creator = '" . userid() . "' LIMIT 1");

            // check if user with this email already exists
            $user = $db->single("SELECT id FROM user WHERE email = '" . $data['email'] . "' LIMIT 1");
            if (!$user) {
                $data = array(
                    'created' => date('Y-m-d H:i:s'),
                    'creator' => userid(),
                    'email' => $db->escape($_REQUEST['email'])
                );
                $user_id = $db->insert('user', $data);
                $invitation_code = $user_id + 110000;
                $db->update('user', array('invitation_code' => $invitation_code), array('id' => $user_id));

                $link = config('application.domain') . config('application.baseurl') . 'auth/' . $invitation_code;
                $text = "I invite you to collaborate on the " . $project['name'] . " project.\n\nClick on the following link to create an account for Clarify and open the project\n" . $link . "\n\nCheers\n" . user('name') . " & the Clarify team";
                
            } else {
                $link = config('application.domain') . config('application.baseurl') . 'project/' . userid() . '/' . $project['slug'];
                $text = "I invite you to collaborate on the " . $project['name'] . " project.\n\nClick on the following link to open the project\n" . $link . "\n\nCheers\n" . user('name') . " & the Clarify team";
                $user_id = $user['id'];
            }

            // add permission for this project
            $permission = array(
                'created' => date('Y-m-d H:i:s'),
                'creator' => userid(),
                'project' => $project_id,
                'user' => $user_id,
                'permission' => 'EDIT'
            );
            $db->insert('project_permission', $permission);

            // send e-mail invitation to collaborator
            $email = new Mail_Postmark();
            $email->addTo($_REQUEST['email'])
                ->subject(user('name') . ' invites you to collaborate on the "' . $project['name'] . '" project')
                ->messagePlain($text)
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
        $collaborator = $db->single("SELECT project, email FROM project_collaborator WHERE id = '" . $id . "' LIMIT 1");
        $db->query("DELETE FROM project_collaborator WHERE id = '" . $id . "' AND creator = '" . userid() . "' LIMIT 1");
        // check if collaborator is an existing user
        $user = $db->single("SELECT id FROM user WHERE email = '" . $collaborator['email'] . "' LIMIT 1");
        if ($user) {
            $db->query("DELETE FROM project_permission WHERE project = '" . $collaborator['project'] . "' AND user = '" . $user['id'] . "' LIMIT 1");
        }
        header('Content-Type: application/json');
        $data = array();
        echo json_encode($data);
        break;

}