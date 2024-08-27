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
  private string $pathValidateCardNumber = '/v2.0/brizzi/checknum';
  private string $pathTopupDeposit = '/v2.0/brizzi/topup';
  private string $pathCheckTopupDeposit = '/v2.0/brizzi/checktrx';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
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

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $this->pathValidateCardNumber,
      $accessToken,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathValidateCardNumber",
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

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $this->pathTopupDeposit,
      $accessToken,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathTopupDeposit",
      $headersRequest,
      $bodyRequest
    );

    return $response;
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

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $this->pathCheckTopupDeposit,
      $accessToken,
      $this->externalId,
      $timestamp,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathCheckTopupDeposit",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
