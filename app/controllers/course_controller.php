<?php

  class CourseController extends BaseController {

    public static function index() {
      $player = self::get_user_logged_in();
      $course = null;

      if (Course::count_all() > 0) {
        if (isset($_GET['course'])) {
          $course = Course::find($_GET['course']);
        } else {
          $popular_courses = Course::popular_courses($player->playerid); // returns a list for no reason?

          if ($popular_courses) {
            $course = $popular_courses[0];
          } else {
            $course = Course::find(1);
          }
        }

        $sorted_by_name = false;

        if (isset($_GET['sort'])) {
          if ($_GET['sort'] == "name") {
            $courses = Course::all_order_by_name();
            $sorted_by_name = true;
          } else {
            $courses = Course::all();
          }
        } else {
          $courses = Course::all();
        }

        $courseid = $course->courseid;
        $games_played = Course::number_of_games_played($courseid);
        $latest_game = Course::latest_game_date($courseid);
        $avg_score = Course::average_scoring($courseid);
        $par = Course::par($courseid);
        $high_scores = Course::high_scores($courseid);

        // Graph data
        $chrono_high_scores = Course::chronological_high_scores($courseid, 0);
        $chrono_high_scores_player = Course::chronological_high_scores($courseid, $player->playerid);
        $score_distribution = Course::score_distribution($courseid, 1);

        View::make('course/index.html', array(
          'course' => $course,
          'courses' => $courses,
          'player' => $player,
          'games_played' => $games_played,
          'latest_game' => $latest_game,
          'avg_score' => $avg_score,
          'par' => $par,
          'high_scores' => $high_scores,
          'chrono_high_scores' => $chrono_high_scores,
          'chrono_high_scores_player' => $chrono_high_scores_player,
          'score_distribution' => $score_distribution,
          'sorted_by_name' => $sorted_by_name
        ));
      } else {
        View::make('course/index.html');
      }
    }

    public static function show($courseid) {
      $player = self::get_user_logged_in();
      $course = Course::find($courseid);
      $games_played = Course::number_of_games_played($courseid);
      $latest_game = Course::latest_game_date($courseid);
      $avg_score = Course::average_scoring($courseid);
      $par = Course::par($courseid);
      $high_scores = Course::high_scores($courseid);

      // Graph data
      $chrono_high_scores = Course::chronological_high_scores($courseid, 0);
      $chrono_high_scores_player = Course::chronological_high_scores($courseid, $player->playerid);
      $score_distribution = Course::score_distribution($courseid, 1);

      View::make('course/show.html', array(
        'course' => $course,
        'games_played' => $games_played,
        'latest_game' => $latest_game,
        'avg_score' => $avg_score,
        'par' => $par,
        'high_scores' => $high_scores,
        'chrono_high_scores' => $chrono_high_scores,
        'chrono_high_scores_player' => $chrono_high_scores_player,
        'score_distribution' => $score_distribution
      ));
    }

    public static function list_courses() {
      $courses = Course::all();
      View::make('course/list.html', array('courses' => $courses));
    }

    public static function create() {
      $hole_count = $_GET['hole_count'];

      View::make('course/new.html', array('hole_count' => $hole_count));
    }

    public static function store() {
      $params = $_POST;

      $course_params = array(
        'name' => $params['name'],
        'city' => $params['city'],
        'map' => $params['map']
      );
      $course = new Course($course_params);
      $errors = $course->errors();

      $number_of_holes = count($params) - count($course_params) - 1; // one hidden input for hole_count

      // Check hole validity before saving anything
      $holes = array();
      for ($hole_num = 1; $hole_num <= $number_of_holes; $hole_num++) {
        $par = $params['hole'. $hole_num];
        $hole = new Hole(array(
          'hole_num' => $hole_num,
          'par' => $par
        ));
        $holes[] = $hole;
        $errors = array_merge($errors, $hole->errors());
      }

      if (count($errors) == 0) {
        // Course and holes were all valid
        $courseid = $course->save();
        $course->number_of_holes = count($holes);

        foreach ($holes as $hole) {
          $hole->courseid = $courseid;
          $hole->save();
        }

        // Clear cached pages
        Cache::clear();

        Redirect::to('/course/'. $courseid, array('message' => 'Rata ja sen väylät lisätty.'));
      } else {
        View::make('course/new.html', array('errors' => $errors,
                                            'attributes' => $params,
                                            'hole_count' => $params['hole_count']));
      }
    }

    public static function edit($courseid) {
      $course = Course::find($courseid);
      $attributes = array(
        'courseid' => $course->courseid,
        'name' => $course->name,
        'city' => $course->city,
        'map' => $course->map
      );

      $hole_num = 1;
      foreach (Hole::course_holes($course->courseid) as $hole) {
        $attributes = array_merge($attributes, array('hole'. $hole_num => $hole->par));
        $hole_num++;
      }

      View::make('course/edit.html', array('attributes' => $attributes, 'hole_count' => $course->number_of_holes));
    }

    public static function update($courseid) {
      $params = $_POST;
      $params['courseid'] = $courseid;

      $course_params = array(
        'courseid' => $courseid,
        'name' => $params['name'],
        'city' => $params['city'],
        'map' => $params['map']
      );
      $course = new Course($course_params);
      $errors = $course->errors();

      // Check hole validity before saving anything
      $holes = Hole::course_holes($courseid);

      foreach ($holes as $hole) {
        $hole->par = $params['hole'. $hole->hole_num];
        $errors = array_merge($errors, $hole->errors());
      }

      if (count($errors) == 0) {
        // Course and holes were all valid
        $course->update();

        foreach ($holes as $hole) {
          $hole->update();
        }

        // Clear cached pages
        Cache::clear();

        Redirect::to('/course/'. $courseid, array('message' => 'Rata ja sen väylät päivitetty.'));
      } else {
        View::make('course/edit.html', array('errors' => $errors,
                                            'attributes' => $params,
                                            'hole_count' => $params['hole_count']));
      }
    }

    public static function destroy($courseid) {
      // Destroy both the course and its holes.
      // Also destroy all games and scores on the course.
      $course = Course::find($courseid);
      $holes = Hole::course_holes($course->courseid);
      $games = Game::course_games($courseid);

      foreach ($games as $game) {
        GameController::destroy_no_redirect($game->gameid);
      }

      foreach ($holes as $hole) {
        $hole->destroy();
      }

      $course->destroy();

      // Clear cached pages
      Cache::clear();

      Redirect::to('/course', array('message' => 'Rata ja sen väylät poistettu. Kaikki radan pelit poistettu.'));
    }
  }
