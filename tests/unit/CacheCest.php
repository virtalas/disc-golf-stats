<?php


class CacheCest {
    
    public function cacheIsTurnedOn(UnitTester $I) {
        // Cache should be on when deploying or pushing to GitHub
        // but can be off for development purposes.
        $I->assertTrue(Cache::on());
    }
}
