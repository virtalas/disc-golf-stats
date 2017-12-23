<?php


class ContestCest {

    private $contest;
    private $contest2;

    private $tie_points;
    private $tie_contest_points_calculated = false;

    public function _before(UnitTester $I) {
        $this->contest = new Contest(array(
            'creator' => 1,
            'name' => "Winter Cup",
            'number_of_games' => 5));
        $this->contest2 = new Contest(array(
            'creator' => 1,
            'name' => "Summer Cup",
            'number_of_games' => 1));
        $this->contest->save();
        $this->contest2->save();

        if (!$this->tie_contest_points_calculated) {
            $this->tie_points = $this->_tieContestPoints();
            $this->tie_contest_points_calculated = true;
        }
    }

    public function _after(UnitTester $I) {
        $this->contest->destroy();
        $this->contest2->destroy();
    }

    public function contestCanBeSaved(UnitTester $I) {
        $I->seeInDatabase('contest', ['creator' => "1", 'name' => "Winter Cup", 'number_of_games' => "5"]);
    }

    public function contestCanBeUpdated(UnitTester $I) {
        $this->contest->name = "Summer Cup";
        $this->contest->number_of_games = 1;
        $this->contest->update();
        $I->seeInDatabase('contest', ['name' => "Summer Cup", 'number_of_games' => "1", "contestid" => $this->contest->contestid]);
    }

    public function contestCanBeDestroyed(UnitTester $I) {
        $this->contest->destroy();
        $I->dontSeeInDatabase('contest', ['creator' => "1", 'name' => "Winter Cup", 'number_of_games' => "5", "contestid" => $this->contest->contestid]);
    }

    public function contestHasCorrectCreator(UnitTester $I) {
        $player = new Player(array(
          'playerid' => 1,
          'firstname' => "Admin",
          'lastname' => "Admin",
          'username' => "admin"
        ));
        $I->assertTrue($this->contest->is_creator($player));
        $player->playerid = "1";
        $I->assertTrue($this->contest->is_creator($player));
        $player->playerid = -1;
        $I->assertFalse($this->contest->is_creator($player));
        $player->playerid = 0;
        $I->assertFalse($this->contest->is_creator($player));
        $player->playerid = 2;
        $I->assertFalse($this->contest->is_creator($player));
        $player->playerid = "2";
        $I->assertFalse($this->contest->is_creator($player));
        $player->playerid = "";
        $I->assertFalse($this->contest->is_creator($player));
        $player->playerid = "a";
        $I->assertFalse($this->contest->is_creator($player));
    }

    public function contestCanBeFoundById(UnitTester $I) {
        $found_contest = Contest::find($this->contest->contestid);
        $I->assertEquals($this->contest->contestid, $found_contest->contestid);
        $I->assertEquals($this->contest->creator, $found_contest->creator);
        $I->assertEquals($this->contest->name, $found_contest->name);
        $I->assertEquals($this->contest->number_of_games, $found_contest->number_of_games);
    }

    public function returnAllReturnsCorrectNumberOfContests(UnitTester $I) {
        $contests = Contest::all();
        $I->assertTrue(sizeof($contests) === 5);
        $I->assertTrue($this->_arrayContainsContestWithName($contests, "Winter Cup", $this->contest->contestid));
        $I->assertTrue($this->_arrayContainsContestWithName($contests, "Summer Cup", $this->contest2->contestid));
    }

    /*

    CONTESTS FOR TESTING:

    Tyhjä kisa (id=37)

    Voitto pisteillä (id=59)

    Tasapeli voitolla ja häviöllä (id=17)
    1. event: Admin -5, Matti -3, Teppo -2, Seppo -1, Esko 0
    2. event: Admin -4, Matti -4
    3. event: Admin -4, Matti -5
    4. event: Admin -4, Matti -4, Teppo +2, Seppo +3, Esko +4

    */

    public function correctNumberOfGamesPlayedWhenZeroGames(UnitTester $I) {
        $contests = Contest::all_with_games_played();
        $I->assertTrue($this->_arrayContainsContestWithGameCount($contests, 0, 37));
    }

    public function correctNumberOfGamesPlayedWhenMoreThanZeroGames(UnitTester $I) {
        $contests = Contest::all_with_games_played();
        $I->assertTrue($this->_arrayContainsContestWithGameCount($contests, 4, 17));
    }

