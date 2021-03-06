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
    GameController::index(false);
  });

  // Pelien hakusivu
  $routes->get('/search', 'check_logged_in', function(){
    SearchController::index();
  });

  // Pelin lisääminen tietokantaan
  $routes->post('/game', 'check_logged_in', function(){
    GameController::store(True);
  });

  // Viimeisin gameid
  $routes->get('/game/latest/id', "check_logged_in", function(){
    echo Game::latest_gameid();
  });

  // Pelin lisäyssivu
  $routes->get('/game/new', 'check_logged_in', function(){
    GameController::create();
  });

  // Pelin merkkaus mobiili UI:ssa
  $routes->get('/game/mobile/new', 'check_logged_in', function(){
    GameController::initMobileScoreCard();
  });

  // Pelin muokkaus mobile guissa (eli tulosten lisäys)
  $routes->get('/game/mobile/:gameid', 'check_logged_in', function($gameid){
    GameController::mobileScoreCard($gameid);
  });

  // Pelin esittelysivu
  $routes->get('/game/:gameid', 'check_logged_in', function($gameid){
    GameController::show($gameid);
  });

  // Pelin muokkauslomakkeen esittäminen (vain pelin luoja tai admin)
  $routes->get('/game/:gameid/edit', 'check_logged_in', function($gameid){
    GameController::edit($gameid);
  });

  // Pelin muokkaaminen (vain pelin luoja tai admin)
  $routes->post('/game/:gameid/edit', 'check_logged_in', function($gameid){
    GameController::update($gameid, True);
  });

  // Pelin päivittäminen (vain pelin luoja tai admin) mobile guista tuloksia merkatessa
  $routes->post('/game/:gameid/mobile/edit', 'check_logged_in', function($gameid){
    GameController::update($gameid, False);
  });

  // Pelin poisto (vain pelin luoja tai admin)
  $routes->post('/game/:gameid/destroy', 'check_logged_in', function($gameid){
    GameController::destroy($gameid);
  });

  // csv-tiedoston tulostus
  $routes->get('/game/:gameid/export', 'check_logged_in', function($gameid){
    GameController::export($gameid);
  });

  // Näytä tuloskortti-kuvat
  $routes->get('/game/old/:year', 'check_logged_in', function($year){
    GameController::display_score_card_pictures_by_year($year);
  });

  // Ajax get for Javascript

  // Ajax pyynnöllä lista peleistä hakua varten
  $routes->get('/search/game', 'check_logged_in', function(){
    SearchController::search();
  });

  $routes->get('/search/game/:gameid', 'check_logged_in', function($gameid){
    SearchController::searchById($gameid);
  });

  // Ajax pyynnöllä väylän tulokset pelaajalle
  $routes->get('/hole/:holeid/player/:playerid/stats', 'check_logged_in', function($holeid, $playerid){
    Hole::score_distribution($holeid, $playerid);
  });

  // Ajax pyynnöllä väylän tulokset kaikille pelaajille
  $routes->get('/hole/:holeid/stats', 'check_logged_in', function($holeid){
    Hole::score_distribution($holeid, null);
  });



  // Pelaaja

  // Pelaajien listaussivu ja esittelysivu
  $routes->get('/player', 'check_logged_in', function() {
    PlayerController::index();
  });

  // Pelaajien traditionaalinen listaussivu - onko tarvetta?
  $routes->get('/player/list', 'check_logged_in', function() {
    // Not implemented yet
    // PlayerController::list_players();
  });

  // Infosivu
  $routes->get('/info', function(){
    PlayerController::info();
  });

  // Välimuistisivujen poisto (vain admin)
  $routes->get('/clearcache', 'check_admin_logged_in', function(){
    Cache::clear();
    Redirect::to('/', array('message' => 'Sivujen välimuisti poistettu.'));
  });

  // Kuvaajat

  $routes->get('/graphs', 'check_logged_in', function(){
    GraphController::index();
  });

  // Kisat

  // Kisojen listaussivu
  $routes->get('/contest', 'check_logged_in', function(){
    ContestController::index();
  });

  // Kisan lisäyssivu
  $routes->get('/contest/new', 'check_logged_in', function(){
    ContestController::create();
  });

  // Kisan tallentaminen tietokantaan
  $routes->post('/contest', 'check_logged_in', function(){
    ContestController::store();
  });

  // Kisan esittelysivu
  $routes->get('/contest/:contestid', 'check_logged_in', function($contestid){
    ContestController::show($contestid);
  });

  // Kisan muokkauslomakkeen esittäminen (vain kisan luoja)
  $routes->get('/contest/:contestid/edit', 'check_logged_in', function($contestid){
    ContestController::edit($contestid);
  });

  // Kisan muokkaaminen (vain kisan luoja)
  $routes->post('/contest/:contestid/edit', 'check_logged_in', function($contestid){
    ContestController::update($contestid);
  });

  // Pelin lisääinen kisaan
  $routes->post('/contest/:contestid/add', 'check_logged_in', function($contestid){
    ContestController::add_game($contestid);
  });

  // Pelin poistaminen kisasta
  $routes->post('/contest/:contestid/remove', 'check_logged_in', function($contestid){
    ContestController::remove_game($contestid);
  });

  // Kisan poistaminen
  $routes->post('/contest/:contestid/destroy', 'check_logged_in', function($contestid){
    ContestController::destroy($contestid);
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

  // Add multiple csv files
  $routes->get('/csv_import', 'check_admin_logged_in', function(){
    GameController::csv_import_show();
  });

  // Process multiple csv files
  $routes->post('/csv_import', 'check_admin_logged_in', function(){
    GameController::csv_import_process();
  });
