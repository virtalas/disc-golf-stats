<?php


class CourseCest {

    private $course;

    public function _before(UnitTester $I) {
        $this->course = new Course(array(
            'name' => "Hirvikuja",
            'city' => "Helsinki",
            'map' => "https://kartta.com/123"));

        $this->course->save();
    }

    public function _after(UnitTester $I) {
        $this->course->destroy();
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
}
