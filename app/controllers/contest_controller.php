<?php

  class ContestController extends BaseController {

    public static function index() {
      $contests = Contest::all_with_games_played();
      View::make('contest/index.html', array('contests' => $contests));
    }

    public static function create() {
      View::make('contest/new.html');
    }

    public static function store() {
      $params = $_POST;
      $player = self::get_user_logged_in();

      $contest_params = array(
        'creator' => $player->playerid,
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

    public static function show($contestid) {
      $contest = Contest::find($contestid);
      $latest_games = Game::five_latest_games();
      $games = Game::contest_games($contestid);
      $points = Contest::points($contest, $games);
      $players = Player::all();

      View::make('contest/show.html', array('contest' => $contest,
                                            'latest_games' => $latest_games,
                                            'games' => $games,
                                            'players' => $players,
                                            'points' => $points));
    }

    public static function edit($contestid) {
      $contest = Contest::find($contestid);
      $player = self::get_user_logged_in();

      $attributes = array(
        'contestid' => $contest->contestid,
        'creator' => $contest->creator,
        'name' => $contest->name,
        'number_of_games' => $contest->number_of_games
      );

      // Only the creator of the contest can view this page
      if ($contest->creator == $player->playerid) {
        View::make("contest/edit.html", array('attributes' => $attributes));
      } else {
        Redirect::to('/');
      }
    }

    public static function update($contestid) {
      $params = $_POST;
      $player = self::get_user_logged_in();

      $contest_params = array(
        'contestid' => $contestid,
        'name' => $params['name'],
        'number_of_games' => $params['number_of_games']
      );

      $contest = new Contest($contest_params);
      $errors = $contest->errors();

      // Only the creator of the contest can update
      if ($contest->is_creator($player)) {
        $errors[] = "Vain kisan luoja voi muokata sitä.";
      }

      if (count($errors) == 0) {
        // Contest was valid
        $contest->update();

        // Clear cached pages
        Cache::clear();

        Redirect::to('/contest/'. $contestid, array('message' => 'Kisan tiedot päivitetty.'));
      } else {
        View::make('course/edit.html', array('errors' => $errors, 'attributes' => $params));
      }
    }

    public static function add_game($contestid) {
      $gameid = $_POST['gameid'];
      $player = self::get_user_logged_in();

      $contest = Contest::find($contestid);
      $game = Game::find($gameid);

      if ($contest->is_creator($player)) {
        $game->contestid = (int) $contestid;
        $game->update();
        Redirect::to('/contest/'. $contestid, array('message' => 'Peli lisätty kisaan.'));
      } else {
        Redirect::to('/contest/'. $contestid, array('message' => 'Vain kisan luoja voi lisätä siihen pelejä.'));
      }
    }
  }
