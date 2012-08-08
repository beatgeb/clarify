<?php

lock();

require_once LIBRARY . 'thirdparty/aco/aco.class.php';

// Export API
define('API_EXPORT_ACO', 'export.aco');

switch ($action) {

    case API_EXPORT_ACO:

        $id = intval($route[4]);

        if ($id < 1) {
            die('Please provide a project id');
        }

        $colors = $db->data("
            SELECT
                p.`name` project,
                pc.`name`,
                pc.r,
                pc.g,
                pc.b
            FROM
                project_color pc
            LEFT JOIN
                project p ON (p.id = pc.project)
            WHERE
                pc.project = '" . $id . "' AND pc.creator = '" . userid() . "'
        ");

        if (!$colors) {
            die();
        }

        $aco = new acofile();

        $aco->acofile($colors[0]['project'] . '.aco');

        $collection = Array();

        foreach($colors as $color => $rgb) {
            $collection[$rgb['name']] = array($rgb['r'], $rgb['g'], $rgb['b']);
        }

        $aco->add($collection);

        $aco->outputAcofile();

        break;

}