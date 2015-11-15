<?php

  class StatsController extends BaseController {

    public static function stats() {
      $player = self::get_user_logged_in();
      $url = $_SERVER['REQUEST_URI'];
      $stripped_url = preg_replace("/[^A-Za-z0-9 ]/", '', $url);


      if ($player) {
        $stripped_url .= $player->playerid;
      }

      // Fetch page from cache
      $cached_page = Cache::getPage($stripped_url);

      if ($cached_page != null && Cache::on()) {
        // Use cached page (which is up to date because outdated pages are deleted)
        echo $cached_page;

      } else {
        // Make page from scratch

        $high_scores = null;
        if ($player) {
          $high_scores = Game::player_high_scores($player->playerid);
        }

        $game_count = Game::count_all();
        $throw_count = Score::count_all();
        $latest_game = Game::latest_game();
        $popular_courses = Course::popular_courses_all_players();

        $page_html = View::make("stats/stats.html", array(
          'game_count' => $game_count,
          'throw_count' => $throw_count,
          'latest_game' => $latest_game,
          'popular_courses' => $popular_courses,
          'player' => $player,
          'high_scores' => $high_scores
        ));

        // Don't include the page message in the cached file
        $page_html = Cache::strip_tags_content($page_html, "message-success");

        Cache::store($stripped_url, $page_html);
      }
    }
  }
