<?php

  class Player extends BaseModel {

  	public $playerid, $firstname, $lastname, $username, $password; // Ready to use after creation

  	public function __construct($attributes) {
  		parent::__construct($attributes);
  	}

    public function save() {
      $sql = "INSERT INTO player (firstname, username, password) 
              VALUES (:firstname, :username, :password) RETURNING playerid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('firstname' => $this->firstname,
                            'username' => $this->username,
                            'password' => $this->password));
      $row = $query->fetch();
      $this->playerid = $row['playerid'];
    }

    public static function authenticate($username, $password) {
      $sql = "SELECT * FROM Player WHERE username = :username AND password = :password LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('username' => $username, 'password' => $password));
      $row = $query->fetch();

      if ($row) {
        $player = new Player(array(
          'playerid' => $row['playerid'],
          'firstname' => $row['firstname'],
          'lastname' => $row['lastname'],
          'username' => $row['username']
        ));
        return $player;
      } else {
        return null;
      }
    }

    public static function username_exists($username) {
      $sql = "SELECT * FROM Player WHERE username = :username LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('username' => $username));
      $row = $query->fetch();

      if ($row) {
        return true;
      } else {
        return false;
      }
    }

  	public static function all() {
  		$query = DB::connection()->prepare('SELECT * FROM player');
  		$query->execute();
  		$rows = $query->fetchAll();
  		$players = array();

  		foreach ($rows as $row) {
  			$players[] = new Player(array(
  				'playerid' => $row['playerid'],
  				'firstname' => $row['firstname'],
  				'lastname' => $row['lastname'],
  				'username' => $row['username'],
  				'password' => $row['password']
  			));
  		}

  		return $players;
  	}

  	public static function find($playerid){
      $sql = "SELECT * FROM player WHERE playerid = :playerid LIMIT 1";
	    $query = DB::connection()->prepare($sql);
	    $query->execute(array('playerid' => $playerid));
	    $row = $query->fetch();

      if ($row) {
  			$player = new Player(array(
  				'playerid' => $row['playerid'],
  				'firstname' => $row['firstname'],
  				'lastname' => $row['lastname'],
  				'username' => $row['username'],
  				'password' => $row['password']
  			));

  			return $player;
  		}

  		return null;
  	}
  }