<?php

  class CourseController extends BaseController {
    public static function index() {
      $courses = Course::all();

      View::make('course/index.html', array('courses' => $courses));
    }

    public static function show($courseid) {
      $course = Course::find($courseid);
      $holes = Hole::course_holes($courseid);
      $games_played = Course::number_of_games_played($courseid);
      $latest_game_date = Course::latest_game_date($courseid);

      View::make('course/show.html', array('course' => $course,
                                          'holes' => $holes,
                                          'games_played' => $games_played,
                                          'latest_game_date' => $latest_game_date));
    }

    public static function create() {
      View::make('course/new.html');
    }

    public static function store() {
      $params = $_POST;

      $course_params = array(
        'name' => $params['name'],
        'city' => $params['city']
      );
      $course = new Course($course_params);
      $courseid = $course->save();

      // Create course holes
      $number_of_holes = count($params) - count($course_params);
      for ($hole_num = 1; $hole_num <= $number_of_holes; $hole_num++) {
        $par = $params['hole'. $hole_num];
        $hole = new Hole(array(
          'courseid' => $courseid,
          'hole_num' => $hole_num,
          'par' => $par
        ));
        $hole->save();
      }

      Redirect::to('/course/'. $courseid, array('message' => 'Rata lisätty.'));
    }
  }