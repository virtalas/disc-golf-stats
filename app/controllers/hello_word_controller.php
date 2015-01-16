<?php

  class HelloWorldController extends BaseController{

    public static function index(){
   	  self::render_view('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      echo 'Hello World!';

      $query = DB::connection()->prepare('SELECT * FROM Player');
      $query->execute();

      print_r($query->fetch());
    }
  }
