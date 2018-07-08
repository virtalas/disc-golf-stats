<?php
  class UserController extends BaseController {

    public static function login() {
        View::make('user/login.html');
    }

    public static function handle_login() {
      $params = $_POST;

      $user = Player::authenticate($params['username'], $params['password']);

      if (!$user) {
        View::make('user/login.html', array('error' => 'Väärä käyttäjätunnus tai salasana!',
                                            'username' => $params['username']));
      } else {
        $_SESSION['user'] = $user->playerid;
        setcookie("user", $user->playerid);

        // Redirect::to('/', array('message' => 'Tervetuloa takaisin ' . $user->firstname . '!'));
        Redirect::to('/');

      }
    }

    public static function logout() {
      $_SESSION['user'] = null;
      Redirect::to('/login', array('message' => 'Olet kirjautunut ulos.'));
    }

    public static function register() {
      View::make('user/register.html');
    }

    public static function handle_register() {
      $firstname = $_POST['firstname'];
      $username = $_POST['username'];
      $password = $_POST['password'];
      $password2 = $_POST['password2'];

      $errors = array();

      if ($firstname == null) {
        $errors[] = "Syötä nimesi.";
      }
      if ($username == null) {
        $errors[] = "Syötä uusi käyttäjätunnus.";
      }
      if (Player::username_exists($username)) {
        $errors[] = "Käyttäjätunnus on jo käytössä. Valitse toinen käyttäjätunnus.";
      }
      if ($password == null || $password2 == null) {
        $errors[] = "Syötä uusi salasana.";
      }
      if ($password != $password2) {
        $errors[] = "Annetut salasanat eivät täsmänneet.";
      }

      if ($errors != null) {
        View::make('user/register.html', array('errors' => $errors,
                                              'username' => $username,
                                              'firstname' => $firstname));
      } else {
        // Register user/player and login
        $salt = mcrypt_create_iv(50, MCRYPT_DEV_URANDOM);
        $salt = mb_convert_encoding($salt, "UTF-8", "UTF-8");

        $player = new Player(array(
          'firstname' => $firstname,
          'username' => $username,
          'password' => crypt($password, $salt),
          'salt' => $salt,
          'guest' => false
        ));
        $player->save();

        // POST params for signing in are the same in register.html and login.html
        self::handle_login();
      }
    }
  }
?>
