<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 16:09
 */

namespace Builder;


class SimpleBuilder extends Builder
{

    /**
     * @return BuildObject[] array
     */
    public function build()
    {
        $result = [];

        $schema = $this->tree->countConstructTime($this->count);
        foreach ($schema as $object) {
            $count = (int)ceil($object['time'] / 0.5);
            $in = isset($object['children']) ? array_keys($object['children']) : [];
            $result[] = new BuildObject($object['name'], $in, $count);
        }

        return $result;
    }

    public function showBuildObjects()
    {
        $buildObjects = $this->build();
        foreach ($buildObjects as $object) {
            if ($object->factoryCount > 0) {
                $object->show();
                echo PHP_EOL . '===============' . PHP_EOL;
            }
        }
    }
    
}