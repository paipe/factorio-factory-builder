<?php
/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 16:09
 */

declare(strict_types=1);

namespace Builder;


class SimpleBuilder extends Builder
{

    public function build(): array
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

}
