<?php

namespace BRI\Token;

use InvalidArgumentException;

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
    try {
      // Validate inputs
      if (empty($clientId) || empty($clientSecret) || empty($baseUrl) || empty($accessTokenPath)) {
        throw new InvalidArgumentException('All parameters are required.');
      }

      if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid base URL.');
      }

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
      $curlError = curl_error($chPost);
      $httpCode = curl_getinfo($chPost, CURLINFO_HTTP_CODE);
      curl_close($chPost);

      // Handle cURL errors
      if ($response === false || $curlError) {
        throw new \RuntimeException('cURL error: ' . $curlError);
      }

      // Parse response
      $jsonPost = json_decode($response, true);

      // Validate response structure
      if ($httpCode !== 200 || !isset($jsonPost['access_token'])) {
        throw new \RuntimeException('Invalid response received: ' . $response);
      }

      return $jsonPost['access_token'];
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
  }
}
