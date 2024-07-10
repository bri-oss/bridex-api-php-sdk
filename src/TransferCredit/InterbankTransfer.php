<?php

namespace BRI\TransferCredit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface InterbankTransferInterface {
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $beneficiaryBankCode,
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
    string $beneficiaryAccountName,
    string $beneficiaryAccountNo,
    string $beneficiaryBankCode,
    string $sourceAccountNo,
    string $transactionDate,
    ?string $currency,
    ?string $beneficiaryAddress,
    ?string $beneficiaryBankName,
    ?string $beneficiaryEmail,
    ?string $customerReference,
    ?string $deviceId,
    ?string $channel
  ): string;
}

class InterbankTransfer implements InterbankTransferInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInquiry = '/interbank/snap/v1.0/account-inquiry-external';
  private string $pathTransfer = '/interbank/snap/v1.0/transfer-interbank';

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
    string $beneficiaryBankCode,
    string $beneficiaryAccountNo,
    ?string $deviceId,
    ?string $channel
  ): string {
    $additionalBody = [
      'beneficiaryBankCode' => $beneficiaryBankCode,
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
    string $beneficiaryAccountName,
    string $beneficiaryAccountNo,
    string $beneficiaryBankCode,
    string $sourceAccountNo,
    string $transactionDate,
    ?string $currency = 'IDR',
    ?string $beneficiaryAddress,
    ?string $beneficiaryBankName,
    ?string $beneficiaryEmail,
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
      'beneficiaryAccountName' => $beneficiaryAccountName,
      'beneficiaryAccountNo' => $beneficiaryAccountNo,
      'beneficiaryAddress' => $beneficiaryAddress, // optional
      'beneficiaryBankCode' => $beneficiaryBankCode,
      'beneficiaryBankName' => $beneficiaryBankName, // optional
      'beneficiaryEmail' => $beneficiaryEmail, // optional
      'customerReference' => $customerReference, // optional
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
