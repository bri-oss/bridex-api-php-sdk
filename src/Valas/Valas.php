<?php

namespace BRI\Valas;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use CURLFile;
use InvalidArgumentException;

interface ValasInterface {
  public function infoKursCounter(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string;
  public function valasNegoInfo(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string;
  public function checkDealCode(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    string $dealCode,
    string $partnerCode
  ): string;
  public function transactionValas(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string;
  public function transactionValasNonNego(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string;
  public function inquiryTransaction(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string;
  public function inquiryLimit(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    string $debitAccount,
    string $partnerCode
  ): string;
  public function uploadUnderlying(
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    string $partnerCode,
    array $body = null
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

  public function infoKursCounter(string $clientSecret, string $baseUrl, string $accessToken, string $timestamp, array $body, string $partnerCode): string
  {
    if (!isset($body['dealtCurrency']) || !isset($body['counterCurrency'])) {
      throw new InvalidArgumentException('Both dealtCurrency and counterCurrency are required.');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInfoKursCounter,
      $accessToken,
      $timestamp,
      $partnerCode,
      json_encode($body, true)
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string {
    if (!isset($body['dealtCurrency']) || !isset($body['counterCurrency'])) {
      throw new InvalidArgumentException('Both dealtCurrency and counterCurrency are required.');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathValasNegoInfo,
      $accessToken,
      $timestamp,
      $partnerCode,
      json_encode($body, true)
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    string $dealCode,
    string $partnerCode
  ): string {
    $additionalBody = [
      'dealCode' => $dealCode,
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathCheckDealCode,
      $accessToken,
      $timestamp,
      $partnerCode,
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string {
    if (
      !isset($body['debitAccount']) ||
      !isset($body['creditAccount']) ||
      !isset($body['dealCode']) ||
      !isset($body['remark'])) {
      throw new InvalidArgumentException('Both debitAccount, creditAccount, dealCode, remark, partnerReferenceNo, underlyingReference are required.');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathTransactionValas,
      $accessToken,
      $timestamp,
      $partnerCode,
      json_encode($body, true)
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string {
    if (
      !isset($body['debitAccount']) ||
      !isset($body['creditAccount']) ||
      !isset($body['debitCurrency']) ||
      !isset($body['debitAmount']) ||
      !isset($body['remark']) ||
      !isset($body['partnerReferenceNo'])) {
      throw new InvalidArgumentException('Both debitAccount, creditAccount, debitCurrency, debitAmount, remark, and partnerReferenceNo are required.');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathTransactionNonNego,
      $accessToken,
      $timestamp,
      $partnerCode,
      json_encode($body, true)
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    array $body,
    string $partnerCode
  ): string {
    if (
      !isset($body['originalPartnerReferenceNo']) ||
      !isset($body['originalReferenceNo'])) {
      throw new InvalidArgumentException('Both originalPartnerReferenceNo and originalReferenceNo are required.');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInquiryTransaction,
      $accessToken,
      $timestamp,
      $partnerCode,
      json_encode($body, true)
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    string $debitAccount,
    string $partnerCode
  ): string {
    $additionalBody = [
      'debitAccount' => $debitAccount
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathInquiryLimit,
      $accessToken,
      $timestamp,
      $partnerCode,
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
    string $baseUrl,
    string $accessToken,
    string $timestamp,
    string $partnerCode,
    array $body = null
  ): string {
    if (
      !isset($body['fileData']) || !isset($body['fileName'])) {
      throw new InvalidArgumentException('fileData and fileName is required.');
    }

    list($bodyRequest, $headersRequest) = $this->prepareRequest->Valas(
      $clientSecret,
      $this->pathUploadUnderlying,
      $accessToken,
      $timestamp,
      $partnerCode,
      json_encode($body, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathUploadUnderlying",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }
}
