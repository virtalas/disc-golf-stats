<?php

  class DB{

      public static function connection(){
        // Haetaan tietokantakonfiguraatio
        $connection_config = DatabaseConfig::connection_config();
        $config = $connection_config['config'];

        try {
            // Alustetaan PDO
            if(isset($config['username'])){
              $connection = new PDO($config['resource'], $config['username'], $config['password']);
            }else{
              $connection = new PDO($config['resource']);
            }

            // Asetetaan tietokannan kenttien koodaukseksi utf8
            $connection->exec("set names utf8");

        } catch (PDOException $e) {
            die('Virhe tietokantayhteydessä: ' . $e->getMessage());
        }

        return $connection;
      }

      public static function test_connection(){
        require 'vendor/connectiontest/connectiontest.php';

        exit();
      }

  }
