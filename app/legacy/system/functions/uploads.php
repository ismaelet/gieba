<?php

// Upload file
function upload_file($file)
{

    $path = substr(SERVER_ROOT.$file, 0, strrpos(SERVER_ROOT.$file, '/'));

    if (! file_exists($path)) {
        mkdir($path, 0777, true);
    }

    move_uploaded_file($_FILES['file']['tmp_name'], SERVER_ROOT.$file);
}

// Upload image
function upload_img($file, $type, $size, $mode = 'min')
{

    $error = false;

    // get file data
    $img_name = $_FILES['file']['name'];
    $img_temp = $_FILES['file']['tmp_name'];
    $img_size = $_FILES['file']['size'];
    $img_conf = $_FILES['file']['error'];

    // generate file resolution and type string
    if ($type == 'img') {
        $conf_type = [IMAGETYPE_JPEG, IMAGETYPE_PNG];
    } elseif ($type == 'png') {
        $conf_type = [IMAGETYPE_PNG];
    } elseif ($type == 'jpg') {
        $conf_type = [IMAGETYPE_JPEG];
    }

    $img_type = exif_imagetype($img_temp);
    $img_res = getimagesize($img_temp);
    $src_w = $img_res[0];
    $src_h = $img_res[1];
    $ratio = $src_w / $src_h;

    // check for errors
    if ($img_size > 90485760) {
        $error = 'file_size';
    } elseif (! in_array($img_type, $conf_type)) {
        $error = 'file_type';
    } elseif (($src_w > 10000) || ($src_h > 10000)) {
        $error = 'file_reso';
    } elseif ($img_conf) {
        $error = 'error';
    } else {

        if ($mode == 'min') {

            $x = $y = 0;

            if ($ratio > 1) {
                $new_w = $size;
                $new_h = $size / $ratio;
            } else {
                $new_w = $size * $ratio;
                $new_h = $size;
            }
        } elseif ($mode == 'max') {

            $x = $y = 0;

            if ($ratio > 1) {
                $new_w = $size * $ratio;
                $new_h = $size;
            } else {
                $new_w = $size;
                $new_h = $size / $ratio;
            }
        } elseif ($mode == 'crop') {

            $new_w = $new_h = $size;

            if ($ratio > 1) {
                $y = 0;
                $x = ($src_w - $src_h) / 2;
                $src_w = $src_h;
            } else {
                $x = 0;
                $y = ($src_h - $src_w) / 2;
                $src_h = $src_w;
            }
        }

        $temp = imagecreatetruecolor($new_w, $new_h);

        $path = substr(SERVER_ROOT.$file, 0, strrpos(SERVER_ROOT.$file, '/'));

        if (! file_exists($path)) {
            mkdir($path, 0777, true);
        }

        if ($type == 'png') {
            $orig = imagecreatefrompng($img_temp);
            imagealphablending($temp, false);
            imagesavealpha($temp, true);
            $trsp = imagecolorallocatealpha($temp, 255, 255, 255, 127);
            imagefilledrectangle($temp, 0, 0, $new_w, $new_h, $trsp);
            imagecopyresampled($temp, $orig, 0, 0, $x, $y, $new_w, $new_h, $src_w, $src_h);
            imagepng($temp, SERVER_ROOT.$file);
        } elseif ($type == 'jpg' || $type == 'img') {
            $orig = imagecreatefromstring(file_get_contents($img_temp));
            imagecopyresampled($temp, $orig, 0, 0, $x, $y, $new_w, $new_h, $src_w, $src_h);
            imagejpeg($temp, SERVER_ROOT.$file, 80);
        }

        imagedestroy($orig);
        imagedestroy($temp);
    }

    return $error;
}
