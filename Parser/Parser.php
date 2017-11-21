<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 17:30
 */

declare(strict_types=1);

namespace Parser;

use Exceptions\EmptyItemsException;
use Symfony\Component\Yaml\Yaml;
use Tree\Component;
use Tree\Composite;
use Tree\Item;

class Parser
{
    private const YAML_FILE = __DIR__ . '/../items.yaml';

    private $data;

    public function buildTree(string $name): Component
    {
        $this->data = $this->parse();
        if (empty($this->data)) {
            throw new EmptyItemsException();
        }

        return $this->construct($name, $this->data[$name]);

    }

    private function construct(string $name, array $item): Component
    {
        if (isset($item['children'])) {
            $result = new Composite($name, $item['time'], isset($item['output']) ? $item['output'] : 1);
            foreach ($item['children'] as $children => $count) {
                $result->addItems($this->construct($children, $this->data[$children]), $count);
            }
        } else {
            $result = new Item($name);
        }

        return $result;
    }

    private function parse(): array
    {
        return Yaml::parse(file_get_contents(self::YAML_FILE));
    }

}