<?php

namespace BRI\CardlessCashWithdrawal;

use BRI\Util\ExecuteCurlRequest;

interface AuthTokenInterface {
  public function authToken(
    string $baseUrl,
    string $providerId,
    string $secretKey
  ): string;
}

class AuthToken implements AuthTokenInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private string $path = '/v1/cardless/token';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
  }

  public function authToken(
    string $baseUrl,
    string $providerId,
    string $secretKey
  ): string {
    $body = [
      "providerId" => 'client_credentials',
      "secretKey" => 'PXXQKV0HYVCJAsriOPQ6WSwK4S2lLi5Z'
    ];

    $bodyRequest = json_encode($body, true);

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->path",
      [''],
      $bodyRequest,
      'POST'
    );

    return $response;
  }
}
