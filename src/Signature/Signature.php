<?php

namespace BRI\Signature;

final class Signature
{
   // generate signature Token
   public function generateToken(string $pKeyId, string $clientId, string $timestamp): string
   {
      $stringToSign = "{$clientId}|{$timestamp}";
      openssl_sign($stringToSign, $signature, $pKeyId, OPENSSL_ALGO_SHA256);
      return base64_encode($signature);
   }

   // generate signature request
   public function generateRequest(string $clientSecret, string $method, string $timestamp, string $accessToken, string $bodyRequest, string $path): string
   {
      $sha256 = hash("sha256", $bodyRequest);
      $stringToSign = "{$method}:{$path}:{$accessToken}:{$sha256}:{$timestamp}";
      return hash_hmac("sha512", $stringToSign, $clientSecret);
   }
}
