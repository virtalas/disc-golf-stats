<?php


class ContestCest {

    private $contest;
    private $contest2;

    public function _before(UnitTester $I) {
        $this->contest = new Contest(array(
            'creator' => 1,
            'name' => "Winter Cup",
            'number_of_games' => 5));
        $this->contest2 = new Contest(array(
            'creator' => 1,
            'name' => "Summer Cup",
            'number_of_games' => 1));
    }

    public function _after(UnitTester $I) {
    }

    public function contestCanBeSaved(UnitTester $I) {
        $this->contest->save();
        $I->seeInDatabase('contest', ['creator' => "1", 'name' => "Winter Cup", 'number_of_games' => "5"]);
    }

    public function contestCanBeUpdated(UnitTester $I) {
        $this->contest->save();
        $this->contest->name = "Summer Cup";
        $this->contest->number_of_games = 1;
        $this->contest->update();
        $I->seeInDatabase('contest', ['name' => "Summer Cup", 'number_of_games' => "1", "contestid" => $this->contest->contestid]);
    }

    public function contestCanBeDestroyed(UnitTester $I) {
        $this->contest->save();
        $this->contest->destroy();
        $I->dontSeeInDatabase('contest', ['creator' => "1", 'name' => "Winter Cup", 'number_of_games' => "5", "contestid" => $this->contest->contestid]);
    }

    public function isCreatorOfContest(UnitTester $I) {
        $this->contest->save();
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

    public function contestCanBeFound(UnitTester $I) {
        $this->contest->save();
        $found_contest = Contest::find($this->contest->contestid);
        $I->assertEquals($this->contest->contestid, $found_contest->contestid);
        $I->assertEquals($this->contest->creator, $found_contest->creator);
        $I->assertEquals($this->contest->name, $found_contest->name);
        $I->assertEquals($this->contest->number_of_games, $found_contest->number_of_games);
    }

    public function returnAllContests(UnitTester $I) {
        $this->contest->save();
        $this->contest2->save();
        $contests = Contest::all();
        $I->assertTrue(sizeof($contests) >= 2);
        $I->assertTrue($this->_arrayContainsContestWithName($contests, "Winter Cup", $this->contest->contestid));
        $I->assertTrue($this->_arrayContainsContestWithName($contests, "Summer Cup", $this->contest2->contestid));
    }

    /*

    CONTESTS FOR TESTING:

    Tyhjä kisa (id=37)

    Tasapeli voitolla ja häviöllä (id=17)
    1. event: Admin -5, Matti -3
    2. event: Admin -4, Matti -4
    3. event: Admin -4, Matti -5
    4. event: Admin -4, Matti -4, Teppo +14

    */

    public function numberOfGamesPlayedWhenZeroGames(UnitTester $I) {
        $contests = Contest::all_with_games_played();
        $I->assertTrue($this->_arrayContainsContestWithGameCount($contests, 0, 37));
    }

    public function numberOfGamesPlayedWhenMoreThanZeroGames(UnitTester $I) {
        $contests = Contest::all_with_games_played();
        $I->assertTrue($this->_arrayContainsContestWithGameCount($contests, 4, 17));
    }

    public function eventTiedForFirstGetsBoth4Points(UnitTester $I) {
        $points = $this->_tieContestPoints();
        $I->assertEquals(4, $points["Admin"]["game_points"][1]);
        $I->assertEquals(4, $points["Matti"]["game_points"][1]);
        $I->assertEquals(4, $points["Admin"]["game_points"][3]);
        $I->assertEquals(4, $points["Matti"]["game_points"][3]);
    }

    public function contestWinnerChosenByParWhenTiedByPoints(UnitTester $I) {
        $points = $this->_tieContestPoints();
        reset($points);
        $first_key = key($points);
        $I->assertEquals("Admin", $first_key);
    }

    // Functions with "_" prefix are not run as tests

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

    private function _tieContestPoints() {
        $tieContest = Contest::find(17);
        $games = Game::contest_games($tieContest->contestid);
        return Contest::points($tieContest, $games);
    }
}
