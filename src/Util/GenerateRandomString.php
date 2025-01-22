<?php

namespace BRI\Util;

interface GenerateRandomStringInterface {
  public function generate(?int $length = 6): string;
}

class GenerateRandomString implements GenerateRandomStringInterface {

  public function generate(?int $length = 6): string {
    return bin2hex(random_bytes($length / 2));
  }
}
