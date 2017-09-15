<?php
  class SearchController extends BaseController {

    public static function index() {
      View::make('search/index.html', array(
        'courses' => Course::all()
      ));
    }

    public static function search() {
      $page = isset($_GET['page']) && $_GET['page']  ? $_GET['page'] : 1;
      $page_size = 15;
      $conditions = array();

      if (isset($_GET['courseid']) && $_GET['courseid'] != -1) {
        $conditions['courseid'] = $_GET['courseid'];
      }

      foreach ($_GET as $key => $value) {
        switch ($value) {
          case 'Kyllä':
            $conditions[$key] = 'true';
            break;
          case 'Ei':
            $conditions[$key] = 'false';
            break;
        }
      }

      $games = Game::search($conditions, $page, $page_size);
      $game_count = Game::count_all_for_search($conditions, $page, $page_size);
      $pages = ceil($game_count/$page_size);

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

      View::make('search/search.html', array(
        'games' => $games,
        'prev_page' => $prev_page,
        'curr_page' => $page,
        'next_page' => $next_page,
        'pages' => $pages,
        'game_count' => $game_count,
        'players' => Player::all()
      ));
    }
  }