    public function eventWinnersWhoAreTiedGetBothFourPoints(UnitTester $I) {
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][1]);
        $I->assertEquals(4, $this->tie_points["Matti"]["game_points"][1]);
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][3]);
        $I->assertEquals(4, $this->tie_points["Matti"]["game_points"][3]);
    }

    public function contestWinnerChosenByParWhenTiedByPoints(UnitTester $I) {
        $this->_contestWinnerIs("Admin", $this->tie_points, $I);
    }

    public function contestWinnerChosenByPointsWhenNoTies(UnitTester $I) {
        $points = $this->_contestPoints(59);
        $this->_contestWinnerIs("Admin", $points, $I);
    }

    public function eventPointsDistributedCorrectlyWhenWinnersTied(UnitTester $I) {
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][3]);
        $I->assertEquals(4, $this->tie_points["Matti"]["game_points"][3]);
        $I->assertEquals(2, $this->tie_points["Teppo"]["game_points"][3]);
        $I->assertEquals(1, $this->tie_points["Seppo"]["game_points"][3]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][3]);
    }

    public function eventPointsDistributedCorrectlyWhenNoTies(UnitTester $I) {
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][0]);
        $I->assertEquals(3, $this->tie_points["Matti"]["game_points"][0]);
        $I->assertEquals(2, $this->tie_points["Teppo"]["game_points"][0]);
        $I->assertEquals(1, $this->tie_points["Seppo"]["game_points"][0]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][0]);
    }

    public function eventPointsDistributedCorrectlyWhenOnlyTwoPlayers(UnitTester $I) {
        $points = $this->_contestPoints(59);
        $I->assertEquals(4, $points["Admin"]["game_points"][0]);
        $I->assertEquals(3, $points["Matti"]["game_points"][0]);
    }

    public function skippedEventGivesZeroPoints(UnitTester $I) {
        $I->assertEquals(0, $this->tie_points["Teppo"]["game_points"][1]);
        $I->assertEquals(0, $this->tie_points["Teppo"]["game_points"][2]);
        $I->assertEquals(0, $this->tie_points["Seppo"]["game_points"][1]);
        $I->assertEquals(0, $this->tie_points["Seppo"]["game_points"][2]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][0]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][1]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][2]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][3]);
    }

    public function skippedEventGivesZeroPointsForFirstEvent(UnitTester $I) {
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][0]);
    }

    public function playerParsCalculatedCorrectly(UnitTester $I) {
        $I->assertEquals("-17", $this->tie_points["Admin"]["to_par"]);
        $I->assertEquals("-16", $this->tie_points["Matti"]["to_par"]);
        $I->assertEquals("0", $this->tie_points["Teppo"]["to_par"]);
        $I->assertEquals("+2", $this->tie_points["Seppo"]["to_par"]);
        $I->assertEquals("+4", $this->tie_points["Esko"]["to_par"]);
    }

    public function playersTotalPointsCalculatedCorrectly(UnitTester $I) {
        $I->assertEquals(15, $this->tie_points["Admin"]["total_points"]);
        $I->assertEquals(15, $this->tie_points["Matti"]["total_points"]);
        $I->assertEquals(4, $this->tie_points["Teppo"]["total_points"]);
        $I->assertEquals(2, $this->tie_points["Seppo"]["total_points"]);
        $I->assertEquals(0, $this->tie_points["Esko"]["total_points"]);
    }

    public function playersListedInOrderOfTotalPoints(UnitTester $I) {
        $order = array("Admin", "Matti", "Teppo", "Seppo", "Esko");
        $correct_order = true;
        $i = 0;

        foreach ($this->tie_points as $firstname => $data) {
            if ($firstname !== $order[$i]) $correct_order = false;
            $i++;
        }

        $I->assertTrue($correct_order);
    }

    public function allPointsCalculatedCorrectly(UnitTester $I) {
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][0]);
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][1]);
        $I->assertEquals(3, $this->tie_points["Admin"]["game_points"][2]);
        $I->assertEquals(4, $this->tie_points["Admin"]["game_points"][3]);

        $I->assertEquals(3, $this->tie_points["Matti"]["game_points"][0]);
        $I->assertEquals(4, $this->tie_points["Matti"]["game_points"][1]);
        $I->assertEquals(4, $this->tie_points["Matti"]["game_points"][2]);
        $I->assertEquals(4, $this->tie_points["Matti"]["game_points"][3]);

        $I->assertEquals(2, $this->tie_points["Teppo"]["game_points"][0]);
        $I->assertEquals(0, $this->tie_points["Teppo"]["game_points"][1]);
        $I->assertEquals(0, $this->tie_points["Teppo"]["game_points"][2]);
        $I->assertEquals(2, $this->tie_points["Teppo"]["game_points"][3]);

        $I->assertEquals(1, $this->tie_points["Seppo"]["game_points"][0]);
        $I->assertEquals(0, $this->tie_points["Seppo"]["game_points"][1]);
        $I->assertEquals(0, $this->tie_points["Seppo"]["game_points"][2]);
        $I->assertEquals(1, $this->tie_points["Seppo"]["game_points"][3]);

        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][0]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][1]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][2]);
        $I->assertEquals(0, $this->tie_points["Esko"]["game_points"][3]);
    }

    /*
    *  Validator tests
    */

    public function nameCannotBeEmpty(UnitTester $I) {
        $I->assertTrue(sizeof($this->contest->validate_name()) === 0);

        $this->contest->name = "";
        $I->assertTrue(sizeof($this->contest->validate_name()) > 0);
    }

    public function numberOfEventsCannotBeNegative(UnitTester $I) {
        $I->assertTrue(sizeof($this->contest->validate_number_of_games()) === 0);

        $this->contest->number_of_games = -1;
        $I->assertTrue(sizeof($this->contest->validate_number_of_games()) > 0);
    }

    /*
    *  Functions with "_" prefix are not run as tests
    */

    private function _arrayContainsContestWithName($contests, $name, $id) {
        for ($i = 0; $i < sizeof($contests); $i++) {
            if ($contests[$i]->name == $name && $contests[$i]->contestid == $id) return true;
        }
        return false;
    }

    private function _arrayContainsContestWithGameCount($contests, $count, $id) {
        for ($i = 0; $i < sizeof($contests); $i++) {
            if ($contests[$i]->games_played == $count && $contests[$i]->contestid == $id) return true;
        }
        return false;
    }

    private function _contestPoints($id) {
        $tieContest = Contest::find($id);
        $games = Game::contest_games($tieContest->contestid);
        return Contest::points($tieContest, $games);
    }

    private function _tieContestPoints() {
        return $this->_contestPoints(17);
    }

    private function _contestWinnerIs($firstname, $points, $I) {
        reset($points);
        $firstkey = key($points);
        $I->assertEquals($firstname, $firstkey);
    }
}
