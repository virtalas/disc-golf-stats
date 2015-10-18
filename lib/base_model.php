<?php

  class BaseModel{
    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null){
      // Käydään assosiaatiolistan avaimet läpi
      foreach($attributes as $attribute => $value){
        // Jos avaimen niminen attribuutti on olemassa...
        if(property_exists($this, $attribute)){
          // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
          $this->{$attribute} = $value;
        }
      }
    }

    public function errors(){
      // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
      $errors = array();

      foreach($this->validators as $validator){
        // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
        $errors = array_merge($errors, $this->{$validator}());
      }

      return $errors;
    }

    public function validate_string_not_empty($string, $type) {
      $errors = array();

      if ($string == '' || $string == null) {
        $errors[] = $type. ' ei saa olla tyhjä.';
      }

      return $errors;
    }

    public function validate_integer($integer, $type) {
      $errors = array();

      if (!is_numeric($integer)) {
        $errors[] = $type. ' pitää olla numero.';
      } else if ($integer <= 0) {
        $errors[] = $type. ' pitää olla suurempi kuin 0.';
      }

      return $errors;
    }
  }
