<?php

namespace BRI\Util;

use DateTime;
use DateTimeZone;

interface GenerateDateInterface {
  public function generate(
    ?string $modify,
    ?string $format,
    ?int $hour,
    ?int $minute,
    ?int $second = 0,
    ?int $microsecond = 0
  ): string;
}

class GenerateDate implements GenerateDateInterface {
  public function generate(
    ?string $modify = null,
    ?string $format = 'Y-m-d\TH:i:sP',
    ?int $hour = null,
    ?int $minute = null,
    ?int $second = 0,
    ?int $microsecond = 0
  ): string {
    $date = new DateTime();

    $timezone = new DateTimeZone('+07:00');
    $date->setTimezone($timezone);

    if (!is_null($hour) && !is_null($minute)) {
      $date->setTime($hour, $minute, $second, $microsecond);
    }

    if (!is_null($modify)) {
      $date->modify($modify);
    }

    return $date->format($format);
  }
}
