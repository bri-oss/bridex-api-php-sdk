<?php

namespace BRI\TransferCredit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

class TransactionStatusInquiry {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $path = '/snap/v1.0/transfer/status';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $originalPartnerReferenceNo,
    string $serviceCode,
    string $transactionDate,
    ?string $deviceId,
    ?string $channel
  ){
    $additionalBody = [
      'originalPartnerReferenceNo' => $originalPartnerReferenceNo,
      'serviceCode' => $serviceCode,
      'transactionDate' => $transactionDate,
      'additionalInfo' => (object) [
        'deviceId' => $deviceId,
        'channel' => $channel
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->TransferCredit(
      $clientSecret,
      $partnerId,
      $this->path,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      'POST'
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->path",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }

}
