<?php
namespace BRI\TransferCredit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface IntrabankTransferInterface {
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $beneficiaryAccountNo,
    ?string $deviceId,
    ?string $channel
  ): string;
  public function transfer(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerReferenceNo,
    string $value,
    string $beneficiaryAccountNo,
    string $sourceAccountNo,
    string $feeType,
    string $remark,
    string $transactionDate,
    ?string $currency = 'IDR',
    ?string $customerReference,
    ?string $deviceId,
    ?string $channel
  ): string;
}

class IntrabankTransfer implements IntrabankTransferInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInquiry = '/intrabank/snap/v1.0/account-inquiry-internal';
  private string $pathTransfer = '/intrabank/snap/v1.0/transfer-intrabank';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  /**
   * 
   * @param string $beneficiaryAccountNo
   */
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $beneficiaryAccountNo,
    ?string $deviceId,
    ?string $channel
  ): string {
    $additionalBody = [
      'beneficiaryAccountNo' => $beneficiaryAccountNo,
      'additionalInfo' => (object) [
        'deviceId' => $deviceId,
        'channel' => $channel
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->TransferCredit(
      $clientSecret,
      $partnerId,
      $this->pathInquiry,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      'POST'
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInquiry",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }

  public function transfer(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerReferenceNo,
    string $value,
    string $beneficiaryAccountNo,
    string $sourceAccountNo,
    string $feeType,
    string $remark,
    string $transactionDate,
    ?string $currency = 'IDR',
    ?string $customerReference,
    ?string $deviceId,
    ?string $channel
  ): string {
    $additionalBody = [
      'partnerReferenceNo' => $partnerReferenceNo,
      'amount' => (object) [
        'value' => $value,
        'currency' => $currency
      ],
      'beneficiaryAccountNo' => $beneficiaryAccountNo,
      'customerReference' => $customerReference, // optional
      'feeType' => $feeType,
      'remark' => $remark,
      'sourceAccountNo' => $sourceAccountNo,
      'transactionDate' => $transactionDate,
      'additionalInfo' => (object) [ // optional
        'deviceId' => $deviceId,
        'channel' => $channel
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->TransferCredit(
      $clientSecret,
      $partnerId,
      $this->pathTransfer,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      'POST'
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathTransfer",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }
}
