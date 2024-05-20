<?php

namespace BRI\Util;

use Exception;

final class VarNumber
{
  // generate random number
  public function generateVar(int $randomLength): int
  {
    if ($randomLength < 1) {
      throw new Exception('Numbers length must be greater than 0');
    }
    $min = pow(10, $randomLength - 1);
    $max = pow(10, $randomLength) - 1;
    $var = random_int($min, $max);
    return $var;
  }
}
