<?php

  class Player extends BaseModel {

  	public $playerid, $firstname, $lastname, $username, $password;

  	public function __construct($attributes) {
  		parent::__construct($attributes);
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
  	    $query = DB::connection()->prepare('SELECT * FROM player WHERE playerid = :playerid LIMIT 1');
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