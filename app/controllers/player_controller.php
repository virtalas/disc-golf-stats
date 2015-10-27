<?php
  class PlayerController extends BaseController {

    public static function index() {
      $player = Player::find($_GET['player']);

      $game_count = Game::count_all_player_games($player->playerid);
      $latest_game = Game::latest_player_game($player->playerid);
      $high_scores = Game::player_high_scores($player->playerid);
      $years = Game::game_years();

      $popular_courses = Course::popular_courses($player->playerid);

      $throw_count = Score::count_all_player_scores($player->playerid);
      $birdies = Score::players_birdies($player->playerid);
      $aces = Score::players_aces($player->playerid);

      $courses_avg_scores = array();

      foreach (Course::player_courses($player->playerid) as $course) {
        $averages = array();
        $averages[] = $course->name;

        foreach (Game::game_years() as $year) {
          $averages[] = Course::average_player_scoring_by_year($course->courseid, $player->playerid, $year);
        }

        $courses_avg_scores[] = $averages;
      }

      View::make('player/index.html', array(
        'player' => $player,
        'players' => Player::all(),
        'game_count' => $game_count,
        'throw_count' => $throw_count,
        'latest_game' => $latest_game,
        'high_scores' => $high_scores,
        'popular_courses' => $popular_courses,
        'birdies' => $birdies,
        'aces' => $aces,
        'years' => $years,
        'courses_avg_scores' => $courses_avg_scores
      ));
    }

    public static function info() {
      View::make('player/info.html');
    }
  }
