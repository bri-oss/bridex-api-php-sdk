<?php

namespace BRI\Brizzi;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

interface BrizziInterface {
  public function validateCardNumber(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string;
  public function topupDeposit(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string;
  public function checkTopupStatus(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string;
}

class Brizzi implements BrizziInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;

  public function __construct(
    ExecuteCurlRequest $executeCurlRequest,
    PrepareRequest $prepareRequest
  ) {
    $this->executeCurlRequest = $executeCurlRequest;
    $this->prepareRequest = $prepareRequest;
    $this->externalId = (new VarNumber())->generateVar(9);
  }

  private function getPath(string $type): string {
    $paths = [
      'validateCardNumber' => '/v2.0/brizzi/mock/checknum',
      'topupDeposit' => '/v2.0/brizzi/topup',
      'checkTopupDeposit' => '/v2.0/brizzi/mock/checktrx'
    ];

    if (!array_key_exists($type, $paths)) {
      throw new InvalidArgumentException("Invalid request type: $type");
    }

    return $paths[$type];
  }

  private function makeRequest(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $type,
    array $requiredFields
  ): string {
    foreach ($requiredFields as $field) {
      if (!isset($body[$field])) {
        throw new InvalidArgumentException(
          sprintf('The field "%s" is required.', $field)
        );
      }
    }

    $path = $this->getPath($type);

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $path,
      $accessToken,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    return $this->executeCurlRequest->execute(
      "$baseUrl$path",
      $headersRequest,
      $bodyRequest
    );
  }

  public function validateCardNumber(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string {
    return $this->makeRequest(
      $clientSecret,
      $baseUrl,
      $accessToken,
      $timestamp,
      $body,
      'validateCardNumber',
      ['username', 'brizziCardNo']
    );
  }

  public function topupDeposit(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string {
    return $this->makeRequest(
      $clientSecret,
      $baseUrl,
      $accessToken,
      $timestamp,
      $body,
      'topupDeposit',
      ['username', 'brizziCardNo', 'amount']
    );
  }

  public function checkTopupStatus(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string {
    return $this->makeRequest(
      $clientSecret,
      $baseUrl,
      $accessToken,
      $timestamp,
      $body,
      'checkTopupDeposit',
      ['username', 'brizziCardNo', 'amount', 'reff']
    );
  }
}
