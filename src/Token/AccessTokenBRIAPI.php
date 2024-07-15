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
      "client_id" => "pqYYBsSc6rHwCqp6o4R8ExmBRubEpqtY",
      "client_secret" => "idbaNFh0mGSZ7xol"
    ];
    $bodyToken = json_encode($bodyReq);

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

    echo $response;
    $jsonPost = json_decode($response, true);

    return '';
  }
}
