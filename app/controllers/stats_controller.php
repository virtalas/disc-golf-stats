<?php

  class StatsController extends BaseController {

    public static function stats() {
      $player = self::get_user_logged_in();

      $high_scores = null;
      if ($player) {
        $high_scores = Game::player_high_scores($player->playerid);
      }

      $game_count = Game::count_all();
      $throw_count = Score::count_all();
      $latest_game = Game::latest_game();
      $popular_courses = Course::popular_courses_all_players();

      View::make("stats/stats.html", array(
        'game_count' => $game_count,
        'throw_count' => $throw_count,
        'latest_game' => $latest_game,
        'popular_courses' => $popular_courses,
        'player' => $player,
        'high_scores' => $high_scores
      ));
    }
  }
