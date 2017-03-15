<?php

  class Contest extends BaseModel {

    public $contestid, $creator, $name, $number_of_games;

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