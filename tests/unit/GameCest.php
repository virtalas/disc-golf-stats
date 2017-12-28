<?php


class GameCest {
    public function _before(UnitTester $I) {
    }

    public function _after(UnitTester $I) {
    }

    public function firstGameOnCourseIsHighScore(UnitTester $I) {
        $scores = Course::high_scores(2);
    }

    public function sameScoreAsHighScoreIsNotHighScore(UnitTester $I) {
        $scores = Course::high_scores(2);
    }

    public function scoreThatIsBetterThanPreviousScoresIsHighScore(UnitTester $I) {
        $scores = Course::high_scores(2);
    }

    public function worseScoreIsNotHighScore(UnitTester $I) {
        $scores = Course::high_scores(2);
    }

    public function firstGameOnCourseIsHighScore(UnitTester $I) {
        $scores = Course::high_scores(2);
    }
}
