<?php

namespace BRI\Util;

use Exception;

final class RandomNumber
{
   // generate random number
   public function GenerateRandomNumber(int $randomLength): int
   {
      if ($randomLength < 1) {
         throw new Exception('Numbers length must be greater than 0');
      }
      $min = pow(10, $randomLength - 1);
      $max = pow(10, $randomLength) - 1;
      return rand($min, $max);
   }
}
