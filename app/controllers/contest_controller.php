<?php

  class ContestController extends BaseController {

    public static function index() {
      View::make('contest/index.html');
    }
  }
