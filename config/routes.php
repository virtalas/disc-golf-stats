<?php

  function check_logged_in(){
    BaseController::check_logged_in();
  }

  $routes->get('/', function() {
    HelloWorldController::stats();
  });

  $routes->get('/stats', function() {
    HelloWorldController::stats();
  });

  $routes->get('/games', 'check_logged_in', function() {
    HelloWorldController::games();
  });

  $routes->get('/courses', 'check_logged_in', function() {
    HelloWorldController::courses();
  });

  // Ratojen listaussivu
  $routes->get('/course', 'check_logged_in', function() {
    CourseController::index();
  });

  // Radan lisääminen tietokantaan
  $routes->post('/course', 'check_logged_in', function(){
    CourseController::store();
  });

  // Radan lisäyssivu
  $routes->get('/course/new', 'check_logged_in', function(){
    CourseController::create();
  });

  // Radan esittelysivu
  $routes->get('/course/:courseid', 'check_logged_in', function($courseid){
    CourseController::show($courseid);
  });

  // Radan muokkauslomakkeen esittäminen
  $routes->get('/course/:courseid/edit', 'check_logged_in', function($courseid){
    CourseController::edit($courseid);
  });

  // Radan muokkaaminen
  $routes->post('/course/:courseid/edit', 'check_logged_in', function($courseid){
    CourseController::update($courseid);
  });

  // Radan poisto
  $routes->post('/course/:courseid/destroy', 'check_logged_in', function($courseid){
    CourseController::destroy($courseid);
  });

  // Pelien listaussivu
  $routes->get('/game', 'check_logged_in', function() {
    GameController::index();
  });

  // Pelin lisääminen tietokantaan
  $routes->post('/game', 'check_logged_in', function(){
    GameController::store();
  });

  // Pelin lisäyssivu
  $routes->get('/game/new', 'check_logged_in', function(){
    GameController::create();
  });

  // Pelin muokkauslomakkeen esittäminen
  $routes->get('/game/:gameid/edit', 'check_logged_in', function($gameid){
    GameController::edit($gameid);
  });

  // Pelin muokkaaminen
  $routes->post('/game/:gameid/edit', 'check_logged_in', function($gameid){
    GameController::update($gameid);
  });

  // Radan poisto
  $routes->post('/game/:gameid/destroy', 'check_logged_in', function($gameid){
    GameController::destroy($gameid);
  });

  // Kirjautumislomakkeen esittäminen
  $routes->get('/login', function(){
    UserController::login();
  });

  // Kirjautumisen käsittely
  $routes->post('/login', function(){
    UserController::handle_login();
  });

  // Uloskirjautuminen
  $routes->post('/logout', function(){
    UserController::logout();
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
