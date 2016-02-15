<?php

  class Course extends BaseModel {

    public $courseid, $name, $city, $map, // Ready to use after creation
            $holes, $number_of_holes, // Need to be prepared via prepare()
            $occurance; // used for popular_courses()

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_name', 'validate_city');
    }

    /*
    *  Database functions
    */

    public function save() {
      $sql = "INSERT INTO course (name, city, map) VALUES (:name, :city, :map) RETURNING courseid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('name' => $this->name, 'city' => $this->city, 'map' => $this->map));

      $row = $query->fetch();
      $this->courseid = $row['courseid'];

      return $this->courseid;
    }

    public function update() {
      $sql = "UPDATE course SET name = :name, city = :city, map = :map WHERE courseid = :courseid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array(
        'name' => $this->name,
        'city' => $this->city,
        'map' => $this->map,
        'courseid' => $this->courseid
      ));
    }

    public function destroy() {
      $sql = "DELETE FROM course WHERE courseid = :courseid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $this->courseid));
    }

    public static function find($courseid){
      $sql = "SELECT * FROM course WHERE courseid = :courseid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      return self::get_course_from_row($row);
    }

    public static function count_all(){
      $sql = "SELECT COUNT(*) as course_count FROM course";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $row = $query->fetch();

      return $row['course_count'];
    }

    public static function all() {
      // Returns courses ordered by game count first, courseid second
      $sql = "SELECT course.courseid, course.name, course.city, course.map, COUNT(game.gameid) AS game_count
              FROM course
              LEFT JOIN game ON game.courseid = course.courseid
              GROUP BY course.courseid, course.name, course.city, course.map
              ORDER BY game_count DESC, course.courseid ASC";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();

      return self::get_courses_from_rows($rows);
    }

    public static function all_order_by_name() {
      $sql = "SELECT * FROM course ORDER BY name";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();

      return self::get_courses_from_rows($rows);
    }

    /*
    *  Prepare functions
    */

    public function prepare() {
      $this->load_holes();
    }

    private function load_holes() {
      $this->holes = Hole::course_holes($this->courseid);
      $this->number_of_holes = count($this->holes);
    }

    /*
    *  Information functions
    */

    public static function high_scores($courseid) {
      $sql = "SELECT gameid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate, firstname, total_score, total_score - total_par as to_par
              FROM (
              SELECT score.gameid, gamedate, player.firstname,
              SUM(score.stroke) + SUM(score.ob) as total_score,
              SUM(CASE WHEN score.stroke = 0 THEN 0 ELSE hole.par END) as total_par
              FROM score
              JOIN hole ON score.holeid = hole.holeid
              JOIN course ON hole.courseid = course.courseid
              JOIN game ON score.gameid = game.gameid
              JOIN player ON player.playerid = score.playerid
              WHERE score.legal = TRUE
              AND hole.courseid = :courseid
              GROUP BY score.gameid, gamedate, firstname
              ) t1
              ORDER BY total_score ASC, t1.gamedate DESC LIMIT 5";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $rows = $query->fetchAll();
      $high_scores = array();

      foreach ($rows as $row) {
        // Array to be passed to the HTML template
        $high_score = array();
        $high_score[] = $row['gamedate']; // index 0: gamedate
        $high_score[] = $row['firstname']; // index 1: player's name
        $high_score[] = $row['total_score']; // index 2: total score

        // index 3: to par
        if ($row['to_par'] > 0) {
          $high_score[] = "+". $row['to_par'];
        } else {
          $high_score[] = $row['to_par'];
        }

        $high_score[] = $row['gameid']; // index 4: gameid

        $high_scores[] = $high_score;
      }

      return $high_scores;
    }

    public static function number_of_games_played($courseid) {
      $sql = "SELECT COUNT(*) AS game_count
              FROM game WHERE courseid = :courseid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      if ($row) {
        return $row['game_count'];
      }

      return null;
    }

    public static function par($courseid) {
      $sql = "SELECT SUM(hole.par) as total_par FROM hole WHERE hole.courseid = :courseid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      if ($row) {
        return $row['total_par'];
      }

      return null;
    }

    public static function latest_game_date($courseid) {
      $sql = "SELECT to_char(gamedate, 'HH24:MI DD.MM.YYYY') AS gamedate
              FROM game
              WHERE courseid = :courseid
              ORDER BY game.gamedate DESC LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      if ($row) {
        return $row['gamedate'];
      }

      return null;
    }

    public static function average_scoring($courseid) {
      $sql = "SELECT to_char(AVG(score_sum), '999D99') as avg_score
              FROM (SELECT SUM(score.stroke + score.ob) as score_sum
              FROM score
              JOIN hole ON score.holeid = hole.holeid
              JOIN game ON score.gameid = game.gameid
              JOIN course ON hole.courseid = course.courseid
              WHERE score.legal = TRUE
              AND course.courseid = :courseid
              GROUP BY game.gameid, score.playerid) t1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      $to_par = (double) $row['avg_score'] - self::par($courseid);
      $to_par = round($to_par, 2);

      if ($to_par > 0) {
        return $row['avg_score']. " (+". $to_par. ")";
      } else {
        return $row['avg_score']. " (". $to_par. ")";
      }
    }

    public static function average_player_scoring_by_year($playerid, $year) {
      $sql = "SELECT course.courseid, course.name, to_char(AVG(t1.total_score), '999D9') as avg_score,
              to_char(AVG(t1.total_score - t2.par_sum), 'SG999D9') as to_par
              FROM course
              LEFT JOIN (
                SELECT course.courseid, course.name, SUM(score.stroke + score.ob) as total_score
                FROM score
                JOIN hole ON score.holeid = hole.holeid
                JOIN course ON hole.courseid = course.courseid
                JOIN game ON score.gameid = game.gameid
                WHERE score.legal = TRUE
                AND to_char(game.gamedate, 'YYYY') = :year
                AND score.playerid = :playerid
                GROUP BY game.gameid, course.courseid, course.name
              ) t1 ON course.courseid = t1.courseid
              LEFT JOIN (
                SELECT courseid, SUM(par) as par_sum
                FROM hole
                GROUP BY courseid
              ) t2 ON t1.courseid = t2.courseid
              WHERE course.courseid IN (
                SELECT hole.courseid FROM hole
                JOIN score ON score.holeid = hole.holeid
                WHERE score.playerid = :playerid
              )
              GROUP BY course.courseid, course.name
              ORDER BY course.name ASC";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid, 'year' => $year));
      $rows = $query->fetchAll();

      return $rows;
    }

    public static function popular_courses($playerid) {
      $sql = "SELECT game.courseid, course.name, course.city, COUNT(game.courseid) AS course_occurance
              FROM game
              JOIN course ON game.courseid = course.courseid
              WHERE game.gameid IN
              (SELECT gameid FROM score
              JOIN player ON score.playerid = player.playerid
              WHERE player.playerid = :playerid)
              GROUP BY game.courseid, course.name, course.city
              ORDER BY course_occurance DESC
              LIMIT 3";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $rows = $query->fetchAll();
      $courses = array();

      foreach ($rows as $row) {
        $course = new Course(array(
          'courseid' => $row['courseid'],
          'name' => $row['name'],
          'city' => $row['city'],
          'occurance' => $row['course_occurance']
        ));
        $course->prepare();
        $courses[] = $course;
      }

      return $courses;
    }

    public static function popular_courses_all_players() {
      $sql = "SELECT course.name, course.city, COUNT(game.courseid) AS course_occurance
              FROM game
              JOIN course ON game.courseid = course.courseid
              GROUP BY game.courseid, course.name, course.city
              ORDER BY course_occurance DESC
              LIMIT 3";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();
      $courses = array();

      foreach ($rows as $row) {
        $course = new Course(array(
          'name' => $row['name'],
          'city' => $row['city'],
          'occurance' => $row['course_occurance']
        ));
        $course->prepare();
        $courses[] = $course;
      }

      return $courses;
    }

    public static function player_courses($playerid) {
      $sql = "SELECT courseid, name, city, map
              FROM course
              WHERE courseid IN
              (SELECT hole.courseid FROM hole
              JOIN score ON score.holeid = hole.holeid
              WHERE score.playerid = :playerid)
              ORDER BY name";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $rows = $query->fetchAll();

      return self::get_courses_from_rows($rows);
    }

    /*
    *  Graph functions
    */

    public static function chronological_high_scores($courseid, $playerid) {
      if ($playerid == 0) {
        // High scores for ALL players
        $sql = "SELECT gameid, gamedate, MIN(total_score) as total_score FROM
                  (SELECT to_char(game.gamedate, 'YYYY-MM-DD') as gamedate, score.gameid, score.playerid,
                  SUM(score.stroke + score.ob) as total_score
                  FROM score
                  JOIN game ON game.gameid = score.gameid
                  JOIN course ON course.courseid = game.courseid
                  WHERE score.legal = true AND course.courseid = :courseid
                  GROUP BY score.gameid, score.playerid, game.gamedate) t1
                GROUP BY gameid, gamedate
                ORDER BY gamedate ASC";

        $query = DB::connection()->prepare($sql);
        $query->execute(array('courseid' => $courseid));
        $rows = $query->fetchAll();

      } else {
        // High scores for specific player
        $sql = "SELECT gameid, gamedate, MIN(total_score) as total_score FROM
                  (SELECT to_char(game.gamedate, 'YYYY-MM-DD') as gamedate, score.gameid, score.playerid,
                  SUM(score.stroke + score.ob) as total_score
                  FROM score
                  JOIN game ON game.gameid = score.gameid
                  JOIN course ON course.courseid = game.courseid
                  WHERE score.legal = true AND course.courseid = :courseid AND score.playerid = :playerid
                  GROUP BY score.gameid, score.playerid, game.gamedate) t1
                GROUP BY gameid, gamedate
                ORDER BY gamedate ASC";

        $query = DB::connection()->prepare($sql);
        $query->execute(array('courseid' => $courseid, 'playerid' => $playerid));
        $rows = $query->fetchAll();
      }

      $high_scores = array();
      $current_high_score = null;

      foreach ($rows as $row) {
        if ($current_high_score == null || $row['total_score'] <= $current_high_score) {
          $high_scores[$row['gamedate']] = $row['total_score'];
          $current_high_score = $row['total_score'];
        }
      }

      return $high_scores;
    }

    public static function score_distribution($courseid, $n) {
      $sql = "SELECT hole_num,
              COUNT(hole_in_one) AS hole_in_one,
              COUNT(birdie) AS birdie,
              COUNT(par) AS par,
              COUNT(bogey) AS bogey,
              COUNT(over_bogey) AS over_bogey
              FROM (
                SELECT hole.hole_num,
                CASE WHEN score.stroke = 1 THEN 1 END AS hole_in_one,
                CASE WHEN hole.par - score.stroke - score.ob = 1 THEN 1 END AS birdie,
                CASE WHEN hole.par - score.stroke - score.ob = 0 THEN 1 END AS par,
                CASE WHEN hole.par - score.stroke - score.ob = -1 THEN 1 END AS bogey,
                CASE WHEN hole.par - score.stroke - score.ob < -1 THEN 1 END AS over_bogey
                FROM score
                JOIN hole ON hole.holeid = score.holeid
                JOIN course ON course.courseid = hole.courseid
                WHERE course.courseid = :courseid AND score.legal = true
              ) t1
              GROUP BY hole_num
              ORDER BY hole_num";

      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $rows = $query->fetchAll();

      return $rows;
    }

    public static function course_popularity() {
      $sql = "SELECT course.name, course.city, COUNT(*) as count
              FROM course
              JOIN game ON game.courseid = course.courseid
              GROUP BY course.courseid
              HAVING COUNT(*) > 1
              ORDER BY count DESC";

      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();

      return $rows;
    }

    public static function player_course_popularity($playerid) {
      $sql = "SELECT course.name, course.city, COUNT(*) as count
              FROM course
              JOIN game ON game.courseid = course.courseid
              WHERE game.gameid IN (
                SELECT gameid FROM score WHERE playerid = :playerid
              )
              GROUP BY course.courseid
              HAVING COUNT(*) > 1
              ORDER BY count DESC";

      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $rows = $query->fetchAll();

      return $rows;
    }

    /*
    *  Get from row(s)
    */

    public static function get_course_from_row($row) {
      if ($row) {
        $course = new Course(array(
          'courseid' => $row['courseid'],
          'name' => $row['name'],
          'city' => $row['city'],
          'map' => $row['map']
        ));
        $course->prepare();

        return $course;
      }

      return null;
    }

    private static function get_courses_from_rows($rows) {
      $courses = array();

      foreach ($rows as $row) {
        $course = new Course(array(
          'courseid' => $row['courseid'],
          'name' => $row['name'],
          'city' => $row['city'],
          'map' => $row['map']
        ));
        $course->prepare();
        $courses[] = $course;
      }

      return $courses;
    }

    /*
    *  Validators
    */

    public function validate_name() {
      return $this->validate_string_not_empty($this->name, "Nimi");
    }

    public function validate_city() {
      return $this->validate_string_not_empty($this->city, "Kaupunki");
    }
  }
