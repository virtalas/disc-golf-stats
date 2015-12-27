<?php
  class GraphController extends BaseController {

    public static function index() {
      View::make('graphs/index.html', array(
        'gamedates' => Game::all_game_dates(),
        'player_count_dist' => Game::player_count_distribution()
      ));
    }
  }
