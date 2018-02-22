<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 22.02.18
 * Time: 16:59
 */

namespace App\Core\Map\Move;


class Move
{
    public const T_ROAD = 'road';
    public const T_SPLITTER = 'splitter';
    public const T_UNDERGROUND_ROAD = 'underground_road';
    public const T_INSERTER = 'inserter';
    public const T_PIPE = 'pipe';
    public const T_UNDERGROUND_PIPE = 'underground_pipe';

    protected $contentModules   = [];
    protected $directionModules = [];
    protected $placementModules = [];


}