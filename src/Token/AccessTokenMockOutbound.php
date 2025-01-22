<?php
namespace BRI\Token;

use BRI\Signature\Signature;
use InvalidArgumentException;

interface AccessTokenMockOutboundInterface {
  public function getAccessToken(
    string $clientId,
    string $timestamp,
    string $baseUrl,
    string $accessTokenPath,
    string $privateKey
  ): string;
}

class AccessTokenMockOutbound implements AccessTokenMockOutboundInterface {
  public function getAccessToken(
    string $clientId,
    string $timestamp,
    string $baseUrl,
    string $accessTokenPath,
    string $privateKey
  ): string {
    try {
      // Validate inputs
      if (empty($clientId) || empty($timestamp) || empty($baseUrl) || empty($accessTokenPath) || empty($privateKey)) {
        throw new InvalidArgumentException('All parameters are required.');
      }

      if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid base URL.');
      }

      // Generate signature
      $stringToSign = "$clientId|$timestamp";

      if (!openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
        throw new \RuntimeException('Failed to generate signature.');
      }

      $hexSignature = bin2hex($signature); // Convert to hexadecimal format

      // Headers
      $requestHeadersToken = [
        "Content-Type: application/json",
        "X-TIMESTAMP: $timestamp",
        "X-CLIENT-KEY: $clientId",
        "X-SIGNATURE: $hexSignature"
      ];

      // Request body
      $dataToken = ['grantType' => "client_credentials"];
      $bodyToken = json_encode($dataToken);

      // cURL request
      $chPost = curl_init();
      curl_setopt_array($chPost, [
        CURLOPT_URL => rtrim($baseUrl, '/') . '/' . ltrim($accessTokenPath, '/'),
        CURLOPT_HTTPHEADER => $requestHeadersToken,
        CURLOPT_POSTFIELDS => $bodyToken,
        CURLOPT_RETURNTRANSFER => true,
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
      if ($httpCode !== 200 || !isset($jsonPost['accessToken'])) {
        throw new \RuntimeException('Invalid response received: ' . $response);
      }

      return $jsonPost['accessToken'];
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
  }
}
