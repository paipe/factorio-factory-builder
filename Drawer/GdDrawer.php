<?php
/**
 * Created by PhpStorm.
 * User: alexander.panov
 * Date: 9/20/2017
 * Time: 12:23 PM
 */

namespace Drawer;

use Builder\BuildObject;

class GdDrawer extends Drawer
{
    const PIXELS_ON_DOT = 50;
    const SRC_PATH = 'src/';
    const SRC_EXTENSION = '.png';

    private static $maping = [
        BuildObject::M_FABRIC => 'fabric',
        BuildObject::M_UP_MANIPULATOR => 'up_manipulator',
        BuildObject::M_UP_LONG_MANIPULATOR => 'up_long_manipulator',
        BuildObject::M_LEFT_ROAD => 'left_road',
        BuildObject::M_CHEST => 'chest',
    ];

    public function draw(array $data, array $schema) {
        $width = count($data[0]) * self::PIXELS_ON_DOT;
        $height = count($data) * self::PIXELS_ON_DOT;
        $img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        foreach ($data as $y => $row) {
            foreach ($row as $x => $dot) {
                if ($dot !== BuildObject::M_SPACE) {
                    $this->drawObject(
                        $img,
                        $dot,
                        $x * self::PIXELS_ON_DOT,
                        $y * self::PIXELS_ON_DOT
                    );
                }
            }
        }

        foreach ($schema as $coords => $name) {
            list($y, $x) = explode(':', $coords);
            $this->drawObject(
                $img,
                $name,
                $x * self::PIXELS_ON_DOT,
                $y * self::PIXELS_ON_DOT
            );
        }

        imagejpeg($img, 'result.jpeg');
        imagedestroy($img);
    }

    private function drawObject($img, $type, $x1, $y1)
    {
        $filename =
            self::SRC_PATH .
            (isset(self::$maping[$type]) ? self::$maping[$type] : $type) .
            self::SRC_EXTENSION;

        $dotImgSize = getimagesize($filename);
        $dotImg = imagecreatefrompng($filename);
        imagecopy($img, $dotImg, $x1, $y1, 0, 0, $dotImgSize[0], $dotImgSize[0]);
        imagedestroy($dotImg);
    }

}