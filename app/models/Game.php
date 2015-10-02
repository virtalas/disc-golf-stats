<?php
  class Game extends BaseModel {

    public $gameid, $courseid, $gamedate, $comment, $rain, $wet_no_rain, $windy,
            $variant, $dark, $led, $snow,
            $course, $scores, $conditions;

    public function __construct($attributes) {
      parent::__construct($attributes);
      $this->validators = array('validate_date');
    }

    public function save() {
      $sql = "INSERT INTO game (courseid, gamedate, comment, rain, wet_no_rain,
              windy, variant, dark, led, snow) 
              VALUES (:courseid, :gamedate, :comment, :rain, :wet_no_rain, :windy,
              :variant, :dark, :led, :snow) RETURNING gameid";
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
                            'snow' => $this->snow
                            ));
      $row = $query->fetch();
      $this->gameid = $row['gameid'];
      
      return $this->gameid;
    }

    public function update() {
      $sql = "UPDATE game SET gamedate = :gamedate, comment = :comment, rain = :rain,
              wet_no_rain = :wet_no_rain, windy = :windy, variant = :variant,
              dark = :dark, led = :led, snow = :snow 
              WHERE gameid = :gameid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('gameid' => $this->gameid,
                            'courseid' => $this->courseid, 
                            'gamedate' => $this->gamedate, 
                            'comment' => $this->comment, 
                            'rain' => $this->rain, 
                            'wet_no_rain' => $this->wet_no_rain, 
                            'windy' => $this->windy,
                            'variant' => $this->variant, 
                            'dark' => $this->dark, 
                            'led' => $this->led, 
                            'snow' => $this->snow
                            ));
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

    public static function all_player_games($playerid) {
      $sql = "SELECT gameid, courseid, to_char(gamedate, 'HH24:MI DD.MM.YYYY') as gamedate,
              comment, rain, wet_no_rain, windy, variant, dark, led, snow
              FROM game
              WHERE gameid IN
              (SELECT gameid FROM score WHERE playerid = :playerid)
              ORDER BY game.gamedate DESC";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('playerid' => $playerid));
      $rows = $query->fetchAll();
      $games = array();

      foreach ($rows as $row) {
        $games[] = new Game(array(
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
          'snow' => $row['snow']
        ));
      }

      return $games;
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

    // Validators

    public function validate_date() {
      $format = 'Y-m-d H:i:s';
      $errors = array();
      $d = DateTime::createFromFormat($format, $this->gamedate);

      if (!($d && $d->format($format) == $this->gamedate)) {
        $errors[] = "Päivämäärä on annettu väärässä muodossa. Nuodata annettua syntaksia.";
      }

      return $errors;
    }
  }
