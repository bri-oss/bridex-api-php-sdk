<?php
namespace BRI\VirtualAccount;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\VarNumber;
use BRI\Util\PrepareRequest;

interface BrivaOnlineInterface {
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    string $customerNo,
    string $inquiryRequestId,
    ?string $value,
    ?string $currency,
    ?string $trxDateInit,
    ?int $channelCode,
    ?string $sourceBankCode,
    ?string $passApp,
    ?string $idApp
  ): string;

  public function payment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    string $customerNo,
    string $inquiryRequestId,
    ?string $value,
    ?string $currency,
    ?string $trxDateInit,
    ?int $channelCode,
    ?string $sourceBankCode,
    ?string $passApp,
    ?string $idApp
  ): string;
}

class BrivaOnline implements BrivaOnlineInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInquiry = '/v2.0/transfer-va/inquiry';
  private string $pathPayment = '/v2.0/transfer-va/payment';

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
    string $partnerServiceId,
    string $customerNo,
    string $inquiryRequestId,
    ?string $value,
    ?string $currency,
    ?string $trxDateInit,
    ?int $channelCode,
    ?string $sourceBankCode,
    ?string $passApp,
    ?string $idApp
  ): string {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'amount' => (object) [
        'value' => $value,
        'currency' => $currency
      ], // optional
      'trxDateInit' => $trxDateInit, //'optional',
      'channelCode' => $channelCode, //'optional',
      'sourceBankCode' => $sourceBankCode,//optional
      'passApp' => $passApp, //optional
      'inquiryRequestId' => $inquiryRequestId,
      'additionalInfo' => (object) [
        'idApp' => $idApp //optional
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaOnline(
      $clientSecret,
      $partnerId,
      $this->pathInquiry,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInquiry",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }

  public function payment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    string $customerNo,
    string $inquiryRequestId,
    ?string $value,
    ?string $currency,
    ?string $trxDateInit,
    ?int $channelCode,
    ?string $sourceBankCode,
    ?string $passApp,
    ?string $idApp
  ): string {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'amount' => (object) [
        'value' => $value,
        'currency' => $currency
      ], // optional
      'trxDateInit' => $trxDateInit, //'optional',
      'channelCode' => $channelCode, //'optional',
      'sourceBankCode' => $sourceBankCode,//optional
      'passApp' => $passApp, //optional
      'inquiryRequestId' => $inquiryRequestId,
      'additionalInfo' => (object) [
        'idApp' => $idApp //optional
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaOnline(
      $clientSecret,
      $partnerId,
      $this->pathPayment,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathPayment",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }
}
