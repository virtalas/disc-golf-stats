<?php
  class GameController extends BaseController {

    public static function index() {
      $page = isset($_GET['page']) && $_GET['page']  ? $_GET['page'] : 1;
      $page_size = 10;
      $games = array();
      $playerid = null;
      $courseid = null;

      $url = $_SERVER['REQUEST_URI'];
      $stripped_url = preg_replace("/[^A-Za-z0-9 ]/", '', $url);

      // Fetch page from cache
      $cached_page = Cache::getPage($stripped_url);

      if ($cached_page != null && Cache::on()) {
        // Use cached page (which is up to date because outdated pages are deleted)
        echo $cached_page;

      } else if (Game::count_all() > 0) {
        // Make page from scratch

        $game_years = Game::game_years();

        if (isset($_GET['year'])) {
          $year = $_GET['year'];
        } else {
          $year = $game_years[count($game_years) - 1];
        }

        // GET-parameters determine what games are shown:

        if (isset($_GET['player']) && isset($_GET['course'])) {
          // Show this player's games on this course
          $playerid = $_GET['player'];
          $courseid = $_GET['course'];
          $games_count = Game::count_all_player_games_on_course($playerid, $courseid, $year);
          $games = Game::all(array(
            'page' => $page,
            'page_size' => $page_size,
            'playerid' => $playerid,
            'courseid' => $courseid,
            'year' => $year
          ));

        } else if (isset($_GET['player'])) {
          // Show this player's games
          $playerid = $_GET['player'];
          $games_count = Game::count_all_player_games_by_year($playerid, $year);
          $games = Game::all(array(
            'page' => $page,
            'page_size' => $page_size,
            'playerid' => $playerid,
            'year' => $year
          ));

        } else if (isset($_GET['course'])) {
          // Show games on this course
          $courseid = $_GET['course'];
          $games_count = Game::count_all_course_games_by_year($courseid, $year);
          $games = Game::all(array(
            'page' => $page,
            'page_size' => $page_size,
            'courseid' => $courseid,
            'year' => $year
          ));

        } else {
          // Show all games
          $games_count = Game::count_all_by_year($year);
          $games = Game::all(array(
            'page' => $page,
            'page_size' => $page_size,
            'year' => $year
          ));
        }

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

        $page_html = View::make('game/index.html', array(
          'games' => $games,
          'prev_page' => $prev_page,
          'curr_page' => $page,
          'next_page' => $next_page,
          'pages' => $pages,
          'courses' => Course::all(),
          'course_name' => $courseid ? Course::find($courseid)->name : null,
          'players' => Player::all(),
          'players_without_guests' => Player::all_without_guests(),
          'playerid_param' => $playerid,
          'courseid_param' => $courseid,
          'game_years' => $game_years,
          'current_year' => $year
        ));

        if (Cache::on()) {
          // Don't include the page message in the cached file
          $page_html = Cache::strip_tags_content($page_html, "message-success");
          Cache::store($stripped_url, $page_html);
        }
      } else {
        // No games in the database
        View::make('game/index.html', array(
          'courses' => Course::all(),
          'players' => Player::all()
        ));
      }
    }

    public static function create() {
      $course = Course::find($_GET['course']);

      $attributes = array();
      $players = array();

      foreach (Player::all() as $player) {
        if (isset($_GET['player'. $player->playerid])) {
          $players[] = $player;
          $attributes["legal-player". $player->playerid] = 1;
        }
      }

      View::make('game/new.html', array(
        'course' => $course,
        'players' => $players,
        'attributes' => $attributes
      ));
    }

    public static function initMobileScoreCard() {
      // Check for adding guests
      if (isset($_GET['guests']) && $_GET['guests']) {
          GameController::createGuestsFromGET();
          $all_players = Player::all();
      } else {
          $all_players = Player::all_without_guests();
      }

      // Game index page "new game" button -> /game/mobile/new
      // Mobile gui will have a button to redirect to /game/new, but mobile gui is the default
      // This function will create a new game and redirect to the mobile gui for that game.
      $course = Course::find($_GET['course']);
      $course->prepare();
      $players = array();

      // Legality
      foreach ($all_players as $player) {
        if (isset($_GET['player'. $player->playerid])) {
            $players[] = $player;
            $_POST["legal-player". $player->playerid] = 0; // init with illegal game
        }
      }

      // Strokes and OB
      for ($i = 0; $i < sizeof($players); $i++) {
          $playerid = $players[$i]->playerid;
          for ($j = 0; $j < $course->number_of_holes; $j++) {
              // e.g. "player1-hole1"
              $_POST["player". $playerid. "-hole". ($j+1)] = 0;
              // e.g. "player1-obhole1"
              $_POST["player". $playerid. "-obhole". ($j+1)] = 0;
          }
      }

      // Players
      for ($i = 0; $i < sizeof($players); $i++) {
          $_POST["player". $players[$i]->playerid] = $players[$i]->playerid;
      }

      $_POST["date"] = date("Y-m-d");
      $_POST["time"] = date("H:i");
      $_POST["courseid"] = $course->courseid;

      // Create game in the database but don't redirect.
      $latest_gameid = Game::latest_gameid();
      GameController::store(False);
      $new_gameid = Game::latest_gameid();

      if ($new_gameid <= $latest_gameid) {
        Player::destroy_guests_with_no_games();
        Redirect::to('/game', array('error' => 'Peliä ei voitu luoda.'));
      } else {
        // Redirect to the mobile interface with the new game.
        Redirect::to("/game/mobile/". $new_gameid);
      }
    }

    public static function createGuestsFromGET() {
        $guestCount = isset($_GET['guestCount']) ? $_GET['guestCount'] : 0;
        $time = time();

        for ($i = 1; $i <= $guestCount; $i++) {
            $newPlayer = new Player(array(
                'firstname' => $_GET['guest'. $i],
                'username' => "guest_". date("Y-m-d_H-i-s_". $i, $time),
                'password' => "",
                'salt' => "",
                'guest' => True
            ));
            if ($newPlayer->firstname != "") $newPlayer->save();
            $_GET['player'. $newPlayer->playerid] = "1";
        }
    }

    public static function mobileScoreCard($gameid) {
        $game = Game::find($gameid);
        $game->prepare();

        // Passwords may contain special characters that disturb JSON.parse().
        $players = Game::games_players($gameid);
        foreach ($players as $player) {
            $player->password = "";
        }

        View::make('game/mobilegui/index.html', array(
          'game' => $game,
          'course' => Course::find($game->courseid),
          'players' => $players,
          'attributes' => array()
        ));
    }

    public static function store($redirect) {
      $params = $_POST;

      $player = self::get_user_logged_in();
      $rain = isset($_POST['rain']) && $_POST['rain']  ? "1" : "0"; // checked=1, unchecked=0
      $wet_no_rain = isset($_POST['wet_no_rain']) && $_POST['wet_no_rain']  ? "1" : "0"; // checked=1, unchecked=0
      $windy = isset($_POST['windy']) && $_POST['windy']  ? "1" : "0"; // checked=1, unchecked=0
      $variant = isset($_POST['variant']) && $_POST['variant']  ? "1" : "0"; // checked=1, unchecked=0
      $dark = isset($_POST['dark']) && $_POST['dark']  ? "1" : "0"; // checked=1, unchecked=0
      $led = isset($_POST['led']) && $_POST['led']  ? "1" : "0"; // checked=1, unchecked=0
      $snow = isset($_POST['snow']) && $_POST['snow']  ? "1" : "0"; // checked=1, unchecked=0
      $doubles = isset($_POST['doubles']) && $_POST['doubles']  ? "1" : "0"; // checked=1, unchecked=0
      $temp = isset($_POST['temp']) && $_POST['temp'] != ""  ? $_POST['temp'] : null; // temperature can be null (or 0!)
      $date = $_POST['date'];
      $time = $_POST['time'];
      $comment = isset($_POST['comment']) ? $_POST['comment'] : "";

      $courseid = $_POST['courseid'];
      $gamedate = $date. " ". $time. ":00";

      $game = new Game(array(
        'courseid' => $courseid,
        'creator' => $player->playerid,
        'gamedate' => $gamedate,
        'comment' => $comment,
        'rain' => $rain,
        'wet_no_rain' => $wet_no_rain,
        'windy' => $windy,
        'variant' => $variant,
        'dark' => $dark,
        'led' => $led,
        'snow' => $snow,
        'doubles' => $doubles,
        'temp' => $temp
      ));
      $errors = $game->errors();

      $course = Course::find($courseid);

      if (isset($_FILES['csv']['tmp_name'])) {
        // Scores will be read from a CSV file.
        $tmpName = $_FILES['csv']['tmp_name'];

        if (!empty($tmpName)) {
          $csvAsArray = array_map('str_getcsv', file($tmpName));
        } else {
          $errors[] = "Valitse CSV-tiedosto.";
        }

        if (count($errors) == 0) {
          // Game was valid
          $gameid = $game->save();

          // Read and save scores
          $score_errors = CSVScoreProcessor::process($csvAsArray, $gameid, $course);

          if (count($score_errors) == 0) {
            // Scores were valid as well

            // Clear cached pages
            Cache::clear();

            if ($redirect) {
                Redirect::to('/game/'. $game->gameid, array('message' => 'Peli ja sen tulokset lisätty.'));
            }
          } else if ($redirect) {
            // Scores had errors, nothing was saved
            $errors = array_merge($errors, $score_errors);
            View::make('game/new.html', array('errors' => $errors, 'attributes' => $params, 'course' => $course));
          }
        } else if ($redirect) {
          View::make('game/new.html', array('errors' => $errors, 'attributes' => $params, 'course' => $course));
        }
      } else {
        // Scores will be read from input forms.

        $scores = array();

        // Game's players
        $players = array();
        foreach (Player::all() as $player) {
          if (isset($_POST['player'. $player->playerid])) {
            $players[] = $player;
          }
        }

        // Cycle through players
        foreach ($players as $player) {
          $legal_index = 'legal-player'. $player->playerid;
          $legal = isset($_POST[$legal_index]) && $_POST[$legal_index]  ? "1" : "0"; // checked=1, unchecked=0

          foreach ($course->holes as $hole) {
            // inputs are in format 'player1-hole1'
            $stroke = $_POST['player'. $player->playerid. '-hole'. $hole->hole_num];
            $ob = $_POST['player'. $player->playerid. '-obhole'. $hole->hole_num];

            $score = new Score(array(
              'holeid' => $hole->holeid,
              'playerid' => $player->playerid,
              'stroke' => $stroke,
              'ob' => $ob,
              'legal' => $legal
            ));

            $errors = array_merge($errors, $score->errors());
            $scores[] = $score;
          }
        }

        if (count($errors) == 0) {
          // Game and scores were all valid
          $gameid = $game->save();

          foreach ($scores as $score) {
            $score->gameid = $gameid;
            $score->save();
          }

          // Clear cached pages
          Cache::clear();

          if ($redirect) {
              Redirect::to('/game/'. $game->gameid, array('message' => 'Peli ja sen tulokset lisätty.'));
          }
        } else  if ($redirect) {
          View::make('game/new.html', array(
            'errors' => $errors,
            'attributes' => $params,
            'course' => $course,
            'players' => $players
          ));
        }
      }
    }

    public static function edit($gameid) {
      $player = self::get_user_logged_in();
      $game = Game::find($gameid);
      $game->prepare();
      $players = Game::games_players($gameid);

      if ($game->creator != $player->playerid && !$player->admin) {
        Redirect::to('/game', array('message' => 'Vain pelin tekijä tai admin voi muokata peliä.'));
        return;
      }

      $attributes = array();
      foreach ($game->scores as $playerid => $scores) {
        // playerid is i.e. 'player4'
        $attributes["legal-". $playerid] = $scores[0]->legal;
        foreach ($scores as $score) {
          $attributes[$playerid. '-hole'. $score->hole_num] = $score->stroke;
          $attributes[$playerid. '-obhole'. $score->hole_num] = $score->ob;
        }
      }

      $gamedate = explode(' ', $game->gamedate);
      $date = $gamedate[0];
      $time = substr($gamedate[1], 0, 5);

      View::make('game/edit.html', array(
        'game' => $game,
        'date' => $date,
        'time' => $time,
        'players' => $players,
        'attributes' => $attributes,
        'course' => $game->course
      ));
    }

    public static function update($gameid, $redirect) {
      $attributes = $_POST;
      $player = self::get_user_logged_in();

      if (Game::get_creator($gameid) != $player->playerid && !$player->admin) {
        if ($redirect) {
            Redirect::to('/game', array('message' => 'Vain pelin tekijä tai admin voi muokata peliä.'));
        }
        return;
      }

      $rain = isset($_POST['rain']) && $_POST['rain']  ? "1" : "0"; // checked=1, unchecked=0
      $wet_no_rain = isset($_POST['wet_no_rain']) && $_POST['wet_no_rain']  ? "1" : "0"; // checked=1, unchecked=0
      $windy = isset($_POST['windy']) && $_POST['windy']  ? "1" : "0"; // checked=1, unchecked=0
      $variant = isset($_POST['variant']) && $_POST['variant']  ? "1" : "0"; // checked=1, unchecked=0
      $dark = isset($_POST['dark']) && $_POST['dark']  ? "1" : "0"; // checked=1, unchecked=0
      $led = isset($_POST['led']) && $_POST['led']  ? "1" : "0"; // checked=1, unchecked=0
      $snow = isset($_POST['snow']) && $_POST['snow']  ? "1" : "0"; // checked=1, unchecked=0
      $doubles = isset($_POST['doubles']) && $_POST['doubles']  ? "1" : "0"; // checked=1, unchecked=0
      $temp = isset($_POST['temp']) && $_POST['temp'] != ""  ? $_POST['temp'] : null; // temperature can be null (or 0!)
      $date = $_POST['date'];
      $time = $_POST['time'];
      $comment = isset($_POST['comment']) ? $_POST['comment'] : "";

      $courseid = $_POST['courseid'];
      $gamedate = $date. " ". $time. ":00";

      $game = new Game(array(
        'gameid' => $gameid,
        'courseid' => $courseid,
        'gamedate' => $gamedate,
        'comment' => $comment,
        'rain' => $rain,
        'wet_no_rain' => $wet_no_rain,
        'windy' => $windy,
        'variant' => $variant,
        'dark' => $dark,
        'led' => $led,
        'snow' => $snow,
        'doubles' => $doubles,
        'temp' => $temp
      ));
      $errors = $game->errors();

      $course = Course::find($courseid);

      Score::all_game_scores($game);
      $player_scores = $game->scores;

      // Cycle through players
      foreach ($player_scores as $playerid => $scores) {
        // playerid is i.e. 'player4'
        $legal_index = 'legal-'. $playerid;
        $legal = isset($_POST[$legal_index]) && $_POST[$legal_index]  ? "1" : "0"; // checked=1, unchecked=0
        foreach ($scores as $score) {
          // inputs are in format 'player1-hole1'
          $stroke = $_POST[$playerid. '-hole'. $score->hole_num];
          $ob = $_POST[$playerid. '-obhole'. $score->hole_num];

          $score->stroke = (int) $stroke;
          $score->ob = (int) $ob;
          $score->legal = $legal;

          $errors = array_merge($errors, $score->errors());
        }
      }

      if (count($errors) == 0) {
        // Game and scores were all valid
        $gameid = $game->update();

        foreach ($player_scores as $playerid => $scores) {
          foreach ($scores as $score) {
            $score->update();
          }
        }

        // Clear cached pages
        Cache::clear();

        if ($redirect) {
            Redirect::to('/game/'. $game->gameid, array('message' => 'Peli ja sen tulokset päivitetty.'));
        }
    } else if ($redirect) {
        $game->prepare();
        View::make('game/edit.html', array(
          'errors' => $errors,
          'game' => $game,
          'date' => $date,
          'time' => $time,
          'players' => Game::games_players($gameid),
          'attributes' => $attributes,
          'course' => $game->course
        ));
      }
    }

    public static function show($gameid) {
      $game = Game::find_format_gamedate($gameid);
      $games = array($game);

      View::make('game/show.html', array(
        'games' => $games,
        'courses' => Course::all(),
        'players' => Player::all(),
        'players_without_guests' => Player::all_without_guests()
      ));
    }

    public static function export($gameid) {
      $content = "";
      $game = Game::find($gameid);

      foreach ($game->scores as $playerid => $scores) {
        $player = Player::find(substr($playerid, 6));
        $content .= $player->firstname;

        // strokes
        foreach ($scores as $score) {
          $content .= ";". $score->stroke;
        }

        // OBs
        $content .= ";x;x;";
        $first = true;
        foreach ($scores as $score) {
          if ($first) {
            $content .= $score->ob;
            $first = false;
          } else {
            $content .= ",". $score->ob;
          }
        }

        $content .= "\n";
      }

      $content .= "\n\n";

      // Other game information

      if (!empty($game->illegal_scorers)) {
        $content .= "* Laiton peli: ". $game->illegal_scorers;
        $content .= "\n";
      }
      if (!empty($game->high_scorers)) {
        $content .= "* Ennätys: ". $game->high_scorers;
        $content .= "\n";
      }
      if (!empty($game->conditions)) {
        $content .= "* Olosuhteet: ". $game->conditions;
        $content .= "\n";
      }
      if (!empty($game->weather)) {
        $content .= "* Sää: ". $game->weather;
        $content .= "\n";
      }
      if (!empty($game->comment)) {
        $content .= "* Kommentit: ". $game->comment;
        $content .= "\n";
      }

      $name = $game->gamedate. " ". $game->course->name;

      // Download file
      header('Content-type: application/txt');
      header('Content-Disposition: attachment; filename='. $name. '.txt');
      echo $content;
      exit;
}

    // Import multiple csv files
    public static function csv_import_show() {
      View::make('game/csv_import.html');
    }

    // Process multiple csv files
    // Correct course assigning will work ONLY IF (!) all course names are unique
    // and csv filenames are like '2014-10-30 Coursename.csv'
    public static function csv_import_process() {
      $courses = Course::all();
      $file_count = count($_FILES['file']['tmp_name']);

      // Cycle through files
      for ($i = 0; $i < $file_count; $i++) {
        $original_filename = $_FILES['file']['name'][$i];
        echo "<h3>Adding file: ". $original_filename. "</h3>";

        // Find course name
        $course_name = "NOT FOUND";
        $game_course = null;
        foreach ($courses as $course) {
          // Check if filename contains coursename
          if (strpos($original_filename, $course->name) !== false) {
              $course_name = $course->name;
              $game_course = $course;
          }
        }

        // Game date
        $gamedate = substr($original_filename, 0, 11);
        $gamedate .= " 00:00:00";

        echo "Course name: ". $course_name. "<br>";
        echo "Game date: ". $gamedate. "<br>";

        // Create game
        $game = new Game(array(
          'courseid' => $game_course->courseid,
          'gamedate' => $gamedate,
          'rain' => 0,
          'wet_no_rain' => 0,
          'windy' => 0,
          'variant' => 0,
          'dark' => 0,
          'led' => 0,
          'snow' => 0,
          'doubles' => 0
        ));
        $gameid = $game->save();

        // Create scores
        $csvAsArray = array_map('str_getcsv', file($_FILES['file']['tmp_name'][$i]));
        $score_errors = CSVScoreProcessor::process($csvAsArray, $gameid, $game_course);

        if (count($score_errors)) {
          echo "ERRORS: ";
          print_r($score_errors);
        }

        echo "<br>Done!";
        echo "<br>";
      }

      // Clear cached pages
      Cache::clear();
    }

    // Used for displaying score card images that are not in the database
    public static function display_score_card_pictures_by_year($year) {
      $full_path = getcwd();
      $path = str_replace("/disc-golf-stats/app/controllers", "", $full_path);
      $path = str_replace("/xamppfiles", "", $path);

      $echo = ImageDisplayer::display($path. "/score_cards", $year);

      View::make('game/score_card_images.html', array(
        'current_year' => $year,
        'images' => $echo,
        'players' => Player::all(),
        'courses' => Course::all(),
        'game_years' => Game::game_years()
      ));
    }

    public static function destroy($gameid) {
      $player = self::get_user_logged_in();

      if (Game::get_creator($gameid) != $player->playerid && !$player->admin) {
        Redirect::to('/game', array('message' => 'Vain pelin tekijä tai admin voi poistaa pelin.'));
        return;
      }

      self::destroy_no_redirect($gameid);

      // Clear cached pages
      Cache::clear();

      Redirect::to('/game', array('message' => 'Peli ja sen tulokset poistettu.'));
    }

    public static function destroy_no_redirect($gameid) {
      // Destroy both the game and its scores.
      $game = Game::find($gameid);
      $player_scores = $game->scores;

      foreach ($player_scores as $playername => $scores) {
        foreach ($scores as $score) {
          $score->destroy();
        }
      }

      $game->destroy();

      // Destroy possible guests in the game
      Player::destroy_guests_with_no_games();

      // Clear cached pages
      Cache::clear();
    }
  }
