<?php

  class Course extends BaseModel {

    public $courseid, $name, $city, $map, // Ready to use after creation
            $holes, $number_of_holes, // Need to be prepared via prepare()
            $occurance; // used for popular_courses()

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_name', 'validate_city');
    }

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

    public function prepare() {
      $this->load_holes();
    }

    private function load_holes() {
      $this->holes = Hole::course_holes($this->courseid);
      $this->number_of_holes = count($this->holes);
    }

    public static function all() {
      $sql = "SELECT * FROM course ORDER BY courseid";
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

    public static function find($courseid){
      $sql = "SELECT * FROM course WHERE courseid = :courseid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      return self::get_course_from_row($row);
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

    public static function latest_game_date($courseid) {
      $sql = "SELECT to_char(gamedate, 'HH24:MI DD.MM.YYYY') AS gamedate
              FROM game WHERE courseid = :courseid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $row = $query->fetch();

      if ($row) {
        return $row['gamedate'];
      }

      return null;
    }

    public static function popular_courses($playerid) {
      $sql = "SELECT course.name, course.city, COUNT(game.courseid) AS course_occurance
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

    // Validators

    public function validate_name() {
      return $this->validate_string_not_empty($this->name, "Nimi");
    }

    public function validate_city() {
      return $this->validate_string_not_empty($this->city, "Kaupunki");
    }
  }
