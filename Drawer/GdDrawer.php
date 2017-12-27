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
    const RESULT_FILENAME = 'result.jpeg';

    /**
     * Mapping BuildObject constants to src file names
     *
     * @var array
     */
    private static $mapping = [
        BuildObject::M_FABRIC => 'fabric',
        BuildObject::M_INSERTER_UP => 'inserter_up',
        BuildObject::M_INSERTER_DOWN => 'inserter_down',
        BuildObject::M_LONG_HANDED_INSERTER_UP => 'long_handed_inserter_up',
        BuildObject::M_PATH => 'road',
        BuildObject::M_ROAD_LEFT => 'road_left',
        BuildObject::M_CHEST => 'chest',
    ];

    /**
     * Create result image
     *
     * @param array $map buildings type
     * @param array $schema structure type
     */
    public function draw(array $map, array $schema)
    {
        $width = count($map[0]) * self::PIXELS_ON_DOT;
        $height = count($map) * self::PIXELS_ON_DOT;
        $img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);

        foreach ($map as $y => $row) {
            foreach ($row as $x => $dot) {
                if ($dot !== BuildObject::M_SPACE && $dot !== BuildObject::M_BLOCKED && $dot !== BuildObject::M_PATH) {
                    $this->drawObject(
                        $img,
                        $dot,
                        $x,
                        $y
                    );
                }
            }
        }

        $roadSchema = [];
        foreach ($schema as $coords => $data) {
            if ($data['name'] == BuildObject::M_PATH) {
                $roadSchema[$coords] = $data;
                continue;
            }
            list($y, $x) = explode(':', $coords);
            $this->drawObject(
                $img,
                $data['name'],
                $x,
                $y
            );
        }

        //с map пожалуй костыль, нужна для дорисовки окончания дорог
        $this->drawRoads($roadSchema, $img, $map);


        imagejpeg($img, self::RESULT_FILENAME);
        imagedestroy($img);
    }

    /**
     * @param resource $img - result img resource
     * @param string $type - BuildObject constant
     * @param int $x1 - left top dot X coordinate
     * @param int $y1 - left top dot Y coordinate
     */
    private function drawObject($img, $type, $x1, $y1)
    {
        $x1 = $x1 * self::PIXELS_ON_DOT;
        $y1 = $y1 * self::PIXELS_ON_DOT;
        $filename =
            self::SRC_PATH .
            (isset(self::$mapping[$type]) ? self::$mapping[$type] : $type) .
            self::SRC_EXTENSION;

        $dotImgSize = getimagesize($filename);
        $dotImg = imagecreatefrompng($filename);
        imagecopy($img, $dotImg, $x1, $y1, 0, 0, $dotImgSize[0], $dotImgSize[0]);
        imagedestroy($dotImg);
    }

    /**
     * @param array $roadSchema
     * @param resource $img
     */
    public function drawRoads($roadSchema, $img, $map)
    {
        $roads = [];
        $roadStarts = [];
        foreach ($roadSchema as $coords => $item) {
            $dots = explode(':', $coords);
            $roads[$item['index']][] = [
                'x' => $dots[1],
                'y' => $dots[0]
            ];
            if (isset($item['start'])) {
                $roadStarts[$item['index']] = $item['start'];
            }

        }

        /** @var array $directions - key format: yx modifiers */
        $directions = [
            '10' => 'down',
            '-10' => 'up',
            '01' => 'right',
            '0-1' => 'left'
        ];

        foreach ($roadStarts as $index => $coords) {
            $prevY = null;
            $prevX = null;
            $prevDirection = null;
            foreach ($roads[$index] as $key => &$dot) {
                if ($prevX == null && $prevY == null) {
                    $prevX = $dot['x'];
                    $prevY = $dot['y'];
                    continue;
                }
                $direction = $directions[(string)($dot['y'] - $prevY) . (string)($dot['x'] - $prevX)];
                if (is_null($prevDirection)) {
                    $prevDirection = $direction;
                }
                if ($direction === $prevDirection) {
                    $roadType = 'road_' . $direction;
                } else {
                    $roadType = 'road_' . $prevDirection . '_' .  $direction;
                }

                $this->drawObject(
                    $img,
                    $roadType,
                    $prevX,
                    $prevY
                );
                $prevX = $dot['x'];
                $prevY = $dot['y'];
                $prevDirection = $direction;
            }
            if ($direction == 'left') {
                $roadType = 'road_left';
            } else {
                $roadType = 'road_' . $direction . '_left';
            }
            $this->drawObject(
                $img,
                $roadType,
                $prevX,
                $prevY
            );
        }
    }

}