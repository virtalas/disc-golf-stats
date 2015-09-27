<?php

  class Hole extends BaseModel {
  	
  	public $holeid, $courseid, $hole_num, $par;

  	public function __construct($attributes) {
  		parent::__construct($attributes);
      $this->validators = array('validate_hole_num_and_par');
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

    public function update() {
      $sql = "UPDATE hole SET par = :par WHERE holeid = :holeid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('par' => $this->par, 'holeid' => $this->holeid));
    }

    public function destroy() {
      $sql = "DELETE FROM hole WHERE holeid = :holeid";
      $query = DB::connection()->prepare($sql);
      $query->execute(array('holeid' => $this->holeid));
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
      $sql = "SELECT * FROM hole WHERE courseid = :courseid ORDER BY hole_num";
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

    // Validators

    public function validate_hole_num_and_par() {
      $hole_num_errors = $this->validate_integer($this->hole_num, "Väylän numeron");
      $par_errors = $this->validate_integer($this->par, "Par-tuloksen");

      $errors = array_merge($hole_num_errors, $par_errors);
      return $errors;
    }
  }
