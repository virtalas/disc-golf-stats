<?php
  class Cache {

    // Turn caching on/off (true/false) for development
    public static function on() {
      // return true; // on
      return false; // off
    }

    public static function getPage($stripped_url) {
      $cached_page = null;

      try {
        $cached_page = file_get_contents("cache/". $stripped_url. ".html");
      } catch (Exception $e) {
      }

      return $cached_page;
    }

    public static function store($stripped_url, $content) {
      file_put_contents("cache/". $stripped_url. ".html", $content);
    }

    public static function clear() {
      array_map('unlink', glob("cache/*.html"));
    }

    public static function cached_files() {
      return array_diff(scandir("cache"), array('..', '.', '.gitignore', '.DS_Store'));;
    }

    public static function strip_tags_content($htmlString, $idToRemove) {
      $dom = new DOMDocument;
      libxml_use_internal_errors(true);
      $dom->loadHTML($htmlString);
      libxml_clear_errors();
      $xPath = new DOMXPath($dom);
      $nodes = $xPath->query('//*[@id="'. $idToRemove. '"]');

      if ($nodes->item(0)) {
        $nodes->item(0)->parentNode->removeChild($nodes->item(0));
      }

      return $dom->saveHTML();
    }
  }
