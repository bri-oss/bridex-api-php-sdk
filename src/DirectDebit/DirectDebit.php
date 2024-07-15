<?php

namespace BRI\DirectDebit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface DirectDebitInterface {
  public function payment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function paymentStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function paymentNotify(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function refundPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
  public function refundNotify(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string;
}

class DirectDebit implements DirectDebitInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathPayment = '/snap/v2.0/debit/payment-host-to-host';
  private string $pathPaymentStatus = '/snap/v2.0/debit/status';
  private string $pathPaymentNotify = '/snap/v2.0/debit/notify';
  private string $pathrefundPayment = '/snap/v2.0/debit/refund';
  private string $pathrefundNotify = '/snap/v2.0/debit/notify/refund';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function payment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    // string $partnerReferenceNo,
    // array $urlParam
  ): string {
    $additionalBody = [
      'partnerReferenceNo' => '426306015176',
      'urlParam' => [
        (object) [
          'url' => "https://5fdc5f1948321c00170119e0.mockapi.io/api/v1/simulation/simulation",
          'type' => 'PAY_NOTIFY',
          'isDeepLink' => 'N'
        ]
      ],
      'amount' => (object) [
        'value' => '10000.00',
        'currency' => 'IDR',
      ],
      'chargeToken' => 'null',
      'bankCardToken' => "card_.eyJpYXQiOjE3MDgwNTAzNTYsImlzcyI6IkJhbmsgQlJJIC0gRENFIiwianRpIjoiNmY2MmE4ZjUtMGUwMS00NjFjLWJlZmQtYjk3ZWE5YjNmMmIwIiwicGFydG5lcklkIjoi77-9Iiwic2VydmljZU5hbWUiOiJERF9FWFRFUk5BTF9TRVJWSUNFIn0.HR4P9PecyfCZLJ-ibeuxuuWtHzWHrzgunjxiEQJZEjZHO2fQqrMgaO8IUnmACtNJilGOpIQAc7Jsa5W_tCF4KmIpC5jB-tDw40tpqImZ9Famt_hzgacrDcByw2jT9UAPMH444kGAQa7z44PV6jcHdQoaIAfiOkChHw-b11Vg4LyETbsEExvOcL2hKomG_JXpDq5bYmuHcJ2SJ8lRnGomi-7oz_dyM0_wUe1fmE6UyLnvEFz6o6q8nXtm_3g29cLP_4uw5BT54DuSXrRdmw4J7PK3zl2qUnM7CBpYVRLr74iCx9SLGYIMMROE7aGe_DkNfK-dnLKgcvIaN0q-rnLbhg",
      'additionalInfo' => (object) [
        'otpStatus' => 'NO',
        'settlementAccount' => '020601000109305',
        'merchantTrxId' => '',
        'remarks' => 'test'
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathPayment,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    echo '<pre>'; print_r($headersRequest); echo '</pre>';

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathPayment",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function paymentStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'originalPartnerReferenceNo' => '815027979003',
      'originalReferenceNo' => '574929794216',
      'serviceCode' => '54'
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathPaymentStatus,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathPaymentStatus",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function paymentNotify(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'originalPartnerReferenceNo' => '815027979003',
      'originalReferenceNo' => '574929794216',
      'amount' => (object) [
        'value' => "10000.00",
        "currency" => "IDR"
      ],
      'latestTransactionStatus' => '00',
      'transactionStatusDesc' => 'success',
      'additionalInfo' => (object) [
        'merchantTrxid' => '30220107504',
        'remarks' => ''
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathPaymentNotify,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathPaymentNotify",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function refundPayment(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'originalPartnerReferenceNo' => '815027979003',
      'originalReferenceNo' => '574929794216',
      'partnerRefundNo' => '341406425579',
      'refundAmount' => (object) [
        'value' => "10000.00",
        "currency" => "IDR"
      ],
      "reason" => "testing coba",
      'additionalInfo' => (object) [
        'callbackUrl' => "https://5fdc5f1948321c00170119e0.mockapi.io/api/v1/simulation/simulation",
        'settlementAccount' => '020601000109305'
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathrefundPayment,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathrefundPayment",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function refundNotify(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp
  ): string {
    $additionalBody = [
      'originalPartnerReferenceNo' => '815027979003',
      'originalReferenceNo' => '574929794216',
      'amount' => (object) [
        'value' => "10000.00",
        "currency" => "IDR"
      ],
      "latestTransactionStatus" => "00",
      'transactionStatusDescription' => 'success',
      'additionalInfo' => (object) [
        'refundId' => '528786398613'
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->DirectDebit(
      $clientSecret,
      $partnerId,
      $this->pathrefundNotify,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathrefundNotify",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

}
