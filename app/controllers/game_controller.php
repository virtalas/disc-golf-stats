<?php
  class GameController extends BaseController {

    public static function index() {
      $page = isset($_GET['page']) && $_GET['page']  ? $_GET['page'] : 1;
      $player = self::get_user_logged_in();
      $games_count = Game::count_all_player_games($player->playerid);
      $page_size = 10;
      $pages = ceil($games_count/$page_size);

      $games = Game::all(array(
        'playerid' => $player->playerid,
        'page' => $page,
        'page_size' => $page_size
      ));

      if ($page > 1) {
        $prev_page = (int)$page - 1;
      } else {
        $prev_page = null;
      }

      if ($pages > $page) {
        $next_page = (int)$page + 1;
      } else {
        $next_page = null;
      }

      View::make('game/index.html', array(
        'games' => $games,
        'courses' => Course::all(),
        'prev_page' => $prev_page,
        'curr_page' => $page,
        'next_page' => $next_page,
        'pages' => $pages
      ));
    }

    public static function create() {
      $params = $_GET;
      $course = Course::find($params['course']);

      View::make('game/new.html', array('course' => $course));
    }

    public static function store() {
      $params = $_POST;

      $rain = isset($_POST['rain']) && $_POST['rain']  ? "1" : "0"; // checked=1, unchecked=0
      $wet_no_rain = isset($_POST['wet_no_rain']) && $_POST['wet_no_rain']  ? "1" : "0"; // checked=1, unchecked=0
      $windy = isset($_POST['windy']) && $_POST['windy']  ? "1" : "0"; // checked=1, unchecked=0
      $variant = isset($_POST['variant']) && $_POST['variant']  ? "1" : "0"; // checked=1, unchecked=0
      $dark = isset($_POST['dark']) && $_POST['dark']  ? "1" : "0"; // checked=1, unchecked=0
      $led = isset($_POST['led']) && $_POST['led']  ? "1" : "0"; // checked=1, unchecked=0
      $snow = isset($_POST['snow']) && $_POST['snow']  ? "1" : "0"; // checked=1, unchecked=0
      $date = $_POST['date'];
      $time = $_POST['time'];
      $comment = $_POST['comment'];

      $courseid = $_POST['courseid'];
      $gamedate = $date. " ". $time. ":00";

      $game = new Game(array(
        'courseid' => $courseid,
        'gamedate' => $gamedate,
        'comment' => $comment,
        'rain' => $rain,
        'wet_no_rain' => $wet_no_rain,
        'windy' => $windy,
        'variant' => $variant,
        'dark' => $dark,
        'led' => $led,
        'snow' => $snow
      ));
      $errors = $game->errors();

      $course = Course::find($courseid);
      $scores = array();
      // When implementing multiple players per game, cycle through playerid's here
      $playerid = $_POST['playerid'];

      foreach ($course->holes as $hole) {
        $stroke = $_POST['hole'. $hole->hole_num]; // 'holeN' will be something like 'playername-holeN'
        $ob = $_POST['obhole'. $hole->hole_num];

        $score = new Score(array(
          'holeid' => $hole->holeid,
          'playerid' => $playerid,
          'stroke' => $stroke,
          'ob' => $ob
        ));

        $errors = array_merge($errors, $score->errors());
        $scores[] = $score;
      }

      if (count($errors) == 0) {
        // Game and scores were all valid
        $gameid = $game->save();

        foreach ($scores as $score) {
          $score->gameid = $gameid;
          $score->save();
        }

        Redirect::to('/game/'. $game->gameid, array('message' => 'Peli ja sen tulokset lisätty.'));
      } else {
        View::make('game/new.html', array('errors' => $errors, 'attributes' => $params, 'course' => $course));
      }
    }

    public static function edit($gameid) {
      $game = Game::find($gameid);

      $gamedate = explode(' ', $game->gamedate);
      $date = $gamedate[0];
      $time = substr($gamedate[1], 0, 5);

      View::make('game/edit.html', array('game' => $game, 'date' => $date, 'time' => $time));
    }

    public static function update($gameid) {
      $rain = isset($_POST['rain']) && $_POST['rain']  ? "1" : "0"; // checked=1, unchecked=0
      $wet_no_rain = isset($_POST['wet_no_rain']) && $_POST['wet_no_rain']  ? "1" : "0"; // checked=1, unchecked=0
      $windy = isset($_POST['windy']) && $_POST['windy']  ? "1" : "0"; // checked=1, unchecked=0
      $variant = isset($_POST['variant']) && $_POST['variant']  ? "1" : "0"; // checked=1, unchecked=0
      $dark = isset($_POST['dark']) && $_POST['dark']  ? "1" : "0"; // checked=1, unchecked=0
      $led = isset($_POST['led']) && $_POST['led']  ? "1" : "0"; // checked=1, unchecked=0
      $snow = isset($_POST['snow']) && $_POST['snow']  ? "1" : "0"; // checked=1, unchecked=0
      $date = $_POST['date'];
      $time = $_POST['time'];
      $comment = $_POST['comment'];

      $courseid = $_POST['courseid'];
      $gamedate = $date. " ". $time. ":00";

      $game = new Game(array(
        'gameid' => $gameid,
        'courseid' => $courseid,
        'gamedate' => $gamedate,
        'comment' => $comment,
        'rain' => $rain,
        'wet_no_rain' => $wet_no_rain,
        'windy' => $windy,
        'variant' => $variant,
        'dark' => $dark,
        'led' => $led,
        'snow' => $snow
      ));
      $errors = $game->errors();

      $course = Course::find($courseid);
      // When implementing multiple players per game, cycle through playerid's here
      $playerid = $_POST['playerid'];

      $player_scores = Score::all_game_scores($gameid);
      foreach ($player_scores as $playername => $scores) {
        foreach ($scores as $score) {
          $stroke = $_POST['hole'. $score->hole->hole_num]; // 'holeN' will be something like 'playername-holeN'
          $ob = $_POST['obhole'. $score->hole->hole_num];

          $score->stroke = $stroke;
          $score->ob = $ob;

          $errors = array_merge($errors, $score->errors());
        }
      }

      if (count($errors) == 0) {
        // Game and scores were all valid
        $gameid = $game->update();

        foreach ($player_scores as $playername => $scores) {
          foreach ($scores as $score) {
            $score->update();
          }
        }

        Redirect::to('/game/'. $game->gameid, array('message' => 'Peli ja sen tulokset päivitetty.'));
      } else {
        View::make('game/edit.html', array('errors' => $errors, 'game' => $game, 'date' => $date, 'time' => $time));
      }
    }

    public static function show($gameid) {
      $game = Game::find_format_gamedate($gameid);
      $games = array($game);

      View::make('game/show.html', array(
        'games' => $games,
        'courses' => Course::all()
      ));
    }

    public static function destroy($gameid) {
      self::destroy_no_redirect($gameid);

      Redirect::to('/game', array('message' => 'Peli ja sen tulokset poistettu.'));
    }

    public static function destroy_no_redirect($gameid) {
      // Destroy both the game and its scores.
      $game = Game::find($gameid);
      $player_scores = $game->scores;

      foreach ($player_scores as $playername => $scores) {
        foreach ($scores as $score) {
          $score->destroy();
        }
      }

      $game->destroy();
    }
  }
