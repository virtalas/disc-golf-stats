<?php

  class Player extends BaseModel {

  	public $playerid, $admin, $firstname, $lastname, $username, $password, $salt; // Ready to use after creation

  	public function __construct($attributes) {
  		parent::__construct($attributes);
  	}

    /*
    *  Authentication functions
    */

    public static function authenticate($username, $password) {
      $sql = "SELECT * FROM Player WHERE username = :username LIMIT 1";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('username' => $username));
      $row = $query->fetch();

      if ($row && hash_equals($row['password'], crypt($password, $row['salt']))) {
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

    public static function is_admin($playerid) {
      $player = self::find($playerid);
      return $player->admin;
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

    /*
    *  Database functions
    */

    public function save() {
      $sql = "INSERT INTO player (firstname, username, password, salt)
              VALUES (:firstname, :username, :password, :salt) RETURNING playerid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('firstname' => $this->firstname,
                            'username' => $this->username,
                            'password' => $this->password,
                            'salt' => $this->salt));
      $row = $query->fetch();
      $this->playerid = $row['playerid'];
    }

  	public static function all() {
  		$query = DB::connection()->prepare('SELECT * FROM player ORDER BY playerid ASC');
  		$query->execute();
  		$rows = $query->fetchAll();
  		$players = array();

  		foreach ($rows as $row) {
  			$players[] = new Player(array(
  				'playerid' => $row['playerid'],
  				'firstname' => $row['firstname'],
  				'lastname' => $row['lastname'],
  				'username' => $row['username']
  			));
  		}

  		return $players;
  	}

    public static function all_firstnames() {
      $sql = "SELECT firstname FROM player";
      $query = DB::connection()->prepare($sql);
      $query->execute();
      $rows = $query->fetchAll();
      $firstnames = array();

      foreach ($rows as $row) {
        $firstnames[] = $row['firstname'];
      }

      return $firstnames;
    }

  	public static function find($playerid) {
      $sql = "SELECT * FROM player WHERE playerid = :playerid LIMIT 1";
	    $query = DB::connection()->prepare($sql);
	    $query->execute(array('playerid' => $playerid));
	    $row = $query->fetch();

      if ($row) {
  			$player = new Player(array(
  				'playerid' => $row['playerid'],
          'admin' => $row['admin'],
  				'firstname' => $row['firstname'],
  				'lastname' => $row['lastname'],
  				'username' => $row['username'],
  				'password' => $row['password']
  			));

  			return $player;
  		}

  		return null;
  	}

    public static function find_by_firstname($firstname) {
      $sql = "SELECT * FROM player WHERE firstname = :firstname LIMIT 1";
	    $query = DB::connection()->prepare($sql);
	    $query->execute(array('firstname' => $firstname));
	    $row = $query->fetch();

      if ($row) {
  			$player = new Player(array(
  				'playerid' => $row['playerid'],
          'admin' => $row['admin'],
  				'firstname' => $row['firstname'],
  				'lastname' => $row['lastname'],
  				'username' => $row['username'],
  				'password' => $row['password']
  			));

  			return $player;
  		}

  		return null;
    }

    public static function firstname_from_playerid_string($players, $playerid_string) {
      // playerid_string is i.e. 'player4'
      $playerid = (int) str_replace("player", "", $playerid_string);
      $player_name = $players[$playerid - 1]->firstname; // $players ordered by playerid ascending
      return $player_name;
    }
  }

// function hash_equals used for verifying crypted passwords (Not the best way)
if(!function_exists('hash_equals')) {
  function hash_equals($str1, $str2) {
    if(strlen($str1) != strlen($str2)) {
      return false;
    } else {
      $res = $str1 ^ $str2;
      $ret = 0;
      for($i = strlen($res) - 1; $i >= 0; $i--) $ret |= ord($res[$i]);
      return !$ret;
    }
  }
}
