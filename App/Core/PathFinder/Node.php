<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 29.12.17
 * Time: 10:42
 */

declare(strict_types=1);

namespace App\Core\PathFinder;

/**
 * Нода, использующаяся при построении пути PathFinder'ом
 *
 * Class Node
 * @package App\Core\PathFinder
 */
class Node
{
    /**
     * @var int
     */
    public $f;

    /**
     * @var int
     */
    public $g;

    /**
     * @var int
     */
    public $h;

    /**
     * @var int
     */
    public $x;

    /**
     * @var int
     */
    public $y;

    /**
     * Ссылка на предыдущую ноду для построения маршрута
     *
     * @var Node
     */
    public $cameFrom;

    public function __construct(array $coordinates)
    {
        $this->x = $coordinates['x'];
        $this->y = $coordinates['y'];
    }

    public function calculateF()
    {
        $this->f = $this->h + $this->g;
    }

    public function compareNodes(Node $node): bool
    {
        $result = false;
        if ($node->x == $this->x && $node->y == $this->y) {
            $result = true;
        }

        return $result;
    }

}