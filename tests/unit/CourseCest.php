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

    public function correctCountOfAllCourses(UnitTester $I) {
        $I->assertEquals(4, Course::count_all());
    }

    public function returnAllReturnsAllCourses(UnitTester $I) {
        $courses = Course::all();
        $I->assertTrue($this->_arrayContainsCourseWithName($courses, "Hirvikuja", $this->course->courseid));
        $I->assertTrue($this->_arrayContainsCourseWithName($courses, "Tali", 1));
        $I->assertTrue($this->_arrayContainsCourseWithName($courses, "Kivikko", 2));
    }

    public function allOrderByNameOrdersCorrectly(UnitTester $I) {
        $courses = Course::all_order_by_name();
        $I->assertEquals("Hirvikuja", $courses[0]->name);
        $I->assertEquals("Kivikko", $courses[1]->name);
        $I->assertEquals("Tali", $courses[2]->name);
        $I->assertEquals("Ärjälä", $courses[3]->name);
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
