<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 08.07.2017
 * Time: 12:21
 */

header("Content-Type: image/png");
$im = @imagecreate(110, 20)
or die("Невозможно создать поток изображения");
$background_color = imagecolorallocate($im, 0, 0, 0);
$text_color = imagecolorallocate($im, 233, 14, 91);
imagestring($im, 1, 5, 5,  "Простая Текстовая Строка", $text_color);
imagepng($im);
imagedestroy($im);