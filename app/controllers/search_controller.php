<?php
  class SearchController extends BaseController {

    public static function index() {
      View::make('search/index.html', array(

      ));
    }

    public static function search() {
      $page = isset($_GET['page']) && $_GET['page']  ? $_GET['page'] : 1;
      $page_size = 15;
      $games_count = 100;

      $pages = ceil($games_count/$page_size);

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

      $conditions = array();

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

      View::make('search/search.html', array(
        'games' => Game::search($conditions, $page, $page_size),
        'prev_page' => $prev_page,
        'curr_page' => $page,
        'next_page' => $next_page,
        'pages' => $pages,
        'players' => Player::all()
      ));
    }
  }
