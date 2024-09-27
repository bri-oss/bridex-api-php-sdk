<?php

namespace BRI\Token;

use BRI\Signature\Signature;

interface AccessTokenMockOutboundInterface {
  public function getAccessToken(
    string $clientId,
    string $clientSecret,
    string $timestamp,
    string $baseUrl,
    string $accessTokenPath
  ): string;
}

class AccessTokenMockOutbound implements AccessTokenMockOutboundInterface {
  private Signature $signature;

  public function __construct(Signature $signature)
  {
    $this->signature = $signature;
  }

  public function getAccessToken(
    string $clientId,
    string $clientSecret,
    string $timestamp,
    string $baseUrl,
    string $accessTokenPath
  ): string {
    $stringToSign = "{$clientId}|{$timestamp}";
    $signatureToken = hash_hmac("sha256", $stringToSign, $clientSecret);

    // body request
    $dataToken = ['grantType' => "client_credentials"];
    $bodyToken = json_encode($dataToken, true);

    // Headers
    $requestHeadersToken = array(
      "Content-Type:application/json",
      "X-TIMESTAMP:" . $timestamp,
      "X-CLIENT-KEY:" . "$clientId",
      "X-SIGNATURE:" . "$signatureToken",
    );

    // fetch access token
    $chPost = curl_init();
    curl_setopt_array($chPost, [
      CURLOPT_URL => "$baseUrl$accessTokenPath",
      CURLOPT_HTTPHEADER => $requestHeadersToken,
      CURLOPT_POSTFIELDS => $bodyToken,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
    ]);

    $response = curl_exec($chPost);
    curl_close($chPost);

    $jsonPost = json_decode($response, true);

    return $jsonPost['data']['accessToken'];
  }
}
