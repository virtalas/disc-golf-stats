<?php

  class DatabaseConfig{

    // PostgreSQL (psql)
    private static $use_database = 'psql';

    // Add config here after pgsql:
    private static $connection_config = array(
      'psql' => array(
        'resource' => 'pgsql:'
      )
    );

    public static function connection_config(){
      $config = array(
        'db' => self::$use_database,
        'config' => self::$connection_config[self::$use_database]
      );

      return $config;
    }

  }
