<?php

use App\Http\Controllers\InputController;
use App\Http\Controllers\OutputController;
use Illuminate\Routing\Router;

/**
 * @var Illuminate\Routing\Router $router
 */

$router->controller(InputController::class)
    ->group(static function (Router $group) {
        $group->any('input', 'index');
    });

$router->controller(OutputController::class)
    ->group(static function (Router $group) {
        $group->any('hours', 'byHours');
        $group->any('days', 'byDays');
    });

