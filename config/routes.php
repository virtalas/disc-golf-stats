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

  // Ratojen listaus- ja esittelysivu
  $routes->get('/course', 'check_logged_in', function() {
    CourseController::index();
  });

  // Ratojen listaussivu
  $routes->get('/course/list', 'check_logged_in', function() {
    CourseController::list_courses();
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

  // Pelin poisto
  $routes->post('/game/:gameid/destroy', 'check_logged_in', function($gameid){
    GameController::destroy($gameid);
  });

  // Pelaaja

  // Pelaajien listaussivu ja esittelysivu
  $routes->get('/player', 'check_logged_in', function() {
    PlayerController::index();
  });

  // Pelaajien traditionaalinen listaussivu
  $routes->get('/player/list', 'check_logged_in', function() {
    // PlayerController::list_players();
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
  // $routes->get('/register', function(){
  //   UserController::register();
  // });

  // Rekisteröitymisen käsittely
  // $routes->post('/register', function(){
  //   UserController::handle_register();
  // });
