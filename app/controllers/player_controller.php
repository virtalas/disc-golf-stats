<?php
  class PlayerController extends BaseController {

    public static function index() {
      $player = Player::find($_GET['player']);

      $game_count = Game::count_all_player_games($player->playerid);
      $throw_count = Score::count_all_player_scores($player->playerid);
      $latest_game = Game::latest_player_game($player->playerid);
      $high_scores = Game::player_high_scores($player->playerid);
      $popular_courses = Course::popular_courses($player->playerid);

      View::make('player/index.html', array(
        'player' => $player,
        'players' => Player::all(),
        'game_count' => $game_count,
        'throw_count' => $throw_count,
        'latest_game' => $latest_game,
        'high_scores' => $high_scores,
        'popular_courses' => $popular_courses
      ));
    }
  }
