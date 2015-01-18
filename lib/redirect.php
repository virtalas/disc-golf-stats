<?php

  class Redirect{

    public static function to($path, $message = null){
      // Katsotaan onko $message parametri asetettu
      if(!is_null($message)){
        // Jos on, lisätään se sessioksi JSON-muodossa
        $_SESSION['flash_message'] = json_encode($message);
      }

      // Ohjataan käyttäjä annettuun polkuun
      header('Location: ' . BASE_PATH . $path);

      exit();
    }

  }
