<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.12.17
 * Time: 16:57
 */

declare(strict_types=1);

namespace App\Core;


use App\Core\Builder\Blueprints\FactoryBlueprint;
use App\Core\Builder\Blueprints\SourceBlueprint;
use App\Core\Map\Objects\ChestObject;
use App\Core\Map\Objects\EePointRoadObject;
use App\Core\Map\Objects\FactoryObject;
use App\Core\Map\Objects\InserterObject;
use App\Core\Map\Objects\RoadObject;
use App\Core\Parser\Component;
use App\Core\Utils\Logger;
use App\Core\Utils\Utils;

/**
 * Строит мини-схемы на основании дерева компонентов
 *
 * Class Builder
 * @package App\Core
 */
class Builder
{
    /**
     * @var Component
     */
    protected $tree;

    /**
     * @var int per second
     */
    protected $count;

    public function setTree(Component $tree): Builder
    {
        $this->tree = $tree;
        return $this;
    }

    public function setCount(float $count): Builder
    {
        $this->count = $count;
        return $this;
    }

    public function build(): array
    {
        $result = [];
        $schema = $this->tree->countConstructTime($this->count);
        Logger::info('Start building objects', $schema);
        foreach ($schema as $object) {
            if ($object['time'] > 0) {
                $factoryBlueprint = new FactoryBlueprint();
                $result[] = $factoryBlueprint->make($object);
            } else {
                $sourceBlueprint = new SourceBlueprint();
                $result[] = $sourceBlueprint->make($object);
            }
        }

        return $result;
    }

}