<?php

class DatabaseConfig {

    public static function connection_config(){
        $config = array(
            'db' => 'psql',
            'config' => array('resource' => 'pgsql:host=localhost;port=0000;dbname=testdb')
        );
        return $config;
    }
}
