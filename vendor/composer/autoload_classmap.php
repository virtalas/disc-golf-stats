<?php

// autoload_classmap.php @generated by Composer

$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);

return array(
    'BaseController' => $baseDir . '/lib/base_controller.php',
    'BaseModel' => $baseDir . '/lib/base_model.php',
    'CSVScoreProcessor' => $baseDir . '/lib/process_csv_scores.php',
    'Course' => $baseDir . '/app/models/Course.php',
    'CourseController' => $baseDir . '/app/controllers/course_controller.php',
    'DB' => $baseDir . '/lib/database.php',
    'DatabaseConfig' => $baseDir . '/config/database.php',
    'Game' => $baseDir . '/app/models/Game.php',
    'GameController' => $baseDir . '/app/controllers/game_controller.php',
    'HelloWorld' => $baseDir . '/app/models/hello_world.php',
    'HelloWorldController' => $baseDir . '/app/controllers/hello_world_controller.php',
    'Hole' => $baseDir . '/app/models/Hole.php',
    'Player' => $baseDir . '/app/models/Player.php',
    'PlayerController' => $baseDir . '/app/controllers/player_controller.php',
    'Redirect' => $baseDir . '/lib/redirect.php',
    'Score' => $baseDir . '/app/models/Score.php',
    'StatsController' => $baseDir . '/app/controllers/stats_controller.php',
    'UserController' => $baseDir . '/app/controllers/user_controller.php',
    'View' => $baseDir . '/lib/view.php',
    'Whoops\\Module' => $vendorDir . '/filp/whoops/src/deprecated/Zend/Module.php',
    'Whoops\\Provider\\Zend\\ExceptionStrategy' => $vendorDir . '/filp/whoops/src/deprecated/Zend/ExceptionStrategy.php',
    'Whoops\\Provider\\Zend\\RouteNotFoundStrategy' => $vendorDir . '/filp/whoops/src/deprecated/Zend/RouteNotFoundStrategy.php',
);
