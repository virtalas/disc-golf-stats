<?php
  class Game extends BaseModel {

    public $gameid, $courseid, $creator, $contestid, $gamedate, $comment, $rain, $wet_no_rain, $windy, // Ready to use after creation
            $variant, $dark, $led, $snow, $doubles, $temp, // Ready to use after creation
            $course, $scores, $conditions, $weather, $illegal_scorers, $high_scorers, $contest_name, $total_scores; // Need to be prepared via prepare_var()

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_date', 'validate_rain_and_wet_no_rain', 'validate_temp');
    }

    /*
    *  Database functions
    */

    public function save() {
      $sql = "INSERT INTO game (courseid, creator, gamedate, comment, rain, wet_no_rain,
              windy, variant, dark, led, snow, doubles, temp)
              VALUES (:courseid, :creator, :gamedate, :comment, :rain, :wet_no_rain, :windy,
              :variant, :dark, :led, :snow, :doubles, :temp) RETURNING gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $this->courseid,
                            'creator' => $this->creator,
                            'gamedate' => $this->gamedate,
                            'comment' => $this->comment,
                            'rain' => $this->rain,
                            'wet_no_rain' => $this->wet_no_rain,
                            'windy' => $this->windy,
                            'variant' => $this->variant,
                            'dark' => $this->dark,
                            'led' => $this->led,
                            'snow' => $this->snow,
                            'doubles' => $this->doubles,
                            'temp' => $this->temp));
      $row = $query->fetch();
      $this->gameid = $row['gameid'];

      return $this->gameid;
    }

    public function update() {
      $sql = "UPDATE game SET contestid = :contestid, gamedate = :gamedate, comment = :comment, rain = :rain,
              wet_no_rain = :wet_no_rain, windy = :windy, variant = :variant,
              dark = :dark, led = :led, snow = :snow, doubles = :doubles, temp = :temp
              WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);

      $query->execute(array('gameid' => $this->gameid,
                            'contestid' => $this->contestid,
                            'gamedate' => $this->gamedate,
                            'comment' => $this->comment,
                            'rain' => $this->rain,
                            'wet_no_rain' => $this->wet_no_rain,
                            'windy' => $this->windy,
                            'variant' => $this->variant,
                            'dark' => $this->dark,
                            'led' => $this->led,
                            'snow' => $this->snow,
                            'doubles' => $this->doubles,
                            'temp' => $this->temp));
      return $this->gameid;
    }

    public function add_to_contest($contestid) {
      $sql = "UPDATE game SET contestid = :contestid WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid, 'contestid' => $contestid));
    }

    public function remove_from_contest() {
      $sql = "UPDATE game SET contestid = null WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid));
    }

    public function destroy() {
      $sql = "DELETE FROM game WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid));
    }

    /*
    *  Prepare functions
    */

    public function prepare() {
      $this->load_course();
      $this->load_scores();
      $this->load_conditions();
      $this->load_weather();
      $this->load_illegal_scorers();
      $this->load_high_scorers();
      $this->load_contest_name();
    }

    private function load_course() {
      $this->course = Course::find($this->courseid);
      $this->course->prepare();
    }

    private function load_scores() {
      Score::all_game_scores($this);
    }

    private function load_conditions() {
      $conditions_array = array();

      if ($this->variant) {
        array_push($conditions_array, "poikkeava rata");
      }
      if ($this->led) {
        array_push($conditions_array, "LED");
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

    private function load_weather() {
      $weather_array = array();

      if (!is_null($this->temp)) { // temp can be 0!
        array_push($weather_array, $this->temp. " °C");
      }
      if ($this->windy) {
        array_push($weather_array, "tuulista");
      }
      if ($this->rain) {
        array_push($weather_array, "sadetta");
      }
      if ($this->wet_no_rain) {
        array_push($weather_array, "märkää (ei sadetta)");
      }
      if ($this->dark) {
        array_push($weather_array, "pimeää");
      }
      if ($this->snow) {
        array_push($weather_array, "lunta");
      }

      $i = 1;
      $weather_string = "";
      foreach ($weather_array as $weather) {
        $weather_string .= $weather;
        if ($i < count($weather_array)) {
          $weather_string .= ", ";
        }
        $i++;
      }

      $this->weather = $weather_string;
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

    public function load_contest_name() {
      $sql = "SELECT name FROM contest WHERE contestid = :contestid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('contestid' => $this->contestid));
      $row = $query->fetch();
      $this->contest_name = $row['name'];
    }

    /*
    *  Information functions
    */

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
      $sql = "SELECT gameid, courseid, creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
              comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles, temp, contestid
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
      if (!$options) {
        // Fetch all games
        $sql = "SELECT game.gameid, game.courseid, game.creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                game.comment, game.rain, game.wet_no_rain, game.windy, game.variant, game.dark, game.led,
                game.snow, game.doubles, game.temp, game.contestid
                FROM game
                GROUP BY game.gameid
                ORDER BY game.gamedate DESC";

        $query = DB::connection()->prepare($sql);
        $query->execute();
        $rows = $query->fetchAll();

        return self::get_games_from_rows($rows);
      }

      $page_size = $options['page_size'];
      $page = $options['page'];
      $year = "%%%%"; // returns any year
      if (isset($options['year'])) {
        $year = $options['year'];
      }
      $offset = self::offset($page_size, $page);

      // 'course' and 'player' parameters determine what games are fetched

      if (isset($options['playerid']) && isset($options['courseid'])) {
        // Fetch this player's games on this course
        $playerid = $options['playerid'];
        $courseid = $options['courseid'];

        $sql = "SELECT game.gameid, game.courseid, game.creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                game.comment, game.rain, game.wet_no_rain, game.windy, game.variant, game.dark, game.led,
                game.snow, game.doubles, game.temp, game.contestid
                FROM game
                JOIN score ON score.gameid = game.gameid
                WHERE score.playerid = :playerid
                AND game.courseid = :courseid
                AND to_char(gamedate, 'YYYY') LIKE :year
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
        $sql = "SELECT game.gameid, game.courseid, game.creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                game.comment, game.rain, game.wet_no_rain, game.windy, game.variant, game.dark, game.led,
                game.snow, game.doubles, game.temp, game.contestid
                FROM game
                JOIN score ON score.gameid = game.gameid
                WHERE score.playerid = :playerid
                AND to_char(gamedate, 'YYYY') LIKE :year
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
        $sql = "SELECT gameid, courseid, creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles, temp, contestid
                FROM game
                WHERE courseid = :courseid
                AND to_char(gamedate, 'YYYY') LIKE :year
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
        $sql = "SELECT gameid, courseid, creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
                comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles, temp, contestid
                FROM game
                WHERE to_char(gamedate, 'YYYY') LIKE :year
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

    public static function search($conditions, $page, $page_size) {
      $sql = "SELECT game.gameid, game.courseid, game.creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
              game.comment, game.rain, game.wet_no_rain, game.windy, game.variant, game.dark, game.led,
              game.snow, game.doubles, game.temp, game.contestid
              FROM game ";

      $prepared = self::prepare_sql_for_search_conditions($sql, $conditions);
      $sql = $prepared["sql"];
      $search_conditions = $prepared["search_conditions"];

      $search_conditions["limit"] = $page_size;
      $search_conditions["offset"] = self::offset($page_size, $page);

      $sql .= " GROUP BY game.gameid
                ORDER BY game.gamedate DESC
                LIMIT :limit OFFSET :offset";
      $query = DB::connection()->prepare($sql);
      $query->execute($search_conditions);
      $rows = $query->fetchAll();

      return self::get_games_from_rows($rows);
    }

    public static function count_all_for_search($conditions, $page, $page_size) {
      $sql = "SELECT COUNT (*) as game_count FROM game ";

      $prepared = self::prepare_sql_for_search_conditions($sql, $conditions);
      $sql = $prepared["sql"];
      $search_conditions = $prepared["search_conditions"];

      $query = DB::connection()->prepare($sql);
      $query->execute($search_conditions);
      $row = $query->fetch();

      return $row["game_count"];
    }

    private static function prepare_sql_for_search_conditions($sql, $conditions) {
      if (!empty($conditions)) {
        $sql .= " WHERE ";
      }

      $search_conditions = array();

      foreach ($conditions as $key => $value) {
        if ($value !== null) {
          if (!empty($search_conditions)) {
            $sql .= " and ";
          }
          // game.key = :key
          $sql .= " game.". $key. " = :". $key. " ";
          $search_conditions[$key] = $value;
        }
      }

      return array("sql" => $sql, "search_conditions" => $search_conditions);
    }

    public static function contest_games($contestid) {
      $sql = "SELECT * FROM game WHERE contestid = :contestid ORDER BY gamedate ASC"; // needs to be ASC for point counting
      $query = DB::connection()->prepare($sql);
      $query->execute(array('contestid' => $contestid));
      $rows = $query->fetchAll();

      return self::get_games_from_rows($rows);
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
      $sql = "SELECT gameid, courseid, creator, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
              comment, rain, wet_no_rain, windy, variant, dark, led, snow, doubles, temp, contestid
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
      // If there are multiple games with the same high score, this returns them both.
      // Duplicate scores come in a row, with the newest one first (the correct one).
      $sql = "SELECT to_char(t1.gamedate, 'HH24:MI DD.MM.YYYY') as gamedate, t1.name, t1.gameid, t1.total_score,
              CASE
              WHEN t1.total_score - t1.total_par > 0
              THEN '+' || t1.total_score - t1.total_par
              ELSE '' || t1.total_score - t1.total_par
              END AS to_par
              FROM (
                SELECT game.gamedate, course.courseid, course.name, score.gameid, SUM(score.stroke) + SUM(score.ob) as total_score,
                SUM(CASE WHEN score.stroke = 0 THEN 0 ELSE hole.par END) as total_par
                FROM score
                JOIN hole ON score.holeid = hole.holeid
                JOIN course ON hole.courseid = course.courseid
                JOIN game ON score.gameid = game.gameid
                WHERE score.legal = TRUE
                AND score.playerid = :playerid
                GROUP BY score.gameid, course.courseid, game.gamedate
                ORDER BY course.name
              ) t1
                LEFT JOIN (
                  SELECT game.gamedate, course.courseid, course.name, score.gameid, SUM(score.stroke) + SUM(score.ob) as total_score,
                  SUM(CASE WHEN score.stroke = 0 THEN 0 ELSE hole.par END) as total_par
                  FROM score
                  JOIN hole ON score.holeid = hole.holeid
                  JOIN course ON hole.courseid = course.courseid
                  JOIN game ON score.gameid = game.gameid
                  WHERE score.legal = TRUE
                  AND score.playerid = :playerid
                  GROUP BY score.gameid, course.courseid, game.gamedate
                  ORDER BY course.name
                ) t2
                  ON (t1.courseid = t2.courseid AND t1.total_score > t2.total_score)
              WHERE t2.courseid IS NULL
              ORDER BY t1.name ASC, t1.gamedate DESC";

      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $rows = $query->fetchAll();

      return $rows;
    }

    public static function get_gamedate($gameid) {
      $sql = "SELECT gamedate FROM game WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $gameid));
      $row = $query->fetch();

      return $row['gamedate'];
    }

    public static function get_creator($gameid) {
      $sql = "SELECT creator FROM game WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $gameid));
      $row = $query->fetch();

      return $row['creator'];
    }

    public static function ten_latest_games() {
      $sql = "SELECT *
              FROM game
              JOIN course ON game.courseid = course.courseid
              ORDER BY game.gamedate DESC
              LIMIT 10";

      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();

      return self::get_games_from_rows($rows);
    }

    /*
    *  Graph functions
    */

    public static function player_count_distribution() {
      $sql = "SELECT gameid, COUNT(gameid) as player_count
              FROM
              (SELECT gameid, playerid
              FROM score
              GROUP BY gameid, playerid) t1
              GROUP BY gameid";

      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();

      $dist = array();

      // initialize array
      for ($i = 1; $i <= count(Player::all_firstnames()); $i++) {
        $dist[$i] = 0;
      }

      foreach ($rows as $row) {
        $dist[$row['player_count']]++;
      }

      return $dist;
    }

    public static function all_game_dates($playerid = null) {

      if ($playerid != null) {
        // Fetch player specific games
        $sql = "SELECT to_char(gamedate, 'YYYY-MM') as gamedated, COUNT(to_char(gamedate, 'YYYY-MM')) as occurance
                FROM game
                WHERE gameid IN (
                  SELECT gameid FROM score WHERE playerid = :playerid
                )
                GROUP BY gamedated
                ORDER BY gamedated ASC";

        $query = DB::connection()->prepare($sql);
        $query->execute(array('playerid' => $playerid));
      } else {
        // Fetch all games
        $sql = "SELECT to_char(gamedate, 'YYYY-MM') as gamedated, COUNT(to_char(gamedate, 'YYYY-MM')) as occurance
                FROM game
                GROUP BY gamedated
                ORDER BY gamedated ASC";

        $query = DB::connection()->prepare($sql);
        $query->execute();
      }

      $rows = $query->fetchAll();
      $gamedates = array();

      foreach ($rows as $row) {;
        $gamedates[$row['gamedated']] = $row['occurance'];
      }

      return $gamedates;
    }

    public static function game_hours_and_weekdays() {
      $sql = "SELECT date_part('dow', gamedate) as day,
              date_part('hour', date_trunc('hour', gamedate + interval '30 minute')) as hour
              FROM game";

      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();

      $gamedates = array();

      return $rows;
    }

    /*
    *  Get from row(s)
    */

    private static function get_game_from_row($row) {
      if ($row) {
        $game = new Game(array(
          'gameid' => $row['gameid'],
          'courseid' => $row['courseid'],
          'creator' => $row['creator'],
          'gamedate' => $row['gamedate'],
          'comment' => $row['comment'],
          'rain' => $row['rain'],
          'wet_no_rain' => $row['wet_no_rain'],
          'windy' => $row['windy'],
          'variant' => $row['variant'],
          'dark' => $row['dark'],
          'led' => $row['led'],
          'snow' => $row['snow'],
          'doubles' => $row['doubles'],
          'temp' => $row['temp'],
          'contestid' => $row['contestid']
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

    private static function offset($page_size, $page) {
      return (int)$page_size * ((int)$page - 1);
    }

    /*
    *  Validators
    */

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

    public function validate_temp() {
      $errors = array();

      if (!is_numeric($this->temp) && $this->temp != null) {
        $errors[] = "Lämpötila annettu väärin. Käytä lämpötilassa pistettä (.) eikä pilkkua (,)";
      }

      return $errors;
    }
  }
