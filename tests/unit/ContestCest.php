<?php


class ContestCest {

    private $contest;

    public function _before(UnitTester $I) {
        $this->contest = new Contest(array(
            'creator' => 1,
            'name' => "Winter Cup",
            'number_of_games' => 5));
    }

    public function _after(UnitTester $I) {
    }

    public function contestCanBeSaved(UnitTester $I) {
        $this->contest->save();
        $I->seeInDatabase('contest', ['creator' => "1", 'name' => "Winter Cup", 'number_of_games' => "5"]);
    }
}
