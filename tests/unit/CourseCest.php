<?php


class CourseCest {

    private $course;
    private $course2;

    public function _before(UnitTester $I) {
        $this->course = new Course(array(
            'name' => "Hirvikuja",
            'city' => "Helsinki",
            'map' => "https://kartta.com/123"));
        $this->course2 = new Course(array(
            'name' => "Ärjälä",
            'city' => "Örjölä",
            'map' => "https://kartta.com/321"));

        $this->course->save();
        $this->course2->save();
    }

    public function _after(UnitTester $I) {
        $this->course->destroy();
        $this->course2->destroy();
    }

    public function courseCanBeSaved(UnitTester $I) {
        $I->seeInDatabase('course', ['name' => "Hirvikuja", 'city' => "Helsinki", 'map' => "https://kartta.com/123", "courseid" => $this->course->courseid]);
    }

    public function courseCanBeUpdated(UnitTester $I) {
        $this->course->name = "Hirvimetsä";
        $this->course->city = "Espoo";
        $this->course->map = "";
        $this->course->update();
        $I->seeInDatabase('course', ['name' => "Hirvimetsä", 'city' => "Espoo", 'map' => "", "courseid" => $this->course->courseid]);
    }

    public function courseCanBeDestroyed(UnitTester $I) {
        $this->course->destroy();
        $I->dontSeeInDatabase('course', ['name' => "Hirvimetsä", 'city' => "Espoo", 'map' => "", "courseid" => $this->course->courseid]);
    }

    public function courseCanBeFoundById(UnitTester $I) {
        $found_course = Course::find($this->course->courseid);
        $I->assertEquals($this->course->courseid, $found_course->courseid);
        $I->assertEquals($this->course->name, $found_course->name);
        $I->assertEquals($this->course->map, $found_course->map);
    }

    public function countOfAllCourses(UnitTester $I) {
        $count = $I->grabFromDatabase("course", 'COUNT(*)');
        $I->assertEquals($count, Course::count_all());
    }

    public function returnAll(UnitTester $I) {
        $courses = Course::all();
        $count = $I->grabFromDatabase("course", 'COUNT(*)');
        $I->assertEquals($count, sizeof($courses));
        $I->assertTrue($this->_arrayContainsCourseWithName($courses, "Hirvikuja", $this->course->courseid));
        $I->assertTrue($this->_arrayContainsCourseWithName($courses, "Tali", 1));
        $I->assertTrue($this->_arrayContainsCourseWithName($courses, "Kivikko", 2));
    }

    public function allOrderByName(UnitTester $I) {
        $courses = Course::all_order_by_name();
        // Check that the names in $names appear in this order in $courses.
        $names = array("Hirvikuja", "Kivikko", "Tali", "Ärjälä");
        $i = 0;

        foreach ($courses as $key => $course) {
            if ($names[$i] === $course->name) $i++;
            if ($i >= sizeof($names) - 1) break;
        }

        $I->assertEquals(3, $i);
    }

    public function preparingCourseLoadsHolesAndPars(UnitTester $I) {
        $tali = Course::find(1);
        $tali->prepare();
        $I->assertEquals(5, $tali->holes[0]->par);
        $I->assertEquals(3, $tali->holes[1]->par);
        $I->assertEquals(4, $tali->holes[16]->par);
    }

    public function highScoresListsUpToFiveGames(UnitTester $I) {
        $scores = Course::high_scores(2);
        $I->assertEquals(5, sizeof($scores));
    }

    public function highScoresAreInNumericalOrder(UnitTester $I) {
        $scores = Course::high_scores(2);
        $prev_score = -99999;

        foreach ($scores as $key => $high_score) {
            $total_score = $high_score[2];
            $I->assertTrue($prev_score <= $total_score);
            $prev_score = $total_score;
        }
    }

    public function highScoreListParsAreCorrect(UnitTester $I) {
        $scores = Course::high_scores(2);
        $course_par = Course::find(2)->par;

        foreach ($scores as $key => $high_score) {
            $total_score = $high_score[2];
            $real_to_par = $total_score - $course_par;
            $given_to_par = str_replace("+", "", $high_score[3]);
            $I->assertEquals($real_to_par, $given_to_par);
        }
    }

    public function highScoreListNewerGameIsPreferred(UnitTester $I) {
        $scores = Course::high_scores(2);
        $I->assertEquals($scores[0][2], $scores[1][2]);
        $I->assertEquals("Admin", $scores[0][1]);
        $I->assertEquals("Seppo", $scores[1][1]);
    }

    public function gamesPlayedOnCourse(UnitTester $I) {
        // 127: RataJollaKaksiPelia
        $I->assertEquals(2, Course::number_of_games_played(127));
        $game = new Game(array('courseid' => 127, 'creator' => 1));
        $game->save();
        $I->assertEquals(3, Course::number_of_games_played(127));
        $game->destroy();
        $I->assertEquals(2, Course::number_of_games_played(127));
    }

    public function par(UnitTester $I) {
        $I->assertEquals(58, Course::par(1));
        $I->assertEquals(56, Course::par(2));
    }

    public function latestGameDate(UnitTester $I) {
        $date_input = date("Y-m-d H:i");
        $date_output = date("H:i d.m.Y");
        $game = new Game(array('courseid' => 1, 'creator' => 1, 'gamedate' => $date_input));
        $game->save();
        $I->assertEquals($date_output, Course::latest_game_date(1));
        $game->destroy();
    }

    /*
    *  Functions with "_" prefix are not run as tests
    */

    private function _arrayContainsCourseWithName($courses, $name, $id) {
        for ($i = 0; $i < sizeof($courses); $i++) {
            if ($courses[$i]->name == $name && $courses[$i]->courseid == $id) return true;
        }
        return false;
    }
}
