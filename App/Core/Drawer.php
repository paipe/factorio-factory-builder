<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.01.18
 * Time: 16:02
 */

namespace App\Core;


use App\Core\Map\ObjectProto;

class Drawer
{
    const PIXELS_ON_DOT = 50;
    const SRC_PATH = __DIR__ . '/../../src/images/';
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
        return $this;
    }

    public function draw()
    {
        $width = $this->map->getWidth() * self::PIXELS_ON_DOT;
        $height = $this->map->getHeight() * self::PIXELS_ON_DOT;
        $this->img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($this->img, 255, 255, 255);
        imagefill($this->img, 0, 0, $white);
        
        $this->map->processRoadDirections();
        foreach ($this->map->iterateMapObjects() as $object) {
            $this->drawObject($object);
        }

        imagejpeg($this->img, self::RESULT_FILENAME);
        imagedestroy($this->img);
    }

    /**
     * @param ObjectProto $object
     */
    private function drawObject($object) {
        $x = $object->getX() * self::PIXELS_ON_DOT;
        $y = $object->getY() * self::PIXELS_ON_DOT;
        $fileName = $object->getFileName();
        if (is_array($fileName)) {
            $additionalImage = $fileName[1];
            $fileName = $fileName[0];
        }
        $fileName = self::SRC_PATH . $fileName . self::SRC_EXTENSION;

        $objectImgSize = getimagesize($fileName);
        $objectImg = imagecreatefrompng($fileName);
        imagecopy($this->img, $objectImg, $x, $y, 0, 0, $objectImgSize[0], $objectImgSize[0]);
        imagedestroy($objectImg);

        //TODO костыль лютый
        if (isset($additionalImage)) {
            $fileName = self::SRC_PATH . $additionalImage . self::SRC_EXTENSION;
            $objectImgSize = getimagesize($fileName);
            $objectImg = imagecreatefrompng($fileName);
            imagecopy($this->img, $objectImg, $x + 50, $y + 50, 0, 0, $objectImgSize[0], $objectImgSize[0]);
            imagedestroy($objectImg);
        }
    }

}
