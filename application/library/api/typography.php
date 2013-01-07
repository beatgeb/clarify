<?php

lock();

// Typography API
define('API_TYPOGRAPHY_ADD', 'typography.add');
define('API_TYPOGRAPHY_GET', 'typography.get');
define('API_TYPOGRAPHY_DELETE', 'typography.delete');
define('API_TYPOGRAPHY_UPDATE', 'typography.update');
define('API_TYPOGRAPHY_MOVE', 'typography.move');
define('API_TYPOGRAPHY_RESIZE', 'typography.resize');
define('API_TYPOGRAPHY_DATA', 'typography.data');

switch ($action) {
    
    case API_TYPOGRAPHY_ADD:
        $screen = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        $width = intval($route[7]);
        $height = intval($route[8]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'EDIT');

        $project_font = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $screen['project'],
            'name' => 'Untitled',
            'name_css' => 'untitled',
            'family' => 'inherit',
            'size' => 12,
            'line_height' => 1
        );
        $project_font_id = $db->insert('project_font', $project_font);

        $font = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'screen' => $screen['id'],
            'font' => $project_font_id,
            'nr' => 1,
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height
        );
        $id = $db->insert('font', $font);
        $font['id'] = $id;
        $font['family'] = $project_font['family'];
        $font['size'] = $project_font['size'];

        // increase count on screen
        $db->query("UPDATE screen SET count_font = count_font + 1 WHERE id = " . $screen['id'] . "");
        
        // add to activity stream
        activity_add(
            '{actor} added a font definition on screen {target}', 
            userid(), OBJECT_TYPE_USER, user('name'), 
            ACTIVITY_VERB_ADD, 
            $id, OBJECT_TYPE_FONT, null, 
            $screen['id'], OBJECT_TYPE_SCREEN, null
        );

        // return result
        header('Content-Type: application/json');
        echo json_encode($font);
        break;

    case API_TYPOGRAPHY_DATA:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a font id'); }
        $font = $db->single("
            SELECT 
                f.id, 
                f.creator, 
                f.nr, 
                f.x, 
                f.y, 
                f.width, 
                f.height,
                pf.project, 
                pf.name, 
                pf.family, 
                pf.size, 
                pf.line_height,
                pc.name as color_name, 
                pc.hex as color_hex,
                pch.name as color_hover_name, 
                pch.hex as color_hover_hex,
                pca.name as color_active_name, 
                pca.hex as color_active_hex
            FROM font f 
                LEFT JOIN project_font pf ON (pf.id = f.font)
                LEFT JOIN project_color pc ON (pc.id = pf.color)
                LEFT JOIN project_color pch ON (pch.id = pf.color_hover)
                LEFT JOIN project_color pca ON (pca.id = pf.color_active)
            WHERE f.id = '" . $id . "'"
        );
        $font['color_hex'] = $font['color_hex'] != null ? '#' . $font['color_hex'] : null;
        $font['color_hover_hex'] = $font['color_hover_hex'] != null ? '#' . $font['color_hover_hex'] : null;
        $font['color_active_hex'] = $font['color_active_hex'] != null ? '#' . $font['color_active_hex'] : null;
        permission($font['project'], 'VIEW');
        header('Content-Type: application/json');
        echo json_encode($font);
        break;
    
    case API_TYPOGRAPHY_UPDATE:
        $response = array('success' => false);
        $request = json_decode(file_get_contents('php://input'));
        if ($request) {
            $font = $db->single("
                SELECT 
                    f.font,
                    pf.project 
                FROM font f
                    LEFT JOIN project_font pf ON (pf.id = f.font)
                WHERE 
                    f.id = " . intval($request->font->id) . "
                LIMIT 1
            ");

            // check permission
            permission($font['project'], 'VIEW');

            // build data
            $update = array(
                'family' => $request->font->family,
                'size' => $request->font->size,
                'name' => $request->font->name,
                'name_css' => slug($request->font->name),
                'line_height' => $request->font->line_height
            );

            // check, if new color already exists in the library
            $update['color'] = typography_color($font['project'], $request->font->color);
            $update['color_hover'] = typography_color($font['project'], $request->font->color_hover);
            $update['color_active'] = typography_color($font['project'], $request->font->color_active);
            
            $db->update('project_font', $update, array('id' => $font['font']));
            $response['font'] = $font;
            $response['success'] = true;
        }
        header('Content-Type: application/json');
        echo json_encode($response);
    /*
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a font id'); }
        $font = $db->single("SELECT pf.project FROM font f LEFT JOIN project_font pf ON (pf.id = f.font)
                LEFT JOIN project_color pc ON (pc.id = pf.color)
            WHERE f.id = '" . $id . "'"
        );
         */
        //$font_family = $route[7];
        //$font_size = intval($route[8]);
        //$font_color = substr($route[9],0,6);
        
        // check if font already exists in the library
        

        break;

    case API_TYPOGRAPHY_GET:
        $screen = intval($route[4]);
        if ($screen < 1) { die('Please provide a screen id'); }
        $screen = $db->single("SELECT id, project FROM screen WHERE id = '" . $screen . "'");
        permission($screen['project'], 'VIEW');
        $data = $db->data("
            SELECT 
                f.id, 
                f.creator, 
                f.nr, 
                f.x, 
                f.y, 
                f.width, 
                f.height,
                pf.name, 
                pf.family, 
                pf.size, 
                pf.line_height,
                pc.name as color_name, 
                pc.hex as color_hex,
                pch.name as color_hover_name, 
                pch.hex as color_hover_hex,
                pca.name as color_active_name, 
                pca.hex as color_active_hex
            FROM font f 
                LEFT JOIN project_font pf ON (pf.id = f.font)
                LEFT JOIN project_color pc ON (pc.id = pf.color)
                LEFT JOIN project_color pch ON (pch.id = pf.color_hover)
                LEFT JOIN project_color pca ON (pca.id = pf.color_active)
            WHERE 
                f.screen = " . $screen['id']
        );
        while(list($key, $screen) = each($data)) {
            $data[$key]['color_hex'] = $data[$key]['color_hex'] != null ? '#' . $data[$key]['color_hex'] : null;
            $data[$key]['color_hover_hex'] = $data[$key]['color_hover_hex'] != null ? '#' . $data[$key]['color_hover_hex'] : null;
            $data[$key]['color_active_hex'] = $data[$key]['color_active_hex'] != null ? '#' . $data[$key]['color_active_hex'] : null;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
        break;
    
    case API_TYPOGRAPHY_MOVE:
        $id = intval($route[4]);
        $x = intval($route[5]);
        $y = intval($route[6]);
        if ($id < 1) { die('Please provide a font id'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'x' => $x,
            'y' => $y
        );
        $db->update('font', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_TYPOGRAPHY_RESIZE:
        $id = intval($route[4]);
        $width = intval($route[5]);
        $height = intval($route[6]);
        if ($id < 1) { die('Please provide a measure id'); }
        if ($width < 1) { die('Please provide a width'); }
        if ($height < 1) { die('Please provide a height'); }
        $data = array(
            'modified' => date('Y-m-d H:i:s'),
            'modifier' => userid(),
            'width' => $width,
            'height' => $height
        );
        $db->update('font', $data, array('id' => $id, 'creator' => userid()));
        break;
    
    case API_TYPOGRAPHY_DELETE:
        $id = intval($route[4]);
        if ($id < 1) { die('Please provide a font id'); }
        $font = $db->single('
            SELECT f.screen, f.font, pf.project
            FROM font f LEFT JOIN project_font pf ON pf.id = f.font
            WHERE f.id = ' . $id . ' AND f.creator = ' . userid()
        );
        if (!$font) { die(); }
        permission($font['project'], 'EDIT');
        // check if there are other definitions using the exact same font
        $others = $db->single('SELECT COUNT(id) as total FROM font WHERE font = "' . $font['font'] . '"');
        $db->delete('font', array('id' => $id));
        if ($others['total'] == 0) {
            $db->delete('project_font', array('id' => $font['font']));
        }
        $db->query("UPDATE screen SET count_font = count_font - 1 WHERE id = " . $font['screen'] . "");
        echo json_encode(array('RESULT' => 'OK'));
        break;

}

function typography_color($project, $color) {
    global $db;
    if ($color == null) {
        return null;
    }
    $color = strlen($color) == 7 ? substr($color,1) : $color;
    $project_color = $db->single("SELECT id FROM project_color WHERE hex = '" . $color . "' AND project = '" . $project . "' LIMIT 1");
    if ($project_color) {
        return $project_color['id'];
    } else {
        require_once LIBRARY . 'color.php';
        $colorHandler = new ColorHandler();
        $hsl = $colorHandler->HtmltoHsl("#" . $color);
        $match = $colorHandler->getColorMatch("#" . $color);
        $rgb = hexToRgbArray("#" . $color);
        $data = array(
            'created' => date('Y-m-d H:i:s'),
            'creator' => userid(),
            'project' => $project,
            'name' => $match[0], 
            'name_css' => slug($match[0]),
            'hex' => $color, 
            'hue' => $hsl['h'] . "", 
            'saturation' => $hsl['s'] . "", 
            'lightness' => $hsl['l'] . "", 
            'r' => $rgb['r'] . "", 
            'g' => $rgb['g'] . "", 
            'b' => $rgb['b'] . ""
        );
        $color_id = $db->insert('project_color', $data);
        return $color_id;
    }
}