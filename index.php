<?php

  // Error messages
  error_reporting(E_ALL);
  ini_set('display_errors', '1');

  // index.php base path
  $script_name = $_SERVER['SCRIPT_NAME'];
  $explode =  explode('/', $script_name);

  if($explode[1] == 'index.php'){
    $base_folder = '';
  }else{
    $base_folder = $explode[1];
  }

  define('BASE_PATH', '/' . $base_folder);

  // Create new session if there is none
  if(session_id() == '') {
    ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 7);
    session_start();
  }

  header('Content-Type: text/html; charset=utf-8');

  // Composer
  require 'vendor/autoload.php';

  $routes = new \Slim\Slim();
  $routes->add(new \Zeuxisoo\Whoops\Provider\Slim\WhoopsMiddleware);

  $routes->get('/tietokantayhteys', function(){
    DB::test_connection();
  });

  require 'config/routes.php';

  $routes->run();
