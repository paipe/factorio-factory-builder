<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.01.18
 * Time: 16:02
 */

namespace Drawer;


use Map\Map;
use Map\ObjectProto;

class ObjectMapGdDrawer extends Drawer
{
    const PIXELS_ON_DOT = 50;
    const SRC_PATH = 'src/';
    const SRC_EXTENSION = '.png';
    const RESULT_FILENAME = 'result.jpeg';

    /**
     * @var Map
     */
    private $map;

    private $img;

    public function setMap(Map $map)
    {
        $this->map = $map;
    }

    public function draw()
    {
        $width = $this->map->getWidth();
        $height = $this->map->getHeight();
        $this->img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($this->img, 255, 255, 255);
        imagefill($this->img, 0, 0, $white);

        foreach ($this->map->iterateMapObjects() as $object) {
            $this->drawObject($object);
        }
    }

    /**
     * @param ObjectProto $object
     */
    public function drawObject($object) {
        $x = $object->getX() * self::PIXELS_ON_DOT;
        $y = $object->getY() * self::PIXELS_ON_DOT;
        $fileName = self::SRC_PATH . $object->getFileName() . self::SRC_EXTENSION;

        $objectImgSize = getimagesize($fileName);
        $objectImg = imagecreatefrompng($fileName);
        imagecopy($this->img, $objectImg, $x, $y, 0, 0, $objectImgSize[0], $objectImgSize[0]);
        imagedestroy($objectImg);
    }

}