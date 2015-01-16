<?php

  class DB{

      public static function connection(){
        $connection_config = DatabaseConfig::connection_config();
        $config = $connection_config['config'];

        try {
            if(isset($config['username'])){
              $connection = new PDO($config['resource'], $config['username'], $config['password']);
            }else{
              $connection = new PDO($config['resource']);
            }

            $connection->exec("set names utf8");
            
        } catch (PDOException $e) {
            die('Virhe tietokantayhteydessä: ' . $e->getMessage());
        }

        return $connection;
      }

      public static function test_connection(){
        require 'vendor/ConnectionTest/connectiontest.php';

        exit();
      }

  }
