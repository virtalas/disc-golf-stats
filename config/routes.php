<?php

  $routes->get('/', function() {
    HelloWorldController::stats();
  });

  $routes->get('/stats', function() {
    HelloWorldController::stats();
  });

  $routes->get('/games', function() {
    HelloWorldController::games();
  });

  $routes->get('/courses', function() {
    HelloWorldController::courses();
  });

  $routes->get('/sign_in', function() {
    HelloWorldController::sign_in();
  });

  $routes->get('/hiekkalaatikko', function() {
    HelloWorldController::sandbox();
  });

  $routes->get('/courses/1', function() {
    HelloWorldController::course_info();
  });

  $routes->get('/games/edit/1', function() {
    HelloWorldController::game_edit();
  });

  $routes->get('/courses/edit/1', function() {
    HelloWorldController::course_edit();
  });
