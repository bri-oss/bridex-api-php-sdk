<?php

namespace BRI\QrisMPMDynamicNotification;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;

interface NotifyPaymentInterface
{
  public function notifyPayment(
    string $baseUrl,
    string $clientId,
    string $secretKey,
    string $externalId,
    string $ipAddress,
    string $deviceId,
    string $latitude,
    string $longitude,
    string $channelId,
    string $origin,
  ): string;
}

class NotifyPayment implements NotifyPaymentInterface
{
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $path = '/v1.1/qr-dynamic/qr-mpm-notify';

  public function __construct()
  {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->prepareRequest = new PrepareRequest();
  }

  public function notifyPayment(
    string $baseUrl,
    string $clientId,
    string $secretKey,
    string $externalId,
    string $ipAddress,
    string $deviceId,
    string $latitude,
    string $longitude,
    string $channelId,
    string $origin,
  ): string {
    $additionalBody = [
      "originalReferenceNo" => "2020102977770000000009",
      "originalPartnerReferenceNo" => "2020102900000000000001",
      "latestTransactionStatus" => "00",
      "transactionStatusDesc" => "success",
      "customerNumber" => "6281388370001",
      "accountType" => "tabungan",
      "destinationAccountName" => "John Doe",
      "amount" => (object) [
        "value" => "12345678.00",
        "currency" => "IDR"
      ],
      "sessionID" => "0UYEB77329002HY",
      "bankCode" => "002",
      "externalStoreID" => "124928924949487",
      "additionalInfo" => (object) [
        "reffId" => "1001016773",
        "issuerName" => "GOPAY",
        "issuerRrn" => "110002756582"
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->QrisMPMDynamicNotification(
      $clientId,
      $secretKey,
      $externalId,
      $ipAddress,
      $deviceId,
      $latitude,
      $longitude,
      $channelId,
      $origin,
      json_encode($additionalBody, true)
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
