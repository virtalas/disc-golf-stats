<?php
  class GraphController extends BaseController {

    public static function index() {
      $player = self::get_user_logged_in();

      View::make('graphs/index.html', array(
        'gamedates' => Game::all_game_dates(),
        'player_gamedates' => Game::all_game_dates($player->playerid),
        'player_count_dist' => Game::player_count_distribution(),
        'game_hours' => Game::game_hours_and_weekdays(),
        'player_course_popularity' => Course::player_course_popularity($player->playerid),
        'course_popularity' => Course::course_popularity()
      ));
    }
  }
