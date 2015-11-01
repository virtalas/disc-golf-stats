<?php
  class Game extends BaseModel {

    public $gameid, $courseid, $gamedate, $comment, $rain, $wet_no_rain, $windy, // Ready to use after creation
            $variant, $dark, $led, $snow, $doubles, // Ready to use after creation
            $course, $scores, $conditions, $illegal_scorers, $high_scorers; // Need to be prepared via prepare_var()

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_date', 'validate_rain_and_wet_no_rain');
    }

    public function save() {
      $sql = "INSERT INTO game (courseid, gamedate, comment, rain, wet_no_rain,
              windy, variant, dark, led, snow, doubles)
              VALUES (:courseid, :gamedate, :comment, :rain, :wet_no_rain, :windy,
              :variant, :dark, :led, :snow, :doubles) RETURNING gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $this->courseid,
                            'gamedate' => $this->gamedate,
                            'comment' => $this->comment,
                            'rain' => $this->rain,
                            'wet_no_rain' => $this->wet_no_rain,
                            'windy' => $this->windy,
                            'variant' => $this->variant,
                            'dark' => $this->dark,
                            'led' => $this->led,
                            'snow' => $this->snow,
                            'doubles' => $this->doubles
                            ));
      $row = $query->fetch();
      $this->gameid = $row['gameid'];

      return $this->gameid;
    }

    public function update() {
      $sql = "UPDATE game SET gamedate = :gamedate, comment = :comment, rain = :rain,
              wet_no_rain = :wet_no_rain, windy = :windy, variant = :variant,
              dark = :dark, led = :led, snow = :snow, doubles = :doubles
              WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid,
                            'gamedate' => $this->gamedate,
                            'comment' => $this->comment,
                            'rain' => $this->rain,
                            'wet_no_rain' => $this->wet_no_rain,
                            'windy' => $this->windy,
                            'variant' => $this->variant,
                            'dark' => $this->dark,
                            'led' => $this->led,
                            'snow' => $this->snow,
                            'doubles' => $this->doubles
                            ));
      return $this->gameid;
    }

    public function destroy() {
      $sql = "DELETE FROM game WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid));
    }

    public function prepare() {
      $this->load_course();
      $this->load_scores();
      $this->load_conditions();
      $this->load_illegal_scorers();
      $this->load_high_scorers();
    }

    private function load_course() {
      $this->course = Course::find($this->courseid);
      $this->course->prepare();
    }

    private function load_scores() {
      $this->scores = Score::all_game_scores($this->gameid);
    }

    private function load_conditions() {
      $conditions_array = array();

      if ($this->windy) {
        array_push($conditions_array, "tuulista");
      }
      if ($this->rain) {
        array_push($conditions_array, "sadetta");
      }
      if ($this->wet_no_rain) {
        array_push($conditions_array, "märkää (ei sadetta)");
      }
      if ($this->variant) {
        array_push($conditions_array, "poikkeava rata");
      }
      if ($this->dark) {
        array_push($conditions_array, "pimeää");
      }
      if ($this->led) {
        array_push($conditions_array, "LED");
      }
      if ($this->snow) {
        array_push($conditions_array, "lunta");
      }
      if ($this->doubles) {
        array_push($conditions_array, "parigolf");
      }

      $i = 1;
      $condtitions_string = "";
      foreach ($conditions_array as $condition) {
        $condtitions_string .= $condition;
        if ($i < count($conditions_array)) {
          $condtitions_string .= ", ";
        }
        $i++;
      }

      $this->conditions = $condtitions_string;
    }

    public function load_illegal_scorers() {
      $illegal_scorers_array = array();

      foreach ($this->scores as $playerid => $scores) {
        // playerid is i.e. 'player4'
        if (!$scores[0]->legal) {
          $playerid_int = (int) str_replace("player", "", $playerid);
          $player = Player::find($playerid_int);
          $illegal_scorers_array[] = $player->firstname;
        }
      }

      $i = 1;
      $illegal_scorers_string = "";
      foreach ($illegal_scorers_array as $illegal_scorer) {
        $illegal_scorers_string .= $illegal_scorer;
        if ($i < count($illegal_scorers_array)) {
          $illegal_scorers_string .= ", ";
        }
        $i++;
      }

      $this->illegal_scorers = $illegal_scorers_string;
    }

    public function load_high_scorers() {
      $high_scorers_array = array();

      foreach ($this->scores as $playerid => $scores) {
        // playerid is i.e. 'player4'
        $playerid_int = (int) str_replace("player", "", $playerid);
        if (Score::new_high_score($this->gameid, $playerid_int, $this->courseid)) {
          $player = Player::find($playerid_int);
          $high_scorers_array[] = $player->firstname;
        }
      }

      $i = 1;
      $high_scorers_string = "";
      foreach ($high_scorers_array as $high_scorer) {
        $high_scorers_string .= $high_scorer;
        if ($i < count($high_scorers_array)) {
          $high_scorers_string .= ", ";
        }
        $i++;
      }

      $this->high_scorers = $high_scorers_string;
    }

    public static function game_years() {
      $sql = "SELECT DISTINCT to_char(game.gamedate, 'YYYY') as year FROM game ORDER BY year ASC";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();
      $years = array();

      foreach ($rows as $row) {
        $years[] = $row['year'];
      }

      return $years;
    }

    public static function all_player_games($playerid) {
      $sql = "SELECT gameid, courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
              comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles
              FROM game
              WHERE gameid IN
              (SELECT gameid FROM score WHERE playerid = :playerid)
              ORDER BY game.gamedate DESC";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $rows = $query->fetchAll();

      return self::get_games_from_rows($rows);
    }

    public static function all($options) {
      // if (isset($options['page']) && isset($options['page_size'])) {
      //   $page_size = $options['page_size'];
      //   $page = $options['page'];
      // } else {
      //   $page_size = 10;
      //   $page = 1;
      // }
      $page_size = $options['page_size'];
      $page = $options['page'];
      $year = $options['year'];
      $offset = (int)$page_size * ((int)$page - 1);

      // 'course' and 'player' parameters determine what games are fetched

      if (isset($options['playerid']) && isset($options['courseid'])) {
        // Fetch this player's games on this course
        $playerid = $options['playerid'];
        $courseid = $options['courseid'];

        $sql = "SELECT game.gameid, game.courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                game.comment, game.rain, game.wet_no_rain, game.windy, game.variant, game.dark, game.led,
                game.snow, game.doubles
                FROM game
                JOIN score ON score.gameid = game.gameid
                WHERE score.playerid = :playerid
                AND game.courseid = :courseid
                AND to_char(gamedate, 'YYYY') = :year
                GROUP BY game.gameid
                ORDER BY game.gamedate DESC
                LIMIT :limit OFFSET :offset";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('playerid' => $playerid,
                              'courseid' => $courseid,
                              'limit' => $page_size,
                              'offset' => $offset,
                              'year' => $year));
        $rows = $query->fetchAll();

        return self::get_games_from_rows($rows);

      } else if (isset($options['playerid'])) {
        // Fetch only this player's games
        $playerid = $options['playerid'];
        $sql = "SELECT game.gameid, game.courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                game.comment, game.rain, game.wet_no_rain, game.windy, game.variant, game.dark, game.led,
                game.snow, game.doubles
                FROM game
                JOIN score ON score.gameid = game.gameid
                WHERE score.playerid = :playerid
                AND to_char(gamedate, 'YYYY') = :year
                GROUP BY game.gameid
                ORDER BY game.gamedate DESC
                LIMIT :limit OFFSET :offset";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('playerid' => $playerid,
                              'limit' => $page_size,
                              'offset' => $offset,
                              'year' => $year));
        $rows = $query->fetchAll();

        return self::get_games_from_rows($rows);

      } else if (isset($options['courseid'])) {
        // Fetch this games on this course
        $courseid = $options['courseid'];
        $sql = "SELECT gameid, courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles
                FROM game
                WHERE courseid = :courseid
                AND to_char(gamedate, 'YYYY') = :year
                ORDER BY game.gamedate DESC
                LIMIT :limit OFFSET :offset";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('courseid' => $courseid,
                              'limit' => $page_size,
                              'offset' => $offset,
                              'year' => $year));

        $rows = $query->fetchAll();

        return self::get_games_from_rows($rows);

      } else {
        // Fetch all games
        $sql = "SELECT gameid, courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles
                FROM game
                WHERE to_char(gamedate, 'YYYY') = :year
                ORDER BY game.gamedate DESC
                LIMIT :limit OFFSET :offset";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('limit' => $page_size,
                              'offset' => $offset,
                              'year' => $year));

        $rows = $query->fetchAll();

        return self::get_games_from_rows($rows);
      }
    }

    public static function games_players($gameid) {
      $sql = "SELECT DISTINCT playerid FROM score WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $gameid));
      $rows = $query->fetchAll();
      $players = array();

      foreach ($rows as $row) {
        $players[] = Player::find($row['playerid']);
      }

      return $players;
    }

    public static function find($gameid){
      $sql = "SELECT * FROM game WHERE gameid = :gameid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $gameid));
      $row = $query->fetch();

      return self::get_game_from_row($row);
    }

    public static function find_format_gamedate($gameid){
      $sql = "SELECT gameid, courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
              comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles
              FROM game WHERE gameid = :gameid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $gameid));
      $row = $query->fetch();

      return self::get_game_from_row($row);
    }

    public static function count_all_player_games_by_year($playerid, $year) {
      $sql = "SELECT COUNT(*) gamecount FROM game
              WHERE to_char(gamedate, 'YYYY') = :year
              AND gameid IN
              (SELECT gameid FROM score WHERE playerid = :playerid)";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid, 'year' => $year));
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function count_all_player_games($playerid) {
      $sql = "SELECT COUNT(*) gamecount FROM game
              WHERE gameid IN
              (SELECT gameid FROM score WHERE playerid = :playerid)";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function count_all_course_games($courseid) {
      $sql = "SELECT COUNT(*) gamecount FROM game
              WHERE courseid = :courseid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function count_all_course_games_by_year($courseid, $year) {
      $sql = "SELECT COUNT(*) gamecount FROM game
              WHERE courseid = :courseid
              AND to_char(gamedate, 'YYYY') = :year";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid, 'year' => $year));
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function count_all_player_games_on_course($playerid, $courseid, $year) {
      $sql = "SELECT COUNT(*) gamecount FROM game
              WHERE courseid = :courseid
              AND to_char(gamedate, 'YYYY') = :year
              AND gameid IN
              (SELECT gameid FROM score WHERE playerid = :playerid)";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid, 'courseid' => $courseid, 'year' => $year));
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function count_all() {
      $sql = "SELECT COUNT(*) gamecount FROM game";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function count_all_by_year($year) {
      $sql = "SELECT COUNT(*) gamecount
              FROM game
              WHERE to_char(gamedate, 'YYYY') = :year";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('year' => $year));
      $row = $query->fetch();

      return $row['gamecount'];
    }

    public static function course_games($courseid) {
      $sql = "SELECT * FROM game WHERE courseid = :courseid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $rows = $query->fetchAll();

      return self::get_games_from_rows($rows);
    }

    public static function latest_game() {
      $sql = "SELECT course.name, to_char(game.gamedate, 'HH24:MI DD.MM.YYYY') as gamedate
              FROM game
              JOIN course ON game.courseid = course.courseid
              ORDER BY game.gamedate DESC LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $row = $query->fetch();

      return $row['name']. " (". $row['gamedate']. ")";
    }

    public static function latest_player_game($playerid) {
      $sql = "SELECT course.name, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate
              FROM game
              JOIN course ON game.courseid = course.courseid
              WHERE game.gameid IN
              (SELECT gameid FROM score WHERE playerid = :playerid)
              ORDER BY game.gamedate DESC LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $row = $query->fetch();

      return $row['name']. " (". $row['gamedate']. ")";
    }

    public static function player_high_scores($playerid) {
      $player_courses = Course::player_courses($playerid);
      $high_scores = array();

      foreach ($player_courses as $course) {
        $sql = "SELECT gameid, total_score, total_score - total_par as to_par
                FROM (
                SELECT score.gameid, SUM(score.stroke) + SUM(score.ob) as total_score,
                SUM(CASE WHEN score.stroke = 0 THEN 0 ELSE hole.par END) as total_par
                FROM score
                JOIN hole ON score.holeid = hole.holeid
                WHERE score.legal = TRUE
                AND score.playerid = :playerid
                AND hole.courseid = :courseid
                GROUP BY score.gameid
                ) t1
                ORDER BY total_score ASC LIMIT 1";
        $query = DB::connection()->prepare($sql);
        $query->execute(array('playerid' => $playerid, 'courseid' => $course->courseid));
        $row = $query->fetch();

        // Array to be passed to the HTML template
        $high_score = array();
        $high_score[] = self::find_format_gamedate($row['gameid']); // index 0: game
        $high_score[] = $course; // index 1: course
        $high_score[] = $row['total_score']; // index 2: total score

        // index 3: to par
        if ($row['to_par'] > 0) {
          $high_score[] = "+". $row['to_par'];
        } else {
          $high_score[] = $row['to_par'];
        }

        $high_scores[] = $high_score;
      }

      return $high_scores;
    }

    public static function get_gamedate($gameid) {
      $sql = "SELECT gamedate FROM game WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $gameid));
      $row = $query->fetch();

      return $row['gamedate'];
    }

    private static function get_game_from_row($row) {
      if ($row) {
        $game = new Game(array(
          'gameid' => $row['gameid'],
          'courseid' => $row['courseid'],
          'gamedate' => $row['gamedate'],
          'comment' => $row['comment'],
          'rain' => $row['rain'],
          'wet_no_rain' => $row['wet_no_rain'],
          'windy' => $row['windy'],
          'variant' => $row['variant'],
          'dark' => $row['dark'],
          'led' => $row['led'],
          'snow' => $row['snow'],
          'doubles' => $row['doubles']
        ));
        $game->prepare();

        return $game;
      }

      return null;
    }

    private static function get_games_from_rows($rows) {
      $games = array();

      foreach ($rows as $row) {
        $game = self::get_game_from_row($row);
        $games[] = $game;
      }

      return $games;
    }

    // Validators

    public function validate_date() {
      $format = 'Y-m-d H:i:s';
      $errors = array();
      $d = DateTime::createFromFormat($format, $this->gamedate);

      if (!($d && $d->format($format) == $this->gamedate)) {
        $errors[] = "Päivämäärä on annettu väärässä muodossa. Nuodata annettua syntaksia (YYYY-MM-DD HH:MM).";
      }

      return $errors;
    }

    public function validate_rain_and_wet_no_rain() {
      $errors = array();

      if ($this->rain && $this->wet_no_rain) {
        $errors[] = "Olosuhteina ei voi olla sekä sadetta että märkää (ei sadetta).";
      }

      return $errors;
    }
  }
