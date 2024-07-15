<?php

namespace BRI\Brizzi;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface BrizziInterface {
  public function validateCardNumber(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
  ): string;
  public function topupDeposit(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
  ): string;
  public function checkTopupStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
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
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
  ): string {
    $additionalBody = [
      'username' => 'test',
      'brizziCardNo' => '6013500601496673'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $partnerId,
      $this->pathValidateCardNumber,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathValidateCardNumber",
      $headersRequest,
      $bodyRequest
    );
    echo '<pre>'; print_r($headersRequest); echo '</pre>';

    return $response;
  }

  public function topupDeposit(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
  ): string {
    $additionalBody = [
      'username' => 'test',
      'brizziCardNo' => '6013500601496673',
      'amount' => '5123.00'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $partnerId,
      $this->pathTopupDeposit,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathTopupDeposit",
      $headersRequest,
      $bodyRequest
    );
    echo '<pre>'; print_r($headersRequest); echo '</pre>';

    return $response;
  }

  public function checkTopupStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
  ): string {
    $additionalBody = [
      'username' => 'test',
      'brizziCardNo' => '6013500601496673',
      'amount' => '10',
      'reff' => '1356040'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Brizzi(
      $clientSecret,
      $partnerId,
      $this->pathCheckTopupDeposit,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathCheckTopupDeposit",
      $headersRequest,
      $bodyRequest
    );
    echo '<pre>'; print_r($headersRequest); echo '</pre>';

    return $response;
  }
}
