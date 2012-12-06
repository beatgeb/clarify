<?php
/**
 * Clarify.
 *
 * Copyright (C) 2012 Roger Dudler <roger.dudler@gmail.com>
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/**
 * Scale & crops a screen image
 * @param id - screen id
 * @param viewport - dimension (width, height) and position (x,y) of the viewport
 * @param dimension - target dimension (width, height)
 * @param path - target path
 * @return void
 */
function cropScreen($id, $viewport, $dimension, $path) {
    global $db;

    $w = $dimension['width'];
    $h = $dimension['height'];
    $screen = $db->single("SELECT id, project, type, ext, embeddable FROM screen WHERE id = '" . $id . "' LIMIT 1");
    if (!$screen) { die(); }
    if (!$screen['embeddable']) {
        permission($screen['project'], 'VIEW');
    }
    $filename =  UPLOAD . 'screens/' . $screen['project'] . '/' . md5($screen['id'] . config('security.general.hash')) . '.' . $screen['ext'];
    $target =  TERRIFIC . $path;

    if (!is_file($target)) {
        if (!is_dir(dirname($target))) {
            @mkdir(dirname($target), 0777, true);
        }

        $width = $viewport['width'];
        $height = $viewport['height'];
        $r = $width / $height;
        $newheight = $w / $r;
        $newwidth = $w;

        switch ($screen['type']) {
            case 'image/jpeg':
            case 'image/jpg':
                $src = imagecreatefromjpeg($filename);
                break;
            case 'image/png':
                $src = imagecreatefrompng($filename);
                break;
        }
        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, $viewport['x'], $viewport['y'], $newwidth, $newheight, $width, $height);
        imagepng($dst, $target);
    }
}