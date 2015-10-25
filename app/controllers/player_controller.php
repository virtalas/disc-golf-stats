<?php
  class PlayerController extends BaseController {

    public static function index() {
      View::make('player/index.html');
    }
  }
