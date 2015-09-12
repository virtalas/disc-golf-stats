<?php

  class HelloWorldController extends BaseController {

    public static function index() {
      // make-metodi renderöi app/views-kansiossa sijaitsevia tiedostoja
   	  // View::make('home.html');
      echo "Etusivu";
    }

    public static function stats() {
      View::make('suunnitelmat/stats.html');
    }

    public static function games() {
      View::make('suunnitelmat/games.html');
    }

    public static function courses() {
      View::make('suunnitelmat/courses.html');
    }

    public static function sign_in() {
      View::make('suunnitelmat/sign_in.html');
    }

    public static function sandbox() {
      // Testaa koodiasi täällä
      View::make('helloworld.html');
    }

    public static function course_info() {
      View::make('suunnitelmat/course_info.html');
    }

    public static function game_edit() {
      View::make('suunnitelmat/game_edit.html');
    }

    public static function course_edit() {
      View::make('suunnitelmat/course_edit.html');
    }
  }
