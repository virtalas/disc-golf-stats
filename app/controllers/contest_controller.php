<?php

  class ContestController extends BaseController {

    public static function index() {
      $contests = Contest::all();

      View::make('contest/index.html', array(
        'contests' => $contests
      ));
    }

    public static function create() {
      View::make('contest/new.html');
    }

    public static function store() {
      $params = $_POST;

      $contest_params = array(
        'name' => $params['name'],
        'number_of_games' => $params['number_of_games']
      );

      $contest = new Contest($contest_params);
      $errors = $contest->errors();

      if (count($errors) == 0) {
        // Contest attributes were valid
        $contestid = $contest->save();

        // Clear cached pages
        Cache::clear();

        Redirect::to('/contest/'. $contestid, array('message' => 'Kisa lisätty.'));
      } else {
        View::make('contest/new.html', array('errors' => $errors, 'attributes' => $params));
      }
    }
  }
