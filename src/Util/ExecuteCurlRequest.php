<?php

namespace BRI\Util;

interface ExecuteCurlRequestInterface {
  public function execute(): void;
}

class ExecuteCurlRequest {
  public function execute(string $url, array $headers, string $body, ?string $method = 'POST')
  {
    $ch = curl_init();
    curl_setopt_array($ch, [
      CURLOPT_URL => $url,
      CURLOPT_HTTPHEADER => $headers,
      CURLOPT_POSTFIELDS => $body,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => $method,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    return $response;
  }
}