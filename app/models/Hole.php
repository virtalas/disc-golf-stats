<?php

  class Hole extends BaseModel {
  	
  	public $holeid, $courseid, $hole_num, $par;

  	public function __construct($attributes) {
  		parent::__construct($attributes);
  	}

    public function save() {
      $sql = "INSERT INTO hole (courseid, hole_num, par) 
              VALUES (:courseid, :hole_num, :par) RETURNING holeid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $this->courseid,
                            'hole_num' => $this->hole_num,
                            'par' => $this->par));
      $row = $query->fetch();
      $this->holeid = $row['holeid'];
    }

  	public static function all() {
  		$query = DB::connection()->prepare('SELECT * FROM hole');
  		$query->execute();
  		$rows = $query->fetchAll();
  		
      return Hole::get_holes_from_rows($rows);
  	}

  	public static function find($holeid){
      $query = DB::connection()->prepare('SELECT * FROM hole WHERE holeid = :holeid LIMIT 1');
      $query->execute(array('holeid' => $holeid));
      $row = $query->fetch();

      if ($row) {
  			$hole = new Hole(array(
  				'holeid' => $row['holeid'],
  				'courseid' => $row['courseid'],
  				'hole_num' => $row['hole_num'],
  				'par' => $row['par']
  			));

  			return $hole;
  		}

  		return null;
    }

    public static function course_holes($courseid) {
      $sql ="SELECT * FROM hole WHERE courseid = :courseid ORDER BY hole_num";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('courseid' => $courseid));
      $rows = $query->fetchAll();
      
      return Hole::get_holes_from_rows($rows);
    }

    public static function get_holes_from_rows($rows) {
      $holes = array();

      foreach ($rows as $row) {
        $holes[] = new Hole(array(
          'holeid' => $row['holeid'],
          'courseid' => $row['courseid'],
          'hole_num' => $row['hole_num'],
          'par' => $row['par']
        ));
      }

      return $holes;
    }
  }