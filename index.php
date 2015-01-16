<?php

  // Luodaan uusi tai palautetaan olemassaoleva sessio
  if(session_id() == '') {
    session_start();
  }

  // Asetetaan vastauksen Content-Type-otsake, jotta ääkköset näkyvät normaalisti
  header('Content-Type: text/html; charset=utf-8');

  // Otetaan Composer käyttöön
  require 'vendor/autoload.php';

  \Slim\Slim::registerAutoloader();
  
  $app = new \Slim\Slim();

  $app->get('/tietokantayhteys', function(){
    DB::test_connection();
  });

  // Otetaan reitit käyttöön
  require 'config/routes.php';

  $app->run();
