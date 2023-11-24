<?php

namespace BRI\Token;

use BRI\Signature\Signature;

final class AccessToken
{
  private Signature $signature;
  private const URL = 'https://sandbox.partner.api.bri.co.id/snap/v1.0/access-token/b2b';

  public function __construct(Signature $signature)
  {
    $this->signature = $signature;
  }

  // generate signature Token
  public function
  getAccessToken(string $clientId, string $pKeyId, string $timestamp): string
  {
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
      CURLOPT_URL => self::URL,
      CURLOPT_HTTPHEADER => $requestHeadersToken,
      CURLOPT_POSTFIELDS => $bodyToken,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CUSTOMREQUEST => "POST",
    ]);

    $response = curl_exec($chPost);
    curl_close($chPost);


    $jsonPost = json_decode($response, true);
    return $jsonPost['accessToken'];
  }
}
