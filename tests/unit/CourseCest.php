<?php


class CourseCest{

    private $course;

    public function _before(UnitTester $I){
        $this->course = new Course(array(
            'name' => $params['name'],
            'city' => $params['city'],
            'map' => $params['map']));
    }

    public function _after(UnitTester $I){
    }

    
}
