<?php

  class BaseController{

    public static function get_user_logged_in() {
      // Katsotaan onko user-avain sessiossa
      if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user'];
        // Pyydetään User-mallilta käyttäjä session mukaisella id:llä
        $user = Player::find($user_id);

        return $user;
      }

      // Käyttäjä ei ole kirjautunut sisään
      return null;
    }

    public static function get_admin_logged_in() {
      // Katsotaan onko user-avain sessiossa
      if (isset($_SESSION['user'])) {
        $user_id = $_SESSION['user'];
        // Pyydetään User-mallilta käyttäjä session mukaisella id:llä
        $user = Player::find($user_id);

        if (Player::is_admin($user_id)) {
          return $user;
        }

        return null;
      }

      // Käyttäjä ei ole kirjautunut sisään
      return null;
    }

    public static function check_logged_in() {
      if (!isset($_SESSION['user'])) {
        Redirect::to('/login', array('error' => 'Kirjaudu ensin sisään.'));
      }
    }

    public static function check_admin_logged_in() {
      if (!isset($_SESSION['user'])) {
        Redirect::to('/login', array('error' => 'Kirjaudu ensin sisään.'));
      } else if (!Player::is_admin($_SESSION['user'])) {
        Redirect::to('/stats', array('error' => 'Toiminto on mahdollinen vain admin-käyttäjille.'));
      }
    }
  }
