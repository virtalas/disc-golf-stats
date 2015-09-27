<?php
  class UserController extends BaseController {

    public static function login() {
        View::make('user/login.html');
    }

    public static function handle_login() {
      $params = $_POST;

      $user = Player::authenticate($params['username'], $params['password']);

      if (!$user) {
        View::make('user/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!', 'username' => $params['username']));
      } else {
        $_SESSION['user'] = $user->playerid;

        Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $user->firstname . '!'));
      }
    }
  }
?>