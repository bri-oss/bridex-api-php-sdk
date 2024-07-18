<?php

namespace BRI\Valas;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface ValasInterface {
  public function infoKursCounter(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function valasNegoInfo(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function checkDealCode(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function transactionValas(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function transactionValasNonNego(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function inquiryTransaction(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function inquiryLimit(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function uploadUnderlying(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
}

class Valas implements ValasInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInfoKursCounter = '/v2.0/valas-info/kurs-counter';
  private string $pathValasNegoInfo = '/v2.0/valas-info/kurs-nego';
  private string $pathCheckDealCode = '/v2.0/valas-transaction/nego/dealcode';
  private string $pathTransactionValas = '/v2.0/valas-transaction/nego';
  private string $pathTransactionNonNego = '/v2.0/valas-transaction/counter';
  private string $pathInquiryTransaction = '/v2.0/valas-transaction/inquiry';
  private string $pathInquiryLimit = '/v2.0/valas-transaction/inquiry-limit';
  private string $pathUploadUnderlying = '/v2.0/valas-transaction/upload-underlying';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function infoKursCounter(string $clientSecret, string $partnerId, string $baseUrl, string $accessToken, string $channelId, string $timestamp): string
  {
    $additionalBody = [
      'dealtCurrency' => 'USD',
      'counterCurrency' => 'IDR',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInfoKursCounter,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInfoKursCounter",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function valasNegoInfo(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'dealtCurrency' => 'USD',
      'counterCurrency' => 'IDR',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathValasNegoInfo,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathValasNegoInfo",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function checkDealCode(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'dealCode' => 'O0003540',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathCheckDealCode,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathCheckDealCode",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function transactionValas(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'debitAccount' => '030702000141509',
      'creditAccount' => '034401083104504',
      'dealCode' => 'O0003540',
      'remark' => '374628374',
      'partnerReferenceNo' => '6278163827789',
      'underlyingReference' => ""
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathTransactionValas,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathTransactionValas",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function transactionValasNonNego(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'debitAccount' => '030702000141509',
      'creditAccount' => '034401083104504',
      'debitCurrency' => 'USD',
      'creditCurrency' => 'IDR',
      'debitAmount' => '3.00',
      'remark' => '374628374',
      'partnerReferenceNo' => '6278163827120'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathTransactionNonNego,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathTransactionNonNego",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function inquiryTransaction(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'partnerReferenceNo' => '6278163827120',
      'originalReferenceNo' => '8757771'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInquiryTransaction,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInquiryTransaction",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function inquiryLimit(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'debitAccount' => '020602000008513'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInquiryLimit,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInquiryLimit",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function uploadUnderlying(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'fileData' => "{Inputan Base64}",
      'fileName' => 'fileNameTest'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathUploadUnderlying,
      $accessToken,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathUploadUnderlying",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
