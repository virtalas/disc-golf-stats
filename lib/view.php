<?php

  class View{

    public static function make($view, $content = array()){
      // Alustetaan Twig
      $twig = self::get_twig();
      $rendered_page = "";

      try{
        // Asetetaan uudelleenohjauksen yhteydessä lisätty viesti
        self::set_flash_message($content);

        // Asetetaan näkymään base_path-muuttuja index.php:ssa määritellyllä BASE_PATH vakiolla
        $content['base_path'] = BASE_PATH;

        // Asetetaan näkymään kirjautunut käyttäjä, jos get_user_logged_in-metodi on toteutettu
        if(method_exists('BaseController', 'get_user_logged_in')){
          $content['user_logged_in'] = BaseController::get_user_logged_in();
        }

        // Asetetaan näkymään kirjautunut admin-käyttäjä, jos get_admin_logged_in-metodi on toteutettu
        if(method_exists('BaseController', 'get_admin_logged_in')){
          $content['admin_logged_in'] = BaseController::get_admin_logged_in();
        }

        // Tulostetaan Twig:n renderöimä näkymä
        $rendered_page = $twig->render($view, $content);
        echo $rendered_page;
      } catch (Exception $e){
        die('Virhe näkymän näyttämisessä: ' . $e->getMessage());
      }

      // exit();
      return $rendered_page;
    }

    private static function get_twig(){
      Twig_Autoloader::register();

      $twig_loader = new Twig_Loader_Filesystem('app/views');

      return new Twig_Environment($twig_loader);
    }

    private static function set_flash_message(&$content){
      if(isset($_SESSION['flash_message'])){

        $flash = json_decode($_SESSION['flash_message']);

        foreach($flash as $key => $value){
          $content[$key] = $value;
        }

        unset($_SESSION['flash_message']);
      }
    }

  }
