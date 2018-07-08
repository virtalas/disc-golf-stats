<?php
  class PlayerController extends BaseController {

    public static function index() {
      $url = $_SERVER['REQUEST_URI'];
      $stripped_url = preg_replace("/[^A-Za-z0-9 ]/", '', $url);

      // Fetch page from cache
      $cached_page = Cache::getPage($stripped_url);

      if ($cached_page != null && Cache::on()) {
        // Use cached page (which is up to date because outdated pages are deleted)
        echo $cached_page;

      } else {
        // Make page from scratch

        $player = Player::find($_GET['player']);

        $game_count = Game::count_all_player_games($player->playerid);
        $latest_game = Game::latest_player_game($player->playerid);
        $latest_gameid = Game::latest_player_gameid($player->playerid);
        $high_scores = Game::player_high_scores($player->playerid);
        $years = Game::game_years();

        $popular_courses = Course::popular_courses($player->playerid);

        $throw_count = Score::count_all_player_scores($player->playerid);
        $birdies = Score::players_birdies($player->playerid);
        $aces = Score::players_aces($player->playerid);
        $eagles = Score::players_eagles($player->playerid);

        $courses_avg_scores = array();
        $names_done = false;

        foreach ($years as $year) {
          $avg_scores_by_year = Course::average_player_scoring_by_year($player->playerid, $year);

          for ($i = 0; $i < count($avg_scores_by_year); $i++) {
            if (!$names_done) {
              $averages = array();
              $averages[] = $avg_scores_by_year[$i]['name'];
              $courses_avg_scores[] = $averages;
            }

            if ($avg_scores_by_year[$i]['avg_score'] != null) {
              $courses_avg_scores[$i][] = $avg_scores_by_year[$i]['avg_score']. " (". str_replace(" ", "", $avg_scores_by_year[$i]['to_par']). ")";
            } else {
              $courses_avg_scores[$i][] = "";
            }
          }
          $names_done = true;
        }

        $page_html = View::make('player/index.html', array(
          'player' => $player,
          'players' => Player::all_without_guests(),
          'game_count' => $game_count,
          'throw_count' => $throw_count,
          'latest_game' => $latest_game,
          'latest_gameid' => $latest_gameid,
          'high_scores' => $high_scores,
          'popular_courses' => $popular_courses,
          'birdies' => $birdies,
          'aces' => $aces,
          'eagles' => $eagles,
          'years' => $years,
          'courses_avg_scores' => $courses_avg_scores
        ));

        if (Cache::on()) {
          Cache::store($stripped_url, $page_html);
        }
      }
    }

    public static function info() {
      View::make('player/info.html', array(
        'cached_files' => Cache::cached_files()
      ));
    }
  }
