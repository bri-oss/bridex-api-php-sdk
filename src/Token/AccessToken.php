<?php

namespace BRI\Token;

use BRI\Signature\Signature;
use InvalidArgumentException;

final class AccessToken {
  private Signature $signature;

  public function __construct(Signature $signature)
  {
    $this->signature = $signature;
  }

  /**
   * Generate and fetch an access token
   *
   * @param string $clientId
   * @param string $pKeyId
   * @param string $timestamp
   * @param string $baseUrl
   * @param string $accessTokenPath
   * @return string
   * @throws InvalidArgumentException|\RuntimeException
   */
  public function
  getAccessToken(
    string $clientId,
    string $pKeyId,
    string $timestamp,
    string $baseUrl,
    string $accessTokenPath
  ): string {
    try {
      // Validate inputs
      if (empty($clientId) || empty($pKeyId) || empty($timestamp) || empty($baseUrl) || empty($accessTokenPath)) {
        throw new InvalidArgumentException('All parameters are required.');
      }

      if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid base URL.');
      }

      // Generate signature token
      $signatureToken = $this->signature->generateToken($pKeyId, $clientId, $timestamp);

      // Prepare request body and headers
      $dataToken = ['grantType' => "client_credentials"];
      $bodyToken = json_encode($dataToken);

      $requestHeadersToken = [
        "X-TIMESTAMP: $timestamp",
        "X-CLIENT-KEY: $clientId",
        "X-SIGNATURE: $signatureToken",
        "Content-Type: application/json",
      ];

      // Execute cURL request
      $ch = curl_init();
      curl_setopt_array($ch, [
        CURLOPT_URL => rtrim($baseUrl, '/') . '/' . ltrim($accessTokenPath, '/'),
        CURLOPT_HTTPHEADER => $requestHeadersToken,
        CURLOPT_POSTFIELDS => $bodyToken,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
      ]);

      $response = curl_exec($ch);
      $curlError = curl_error($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);

      // Check for cURL errors
      if ($response === false || $curlError) {
        throw new \RuntimeException('cURL error: ' . $curlError);
      }

      // Decode response
      $jsonPost = json_decode($response, true);

      // Validate response format
      if ($httpCode !== 200 || !isset($jsonPost['accessToken'])) {
        throw new \RuntimeException('Invalid response: ' . $response);
      }

      return $jsonPost['accessToken'];

    } catch (InvalidArgumentException $e) {
      // Handle input validation errors
      throw new \RuntimeException("Input validation error: " . $e->getMessage(), 0, $e);

    } catch (\Exception $e) {
      // Handle other exceptions
      throw new \RuntimeException("Failed to fetch access token: " . $e->getMessage(), 0, $e);
    }
  }
}
