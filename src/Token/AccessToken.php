<?php

namespace BRI\Token;

use BRI\Signature\Signature;

final class AccessToken
{
  private Signature $signature;

  public function __construct(Signature $signature)
  {
    $this->signature = $signature;
  }

  // generate signature Token
  public function
  getAccessToken(
    string $clientId,
    string $pKeyId,
    string $timestamp,
    string $baseUrl,
    string $accessTokenPath
  ): string {
    // get signature token
    $signatureToken = $this->signature->generateToken($pKeyId, $clientId, $timestamp);

    // body request
    $dataToken = ['grantType' => "client_credentials"];
    $bodyToken = json_encode($dataToken, true);

    // Headers
    $requestHeadersToken = array(
      "X-TIMESTAMP:" . $timestamp,
      "X-CLIENT-KEY:" . $clientId,
      "X-SIGNATURE:" . $signatureToken,
      "Content-Type:application/json",
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

    echo $response;

    $jsonPost = json_decode($response, true);
    return $jsonPost['accessToken'];
  }
}
