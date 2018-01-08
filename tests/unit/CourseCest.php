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

    public function highScoreListToPars(UnitTester $I) {
        $scores = Course::high_scores(2);
        $course_par = Course::find(2)->par;

        foreach ($scores as $key => $high_score) {
            $total_score = $high_score[2];
            $real_to_par = $total_score - $course_par;
            $given_to_par = str_replace("+", "", $high_score[3]);
            $I->assertEquals($real_to_par, $given_to_par);
        }
    }

    public function highScoreListToParUnderPar(UnitTester $I) {
        $scores = Course::high_scores(127);
        $course_par = Course::find(127)->par;

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

    public function gamesPlayedOnCourseNullWhenNonexistantCourse(UnitTester $I) {
        $I->assertEquals(null, Course::number_of_games_played(-1));
        $I->assertEquals(null, Course::number_of_games_played(10));
    }

    public function par(UnitTester $I) {
        $I->assertEquals(58, Course::par(1));
        $I->assertEquals(56, Course::par(2));
    }

    public function parNullWhenNonexistantCourse(UnitTester $I) {
        $I->assertEquals(null, Course::par(-1));
        $I->assertEquals(null, Course::par(10));
    }

    public function latestGameDate(UnitTester $I) {
        $date_input = date("Y-m-d H:i");
        $date_output = date("H:i d.m.Y");
        $game = new Game(array('courseid' => 1, 'creator' => 1, 'gamedate' => $date_input));
        $game->save();
        $I->assertEquals($date_output, Course::latest_game_date(1));
        $game->destroy();
    }

    public function averageScoringOverPar(UnitTester $I) {
        // 194: over par
        $I->assertEquals("66.5 (+6.5)", Course::average_scoring(194));
    }

    public function averageScoringUnderPar(UnitTester $I) {
        // 195: under par
        $I->assertEquals("49.67 (-4.33)", Course::average_scoring(195));
    }

    public function averageScoringDoesntCountIllegalGames(UnitTester $I) {
        // 237: Laittomia pelejä ei lasketa tuloskeskiarvoon
        $I->assertEquals("54 (0)", Course::average_scoring(237));
    }

    public function averagePlayerScoringByYearReturnsAllYears(UnitTester $I) {
        // 236: Vuosittaiset tuloskeskiarvot
        $I->assertFalse(empty(Course::average_player_scoring_by_year(1, 2015)));
        $I->assertFalse(empty(Course::average_player_scoring_by_year(1, 2016)));
        $I->assertFalse(empty(Course::average_player_scoring_by_year(1, 2017)));
    }

    public function averagePlayerScoringByYearReturnsEmptyForYearWithNoGames(UnitTester $I) {
        $avg = Course::average_player_scoring_by_year(1, 2014);
        foreach ($avg as $key => $value) {
            $I->assertTrue($value["avg_score"] == "");
        }
    }

    public function averagePlayerScoringByYearWhenOneGame(UnitTester $I) {
        $avg = Course::average_player_scoring_by_year(1, 2015);
        $index = -1;
        for ($i = 0; $i < sizeof($avg); $i++) {
            if ($avg[$i]["courseid"] == 236) $index = $i;
        }
        $I->assertEquals(54.0, (double) $avg[$index]["avg_score"]);
    }

    public function averagePlayerScoringByYearWhenMoreThanOneGame(UnitTester $I) {
        $avg = Course::average_player_scoring_by_year(1, 2016);
        $index = -1;
        for ($i = 0; $i < sizeof($avg); $i++) {
            if ($avg[$i]["courseid"] == 236) $index = $i;
        }
        $I->assertEquals(55.5, (double) $avg[$index]["avg_score"]);
    }

    public function averagePlayerScoringByYearIllegalGamesNotCounted(UnitTester $I) {
        $avg = Course::average_player_scoring_by_year(1, 2017);
        $index = -1;
        for ($i = 0; $i < sizeof($avg); $i++) {
            if ($avg[$i]["courseid"] == 236) $index = $i;
        }
        $I->assertEquals(54.0, (double) $avg[$index]["avg_score"]);
    }

    public function popularCoursesByPlayer(UnitTester $I) {
        $games = array();
        for ($i = 0; $i < 30; $i++) {
            $game = new Game(array("courseid" => 1));
        }
    }

    public function chronologicalHighScoresForOnePlayerFirstGameIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and player admin.
        $high_scores = Course::chronological_high_scores(292, 1);
        $I->assertEquals($high_scores["2018-01-01"], 62);
    }

    public function chronologicalHighScoresForOnePlayerBetterScoreIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and player admin.
        $high_scores = Course::chronological_high_scores(292, 1);
        $I->assertEquals($high_scores["2018-01-03"], 58);
    }

    public function chronologicalHighScoresForOnePlayerSameScoreAsPreviosBestIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and player admin.
        $high_scores = Course::chronological_high_scores(292, 1);
        $I->assertEquals($high_scores["2018-01-04"], 58);
    }

    public function chronologicalHighScoresForAllPlayersFirstGameIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and all players.
        $high_scores = Course::chronological_high_scores(292, 0);
        $I->assertEquals($high_scores["2018-01-01"], 62);
    }

    public function chronologicalHighScoresForAllPlayersBetterScoreIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and all players.
        $high_scores = Course::chronological_high_scores(292, 0);
        $I->assertEquals($high_scores["2018-01-03"], 58);
    }

    public function chronologicalHighScoresForAllPlayersBetterScoreByDifferentPlayerIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and all players.
        $high_scores = Course::chronological_high_scores(292, 0);
        $I->assertEquals($high_scores["2018-01-05"], 53);
    }

    public function chronologicalHighScoresForAllPlayersSameScoreAsPreviosBestIsHighScore(UnitTester $I) {
        // Course "Chronological high scores" and all players.
        $high_scores = Course::chronological_high_scores(292, 0);
        $I->assertEquals($high_scores["2018-01-04"], 58);
    }

    public function scoreDistributionForDifferentPars(UnitTester $I) {
        $dist = Course::score_distribution(453);
        $I->assertEquals(1, $dist[0]["hole_num"]);
        $I->assertEquals(0, $dist[0]["hole_in_one"]);
        $I->assertEquals(0, $dist[0]["eagle"]);
        $I->assertEquals(0, $dist[0]["birdie"]);
        $I->assertEquals(2, $dist[0]["par"]);
        $I->assertEquals(0, $dist[0]["bogey"]);
        $I->assertEquals(0, $dist[0]["over_bogey"]);

        $I->assertEquals(2, $dist[1]["hole_num"]);
        $I->assertEquals(0, $dist[1]["hole_in_one"]);
        $I->assertEquals(0, $dist[1]["eagle"]);
        $I->assertEquals(0, $dist[1]["birdie"]);
        $I->assertEquals(2, $dist[1]["par"]);
        $I->assertEquals(0, $dist[1]["bogey"]);
        $I->assertEquals(0, $dist[1]["over_bogey"]);

        $I->assertEquals(3, $dist[2]["hole_num"]);
        $I->assertEquals(0, $dist[2]["hole_in_one"]);
        $I->assertEquals(0, $dist[2]["eagle"]);
        $I->assertEquals(0, $dist[2]["birdie"]);
        $I->assertEquals(2, $dist[2]["par"]);
        $I->assertEquals(0, $dist[2]["bogey"]);
        $I->assertEquals(0, $dist[2]["over_bogey"]);
    }

    public function scoreDistributionForBogies(UnitTester $I) {
        $dist = Course::score_distribution(453);
        $I->assertEquals(4, $dist[3]["hole_num"]);
        $I->assertEquals(0, $dist[3]["hole_in_one"]);
        $I->assertEquals(0, $dist[3]["eagle"]);
        $I->assertEquals(0, $dist[3]["birdie"]);
        $I->assertEquals(1, $dist[3]["par"]);
        $I->assertEquals(1, $dist[3]["bogey"]);
        $I->assertEquals(0, $dist[3]["over_bogey"]);
    }

    public function scoreDistributionForBogiesWithOb(UnitTester $I) {
        $dist = Course::score_distribution(453);
        $I->assertEquals(5, $dist[4]["hole_num"]);
        $I->assertEquals(0, $dist[4]["hole_in_one"]);
        $I->assertEquals(0, $dist[4]["eagle"]);
        $I->assertEquals(0, $dist[4]["birdie"]);
        $I->assertEquals(1, $dist[4]["par"]);
        $I->assertEquals(1, $dist[4]["bogey"]);
        $I->assertEquals(0, $dist[4]["over_bogey"]);
    }

    public function scoreDistributionForBirdies(UnitTester $I) {
        $dist = Course::score_distribution(453);
        $I->assertEquals(6, $dist[5]["hole_num"]);
        $I->assertEquals(0, $dist[5]["hole_in_one"]);
        $I->assertEquals(0, $dist[5]["eagle"]);
        $I->assertEquals(1, $dist[5]["birdie"]);
        $I->assertEquals(1, $dist[5]["par"]);
        $I->assertEquals(0, $dist[5]["bogey"]);
        $I->assertEquals(0, $dist[5]["over_bogey"]);
    }

    public function scoreDistributionForEagles(UnitTester $I) {
        $dist = Course::score_distribution(453);
        $I->assertEquals(7, $dist[6]["hole_num"]);
        $I->assertEquals(0, $dist[6]["hole_in_one"]);
        $I->assertEquals(1, $dist[6]["eagle"]);
        $I->assertEquals(0, $dist[6]["birdie"]);
        $I->assertEquals(1, $dist[6]["par"]);
        $I->assertEquals(0, $dist[6]["bogey"]);
        $I->assertEquals(0, $dist[6]["over_bogey"]);
    }

    public function scoreDistributionForAces(UnitTester $I) {
        $dist = Course::score_distribution(453);
        $I->assertEquals(8, $dist[7]["hole_num"]);
        $I->assertEquals(1, $dist[7]["hole_in_one"]);
        $I->assertEquals(0, $dist[7]["eagle"]);
        $I->assertEquals(0, $dist[7]["birdie"]);
        $I->assertEquals(1, $dist[7]["par"]);
        $I->assertEquals(0, $dist[7]["bogey"]);
        $I->assertEquals(0, $dist[7]["over_bogey"]);
    }

    public function coursePopularity(UnitTester $I) {
        $found = false;
        foreach (Course::course_popularity() as $key => $row) {
            if ($row["name"] === "Rata jolla kaksi peliä") {
                $found = true;
                $I->assertEquals(2, $row["count"]);
            }
        }
        $I->assertTrue($found);
    }

    public function coursePopularityForPlayerWhoHasPlayedOnCourse(UnitTester $I) {
        $found = false;
        foreach (Course::player_course_popularity(5) as $key => $row) {
            if ($row["name"] === "Rata jolla kaksi peliä") {
                $found = true;
                $I->assertEquals(2, $row["count"]);
            }
        }
        $I->assertTrue($found);
    }

    public function coursePopularityForPlayerWhoHasNotPlayedOnCourse(UnitTester $I) {
        $found = false;
        foreach (Course::player_course_popularity(1) as $key => $row) {
            if ($row["name"] === "Rata jolla kaksi peliä") {
                $found = true;
            }
        }
        $I->assertFalse($found);
    }

    public function nameCannotBeEmpty(UnitTester $I) {
        $this->course->name = "";
        $I->assertFalse(empty($this->course->validate_name()));

        $this->course->name = "Name";
        $I->assertTrue(empty($this->course->validate_name()));
    }

    public function cityCannotBeEmpty(UnitTester $I) {
        $this->course->city = "";
        $I->assertFalse(empty($this->course->validate_city()));

        $this->course->city = "City";
        $I->assertTrue(empty($this->course->validate_city()));
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
