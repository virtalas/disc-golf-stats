<?php

  class StatsController extends BaseController {

    public static function stats() {
      $game_count = Game::count_all();
      $throw_count = Score::count_all();
      $latest_game = Game::latest_game();

      $player_game_count = null;
      $player_throw_count = null;
      $player_latest_game = null;
      $high_scores = null;

      if (isset($_SESSION['user'])) {
        $playerid = $_SESSION['user'];

        $player_game_count = Game::count_all_player_games($playerid);
        $player_throw_count = Score::count_all_player_scores($playerid);
        $player_latest_game = Game::latest_player_game($playerid);
        $high_scores = Game::player_high_scores($playerid);
      }

      View::make("stats/stats.html", array(
        'game_count' => $game_count,
        'throw_count' => $throw_count,
        'latest_game' => $latest_game,
        'player_game_count' => $player_game_count,
        'player_throw_count' => $player_throw_count,
        'player_latest_game' => $player_latest_game,
        'high_scores' => $high_scores
      ));
    }
  }
