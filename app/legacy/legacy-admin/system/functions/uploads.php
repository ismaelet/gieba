<?php

// upload a file
function upload_file($filePath, $name = 'file')
{

    $filePath = strpos($filePath, SERVER_ROOT) !== false || (defined('__PUBLIC_SERVER_ROOT__') && strpos($filePath, __PUBLIC_SERVER_ROOT__) !== false) ? $filePath : SERVER_ROOT.'/'.$filePath;
    $src = $_FILES[$name];
    $filePath = explode('/', $filePath);
    $file = array_pop($filePath);
    $directory = implode('/', $filePath);

    if (! file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    move_uploaded_file($src['tmp_name'], $directory.'/'.$file);
}

/**
 * Upload an image
 *
 * @param  array  $fileData
 *                           url: the absolute destination path
 *                           size: the size of the image, used by "mode"
 *                           ?name [file]: the name of the file input
 *                           ?key: the key of the file input, in case of multiple uploads from the same input
 *                           ?type [webp]: the type of the destination image (webp, jpg, png)
 *                           ?mode [min]: the re-size method:
 *                           min: the smallest side will be of the set size
 *                           max: the largest side will be of the set size
 *                           crop: crop following the set size
 *                           ?quality [80]: the compression quality, not used for PNG
 * @return $error
 */
function upload_img($fileData)
{

    $error = false;

    $url = $fileData['url'];
    $size = $fileData['size'];
    $type = isset($fileData['type']) && in_array($fileData['type'], ['webp', 'jpg', 'png']) ? $fileData['type'] : 'webp';
    $mode = isset($fileData['mode']) && in_array($fileData['mode'], ['width', 'height', 'min', 'max', 'crop']) ? $fileData['mode'] : 'min';
    $quality = $fileData['quality'] ?? 80;

    // get file data
    if (isset($fileData['key'])) {
        $srcImg = $_FILES[$fileData['name'] ?? 'file']['tmp_name'][$fileData['key']];
        $fileName = $_FILES[$fileData['name'] ?? 'file']['name'][$fileData['key']];
        $fileSize = $_FILES[$fileData['name'] ?? 'file']['size'][$fileData['key']];
        $fileConf = $_FILES[$fileData['name'] ?? 'file']['error'][$fileData['key']];
    } else {
        $srcImg = $_FILES[$fileData['name'] ?? 'file']['tmp_name'];
        $fileName = $_FILES[$fileData['name'] ?? 'file']['name'];
        $fileSize = $_FILES[$fileData['name'] ?? 'file']['size'];
        $fileConf = $_FILES[$fileData['name'] ?? 'file']['error'];
    }

    $srcType = exif_imagetype($srcImg);
    $srcResolution = getimagesize($srcImg);
    $srcWidth = $srcResolution[0] ?? 0;
    $srcHeight = $srcResolution[1] ?? 0;

    // check for errors
    if (! in_array($srcType, [IMAGETYPE_WEBP, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
        $error = 'file-type';
    } elseif ($fileConf || ! $srcWidth || ! $srcHeight) {
        $error = 'error';
    } else {
        $ratio = $srcWidth / $srcHeight;

        $x = $y = 0;

        switch ($mode) {
            case 'width':
                $newWidth = $size;
                $newHeight = $size / $ratio;
                break;

            case 'height':
                $newWidth = $size * $ratio;
                $newHeight = $size;
                break;

            case 'max':
                if ($ratio > 1) {
                    $newWidth = $size;
                    $newHeight = $size / $ratio;
                } else {
                    $newWidth = $size * $ratio;
                    $newHeight = $size;
                }
                break;

            case 'min':
                if ($ratio > 1) {
                    $newWidth = $size * $ratio;
                    $newHeight = $size;
                } else {
                    $newWidth = $size;
                    $newHeight = $size / $ratio;
                }
                break;

            case 'crop':
                [$newWidth, $newHeight] = $size;

                $new_ratio = $newWidth / $newHeight;

                if ($ratio > $new_ratio) {
                    $srcWidth = $srcHeight * $new_ratio;
                    $x = ($srcResolution[0] - $srcWidth) / 2;
                } else {
                    $srcHeight = $srcWidth / $new_ratio;
                    $y = ($srcResolution[1] - $srcHeight) / 2;
                }
                break;
        }

        $tempImg = imagecreatetruecolor($newWidth, $newHeight);

        $folder = substr($url, 0, strrpos($url, '/'));

        if (! file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        switch ($type) {
            case 'webp':
                if ($srcType == IMAGETYPE_PNG) {
                    $originalImg = imagecreatefrompng($srcImg);
                    imagealphablending($tempImg, false);
                    imagesavealpha($tempImg, true);
                    $trsp = imagecolorallocatealpha($tempImg, 255, 255, 255, 127);
                    imagefilledrectangle($tempImg, 0, 0, $newWidth, $newHeight, $trsp);
                    imagecopyresampled($tempImg, $originalImg, 0, 0, $x, $y, $newWidth, $newHeight, $srcWidth, $srcHeight);
                    imagewebp($tempImg, $url, $quality);
                } else {
                    $originalImg = imagecreatefromstring(file_get_contents($srcImg));
                    imagecopyresampled($tempImg, $originalImg, 0, 0, $x, $y, $newWidth, $newHeight, $srcWidth, $srcHeight);
                    imagewebp($tempImg, $url, $quality);
                }
                break;
            case 'jpg':
                $originalImg = imagecreatefromstring(file_get_contents($srcImg));
                imagecopyresampled($tempImg, $originalImg, 0, 0, $x, $y, $newWidth, $newHeight, $srcWidth, $srcHeight);
                imagejpeg($tempImg, $url, $quality);
                break;
            case 'png':
                $originalImg = imagecreatefrompng($srcImg);
                imagealphablending($tempImg, false);
                imagesavealpha($tempImg, true);
                $trsp = imagecolorallocatealpha($tempImg, 255, 255, 255, 127);
                imagefilledrectangle($tempImg, 0, 0, $newWidth, $newHeight, $trsp);
                imagecopyresampled($tempImg, $originalImg, 0, 0, $x, $y, $newWidth, $newHeight, $srcWidth, $srcHeight);
                imagepng($tempImg, $url);
                break;
        }

        imagedestroy($originalImg);
        imagedestroy($tempImg);
    }

    return $error;
}
