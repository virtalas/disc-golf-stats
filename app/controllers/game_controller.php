<?php
  class GameController extends BaseController {

    public function index() {
      $player = self::get_user_logged_in();

      $games = Game::all_player_games($player->playerid);
      $scores = array();
      $holes = array();

      foreach ($games as $game) {
        $game->prepare();
      }

      View::make('game/index.html', array(
        'games' => $games,
        'courses' => Course::all()
      ));
    }

    public static function create() {
      $params = $_GET;
      $course = Course::find($params['course']);
      $course->prepare();

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
      $course->prepare();
      $scores = array();
      // When implementing multiple players per game, cycle through playerid's here
      $playerid = $_POST['playerid'];

      foreach ($course->holes as $hole) {
        $stroke = $_POST['hole'. $hole->hole_num];
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

        Redirect::to('/game'. $course->courseid, array('message' => 'Peli ja sen tulokset lisätty.'));
      } else {
        View::make('game/new.html', array('errors' => $errors, 'attributes' => $params, 'course' => $course));
      }
    }
  }
