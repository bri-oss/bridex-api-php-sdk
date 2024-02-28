<?php

function isTokenExpired($timestampFile, $minutes)
{
  $currentTime = time();
  $storedTimestamp = strtotime(trim(file_get_contents($timestampFile)));

  // Check minutes passed since the stored timestamp
  $minutesPassed = ($currentTime - $storedTimestamp) / 60;

  return $minutesPassed >= $minutes;
}
