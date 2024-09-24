<?php

$file = $_GET['src'];

if(preg_match('/jpg|jpeg|JPG|JPEG/', $file))
    $image = imagecreatefromjpeg($file);
elseif(preg_match('/gif|GIF/', $file))
    $image = imagecreatefromgif($file);
elseif(preg_match('/png/', $file))
    $image = imagecreatefrompng($file);

list($width, $height) = getimagesize($file);

$virtualImage = imagecreatetruecolor(1215, 400);

//imagecopyresampled($virtualImage, $image, 0, 0, $_GET['x1'], $_GET['y1'], 1215, 400, $_GET['x1'] + $_GET['x2'], $_GET['y1'] + $_GET['y2']);
imagecopyresampled($virtualImage, $image, 0, 0, $_GET['x1'], $_GET['y1'], 1215, 400, $_GET['x2'] - $_GET['x1'], $_GET['y2'] - $_GET['y1']);

imagejpeg($virtualImage, null, 100);