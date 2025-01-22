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
      'validateCardNumber' => '/v2.0/brizzi/checknum',
      'topupDeposit' => '/v2.0/brizzi/topup',
      'checkTopupDeposit' => '/v2.0/brizzi/checktrx'
    ];

    if (!array_key_exists($type, $paths)) {
      throw new InvalidArgumentException("Invalid request type: $type");
    }

    return $paths[$type];
  }

  public function validateCardNumber(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string {
    if (!isset($body['username']) || !isset($body['brizziCardNo'])) {
      throw new InvalidArgumentException('Both username and brizziCardNo are required.');
    }

    $path = $this->getPath('validateCardNumber');

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $path,
      $accessToken,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$path",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function topupDeposit(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string {
    if (!isset($body['username']) || !isset($body['brizziCardNo']) || !isset($body['amount'])) {
      throw new InvalidArgumentException('Both username, amount, and brizziCardNo are required.');
    }

    try {
      $path = $this->getPath('topupDeposit');

      list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
        $clientSecret,
        $path,
        $accessToken,
        $this->externalId,
        $timestamp,
        json_encode($body, true)
      );

      $response = $this->executeCurlRequest->execute(
        "$baseUrl$path",
        $headersRequest,
        $bodyRequest
      );

      return $response;
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
  }

  public function checkTopupStatus(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body
  ): string {
    if (!isset($body['username']) || !isset($body['brizziCardNo']) || !isset($body['amount']) || !isset($body['reff'])) {
      throw new InvalidArgumentException('Both username, amount, reff and brizziCardNo are required.');
    }

    try {
      $path = $this->getPath('checkTopupStatus');

      list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
        $clientSecret,
        $path,
        $accessToken,
        $this->externalId,
        $timestamp,
        json_encode($body, true)
      );

      $response = $this->executeCurlRequest->execute(
        "$baseUrl$path",
        $headersRequest,
        $bodyRequest
      );

      return $response;
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
  }
}
