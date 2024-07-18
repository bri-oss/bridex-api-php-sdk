<?php

namespace BRI\Token;

interface AccessTokenBRIAPIInterface {
  public function getAccessToken(
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessTokenPath
  ): string;
}

class AccessTokenBRIAPI implements AccessTokenBRIAPIInterface {
  public function getAccessToken(string $clientId, string $clientSecret, string $baseUrl, string $accessTokenPath): string
  {
    // body request
    $bodyReq = [
      "client_id" => $clientId,
      "client_secret" => $clientSecret
    ];
    $bodyToken = http_build_query($bodyReq);

    if ($bodyToken === false) {
      echo 'Error in JSON encoding: ' . json_last_error_msg();
      return '';
    }
    // Headers
    $requestHeadersToken = array(
      "Content-Type:application/x-www-form-urlencoded",
    );

    // fetch access token
    $chPost = curl_init();
    curl_setopt_array($chPost, [
      CURLOPT_URL => 'https://sandbox.partner.api.bri.co.id/oauth/client_credential/accesstoken?grant_type=client_credentials',
      CURLOPT_HTTPHEADER => $requestHeadersToken,
      CURLOPT_POSTFIELDS => $bodyToken,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_POST => true,
      CURLOPT_CUSTOMREQUEST => "POST",
    ]);

    $response = curl_exec($chPost);
    curl_close($chPost);

    $jsonPost = json_decode($response, true);

    return $jsonPost['access_token'];
  }
}
