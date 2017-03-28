<?php

  class Contest extends BaseModel {

    public $contestid, $creator, $name, $number_of_games,
            $games_played;

    public function __construct($attributes) {
  		parent::__construct($attributes);
      $this->validators = array('validate_name', 'validate_number_of_games');
  	}

    /*
    *  Database functions
    */

    public function save() {
      $sql = "INSERT INTO contest (creator, name, number_of_games) VALUES (:creator, :name, :number_of_games) RETURNING contestid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('creator' => $this->creator, 'name' => $this->name, 'number_of_games' => $this->number_of_games));

      $row = $query->fetch();
      $this->contestid = $row['contestid'];

      return $this->contestid;
    }

    public function update() {
      $sql = "UPDATE contest SET name = :name, number_of_games = :number_of_games WHERE contestid = :contestid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array(
        'name' => $this->name,
        'number_of_games' => $this->number_of_games,
        'contestid' => $this->contestid
      ));
    }

    public function destroy() {
      $sql = "DELETE FROM contest WHERE contestid = :contestid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('contestid' => $this->contestid));
    }

    public function is_creator($player) {
      return $this->creator == $player->playerid;
    }

    public static function find($contestid){
      $sql = "SELECT * FROM contest WHERE contestid = :contestid LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('contestid' => $contestid));
      $row = $query->fetch();

      return self::get_contest_from_row($row);
    }

    public static function all() {
      $sql = "SELECT * from contest ORDER BY name";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();
      return self::get_contests_from_rows($rows);
    }

    public static function all_with_games_played() {
      $sql = "SELECT t.contestid, t.creator, t.name, t.number_of_games, (
                SELECT COUNT(game.gameid) FROM contest ct JOIN game ON game.contestid = ct.contestid WHERE ct.contestid = t.contestid
              ) as games_played
              FROM contest t";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();
      $contests = array();

      foreach ($rows as $row) {
        $contest = self::get_contest_from_row($row);
        $contest->games_played = $row['games_played'];
        $contests[] = $contest;
      }

      return $contests;
    }

    public static function points($contest, $games) {
      $points = array();
      $players = Player::all();
      $point_distribution = array(4, 3, 2, 1, 0);

      foreach ($games as $game) {
        $placement = 0;
        $game_num = 0;
        $previous_strokes = 0;
        $previous_points = 0;

        foreach ($game->total_scores as $playerid_string => $total) {
          // playerid_string is i.e. 'player4'
          $playerid = (int) str_replace("player", "", $playerid_string);
          $player_name = $players[$playerid - 1]->firstname; // $players ordered by playerid ascending

          // Works only with unique player firstnames (not a problem...)
          if (!array_key_exists($player_name, $points)) {
            // initialize
            $points[$player_name] = array("game_points" => array(), "total_points" => array(), "total_strokes" => 0);
          }

          // Player was tied for a placement:
          if ($previous_strokes == $total) {
            if ($game_num == 0) {
              $points[$player_name]["game_points"][] = $previous_points;
            } else {
              $points[$player_name]["game_points"][] = $points[$player_name]["game_points"][$game_num - 1] + $previous_points;
            }
          // Player was not tied:
          } else {
            if ($game_num == 0) {
              $points[$player_name]["game_points"][] = $point_distribution[$placement];
            } else {
              $points[$player_name]["game_points"][] = $points[$player_name]["game_points"][$game_num - 1] + $point_distribution[$placement];
            }
            $previous_points = $point_distribution[$placement];
          }

          $points[$player_name]["total_strokes"] += $total;

          $placement++;
          $previous_strokes = $total;
        }

        $game_num++;
      }

      return $points;
    }

    /*
    * Get from row(s)
    */

    public static function get_contest_from_row($row) {
      if ($row) {
        $contest = new Contest(array(
          'contestid' => $row['contestid'],
          'creator' => $row['creator'],
          'name' => $row['name'],
          'number_of_games' => $row['number_of_games'],
        ));

        return $contest;
      }

      return null;
    }

    public static function get_contests_from_rows($rows) {
      $contests = array();

      foreach ($rows as $row) {
        $contests[] = self::get_contest_from_row($row);
      }

      return $contests;
    }

    /*
    *  Validators
    */

    public function validate_name() {
      return $this->validate_string_not_empty($this->name, "Nimi");
    }

    public function validate_number_of_games() {
      return $this->validate_integer($this->number_of_games, "Pelien määrä");
    }
  }
