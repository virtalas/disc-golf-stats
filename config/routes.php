<?php

  function check_logged_in() {
    BaseController::check_logged_in();
  }

  function check_admin_logged_in() {
    BaseController::check_admin_logged_in();
  }

  // Stats

  $routes->get('/', function() {
    StatsController::stats();
  });

  $routes->get('/stats', function() {
    StatsController::stats();
  });

  // Radat

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

  // Radan muokkauslomakkeen esittäminen (vain admin)
  $routes->get('/course/:courseid/edit', 'check_admin_logged_in', function($courseid){
    CourseController::edit($courseid);
  });

  // Radan muokkaaminen (vain admin)
  $routes->post('/course/:courseid/edit', 'check_admin_logged_in', function($courseid){
    CourseController::update($courseid);
  });

  // Radan poisto (vain admin)
  $routes->post('/course/:courseid/destroy', 'check_admin_logged_in', function($courseid){
    CourseController::destroy($courseid);
  });

  // Pelit

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

  // Pelin esittelysivu
  $routes->get('/game/:gameid', 'check_logged_in', function($gameid){
    GameController::show($gameid);
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

  // Käyttäjä

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

  // Rekisteröitymislomakkeen esittäminen
  $routes->get('/register', function(){
    UserController::register();
  });

  // Rekisteröitymisen käsittely
  $routes->post('/register', function(){
    UserController::handle_register();
  });

  // Suunnitelmasivut

  $routes->get('/games', 'check_logged_in', function() {
    HelloWorldController::games();
  });

  $routes->get('/courses', 'check_logged_in', function() {
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
