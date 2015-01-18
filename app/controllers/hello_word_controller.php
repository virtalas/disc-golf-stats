<?php

  class HelloWorldController extends BaseController{

    public static function index(){
   	  View::make('home.html');
    }

    public static function sandbox(){
      // Testaa koodiasi täällä
      echo 'Hello World!';
    }
  }
