<?php
  class Cache {

    public static function getPage($stripped_url) {
      $cached_page = null;

      try {
        $cached_page = file_get_contents("cache/". $stripped_url. ".html");
      } catch (Exception $e) {
      }

      return $cached_page;
    }

    public static function store($stripped_url, $content) {
      $dir = 'cached_pages';

      // create new directory with 744 permissions if it does not exist yet
      // owner will be the user/group the PHP script is run under
      // if ( !file_exists($dir) ) {
      //   mkdir ($dir, 0744);
      // }
      //
      // file_put_contents("cache/$dir/". $stripped_url. ".html", $content);

      file_put_contents("cache/". $stripped_url. ".html", $content);

      // $myfile = fopen("cache/". $stripped_url. ".html", "w");
      // fwrite($myfile, $content);
    }

    public static function clear() {
      array_map('unlink', glob("cache/*.html"));
    }
  }
