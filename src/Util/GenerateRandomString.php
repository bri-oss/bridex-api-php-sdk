<?php

namespace BRI\Util;

interface GenerateRandomStringInterface {
  public function generate(?int $length = 6): string;
}

class GenerateRandomString implements GenerateRandomStringInterface {

  public function generate(?int $length = 6): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
  }
}
