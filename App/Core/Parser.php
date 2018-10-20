<?php

/**
 * Created by PhpStorm.
 * User: paipe
 * Date: 10.08.2017
 * Time: 17:30
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Parser\Component;
use App\Exceptions\EmptyItemsException;
use Symfony\Component\Yaml\Yaml;
use App\Core\Parser\Composite;
use App\Core\Parser\Item;
use App\Core\Utils\Logger;

class Parser
{
    private const YAML_FILE = __DIR__ . '/../../resources/items.yaml';

    private $data;

    public function buildTree(string $name): Component
    {
        $this->data = $this->parse();
        if (empty($this->data)) {
            throw new EmptyItemsException();
        }

        $result = $this->construct($name, $this->data[$name]);
        return $result;

    }

    private function construct(string $name, array $item): Component
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

    private function parse(): array
    {
        return Yaml::parse(file_get_contents(self::YAML_FILE));
    }

}