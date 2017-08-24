<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 17:30
 */

namespace Parser;

use Symfony\Component\Yaml\Yaml;
use Tree\Composite;
use Tree\Item;

class Parser
{
    const YAML = __DIR__ . '/../items.yaml';

    private $data;

    public function buildTree($name)
    {
        $this->parse();
        if (!isset($this->data[$name])) {
            return false;
        }
        return $this->construct($name, $this->data[$name]);

    }

    private function construct($name, $item)
    {
        if (isset($item['children'])) {
            $result = new Composite($name, $item['time']);
            foreach ($item['children'] as $children => $count) {
                $result->addItems($this->construct($children, $this->data[$children]), $count);
            }
        } else {
            $result = new Item($name);
        }

        return $result;
    }

    private function parse()
    {
        $this->data = Yaml::parse(file_get_contents(self::YAML));
    }

}