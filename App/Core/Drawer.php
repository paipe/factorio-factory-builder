<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 09.01.18
 * Time: 16:02
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Map\ObjectProto;

class Drawer
{
    /** Параметры для создания изображения */
    const PIXELS_ON_DOT = 50;
    const SRC_PATH = __DIR__ . '/../../src/images/';
    const SRC_EXTENSION = '.png';
    const RESULT_FILENAME = 'result.jpeg';

    /**
     * @var Map
     */
    private $map;

    /**
     * @var resource
     */
    private $img;

    public function setMap(Map $map): Drawer
    {
        $this->map = $map;
        return $this;
    }

    public function draw(): void
    {
        $width = $this->map->getWidth() * self::PIXELS_ON_DOT;
        $height = $this->map->getHeight() * self::PIXELS_ON_DOT;
        $this->img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($this->img, 255, 255, 255);
        imagefill($this->img, 0, 0, $white);
        
        foreach ($this->map->iterateMapObjects() as $object) {
            $this->drawObject($object);
        }

        imagejpeg($this->img, self::RESULT_FILENAME);
        imagedestroy($this->img);
    }

    private function drawObject(ObjectProto $object): void
    {
        $x = $object->getX() * self::PIXELS_ON_DOT;
        $y = $object->getY() * self::PIXELS_ON_DOT;
        $fileName = self::SRC_PATH . $object->getFileName() . self::SRC_EXTENSION;

        $objectImgSize = getimagesize($fileName);
        $objectImg = imagecreatefrompng($fileName);
        imagecopy($this->img, $objectImg, $x, $y, 0, 0, $objectImgSize[0], $objectImgSize[0]);
        imagedestroy($objectImg);

        $additionalFileName = $object->getAdditionalFileName();
        if ($additionalFileName !== null) {
            $fileName = self::SRC_PATH . $additionalFileName . self::SRC_EXTENSION;
            $objectImgSize = getimagesize($fileName);
            $objectImg = imagecreatefrompng($fileName);
            imagecopy($this->img, $objectImg, $x + 50, $y + 50, 0, 0, $objectImgSize[0], $objectImgSize[0]);
            imagedestroy($objectImg);
        }
    }

}
