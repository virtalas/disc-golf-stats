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

  // Ratojen listaussivu
  $routes->get('/course', function() {
    CourseController::index();
  });

  // Radan lisääminen tietokantaan
  $routes->post('/course', function(){
    CourseController::store();
  });

  // Radan lisäyssivu
  $routes->get('/course/new', function(){
    CourseController::create();
  });

  // Radan esittelysivu
  $routes->get('/course/:courseid', function($courseid){
    CourseController::show($courseid);
  });

  // Radan muokkauslomakkeen esittäminen
  $routes->get('/course/:courseid/edit', function($courseid){
    CourseController::edit($courseid);
  });

  // Radan muokkaaminen
  $routes->post('/course/:courseid/edit', function($courseid){
    CourseController::update($courseid);
  });

  // Radan poisto
  $routes->post('/course/:courseid/destroy', function($courseid){
    CourseController::destroy($courseid);
  });

  // Kirjautumislomakkeen esittäminen
  $routes->get('/login', function(){
    UserController::login();
  });

  // Kirjautumisen käsittely
  $routes->post('/login', function(){
    UserController::handle_login();
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
