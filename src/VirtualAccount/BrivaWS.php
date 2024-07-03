<?php

namespace BRI\VirtualAccount;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;

interface BrivaWSInterface {
  public function create(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $virtualAccountName,
    float $total,
    string $expiredDate,
    string $trxId,
    ?string $description,
    ?string $currency = 'IDR'
  );
  public function update(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $virtualAccountName,
    float $total,
    string $expiredDate,
    string $trxId,
    ?string $description,
    ?string $currency = 'IDR'
  );
  public function updateStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $trxId,
    string $paidStatus
  );
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $trxId
  );
  public function delete(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $trxId
  );
  public function getReport(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    string $startDate,
    string $startTime,
    string $endTime,
    ?string $customerCode,
    ?string $uniqueCode
  );
  public function inquiryStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $inquiryRequestId
  );
}

class BrivaWS implements BrivaWSInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathCreate = '/snap/v1.0/transfer-va/create-va';
  private string $pathUpdate = '/snap/v1.0/transfer-va/update-va';
  private string $pathUpdateStatus = '/snap/v1.0/transfer-va/update-status';
  private string $pathInquiry = '/snap/v1.0/transfer-va/inquiry-va';
  private string $pathDelete = '/snap/v1.0/transfer-va/delete-va';
  private string $pathGetReport = '/snap/v1.0/transfer-va/report';
  private string $pathInquiryStatus = '/snap/v1.0/transfer-va/status';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->externalId = (new VarNumber())->generateVar(9);
    $this->prepareRequest = new PrepareRequest();
  }

  public function create(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $virtualAccountName,
    float $total,
    string $expiredDate,
    string $trxId,
    ?string $description,
    ?string $currency = 'IDR'
  ) {
    // $custNo = (new VarNumber())->generateVar(10);
    // $partnerServiceId = '   55888';
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'virtualAccountName' => $virtualAccountName,
      'totalAmount' => (object) [
        'value' => (string) $total,
        'currency' => $currency
      ],
      'expiredDate' => $expiredDate, // '2024-09-21T14:32:00+07:00',
      'trxId' => $trxId, //'fedca321',
      'additionalInfo' => (object) [
        'description' => $description
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
      $clientSecret,
      $partnerId,
      $this->pathCreate,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathCreate",
      $headersRequest,
      $bodyRequest
    );

    return $response;
  }

  public function update(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $virtualAccountName,
    float $total,
    string $expiredDate,
    string $trxId,
    ?string $description,
    ?string $currency = 'IDR'
  ) {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'virtualAccountName' => $virtualAccountName,
      'totalAmount' => (object) [
        'value' => (string) $total,
        'currency' => $currency
      ],
      'expiredDate' => $expiredDate, // '2024-09-21T14:32:00+07:00',
      'trxId' => $trxId, //'fedca321',
      'additionalInfo' => (object) [
        'description' => $description
      ]
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
      $clientSecret,
      $partnerId,
      $this->pathUpdate,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      'PUT'
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathUpdate",
      $headersRequest,
      $bodyRequest,
      'PUT'
    );

    return $response;
  }

  public function updateStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $trxId,
    string $paidStatus
  ) {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'trxId' => $trxId, //'fedca321',
      'paidStatus' => $paidStatus
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
      $clientSecret,
      $partnerId,
      $this->pathUpdateStatus,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      'PUT'
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathUpdateStatus",
      $headersRequest,
      $bodyRequest,
      'PUT'
    );

    return $response;
  }

  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $trxId
  ) {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'trxId' => $trxId, //'fedca321',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
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

  public function delete(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $trxId
  ) {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'trxId' => $trxId, //'fedca321',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
      $clientSecret,
      $partnerId,
      $this->pathDelete,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      "DELETE"
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathDelete",
      $headersRequest,
      $bodyRequest,
      'DELETE'
    );

    return $response;
  }

  public function getReport(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    string $startDate,
    string $startTime,
    string $endTime,
    ?string $customerCode = null,
    ?string $uniqueCode = null
  ) {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'startDate' => $startDate,
      'startTime' => $startTime,
      'endTime' => $endTime
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
      $clientSecret,
      $partnerId,
      $this->pathGetReport,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      "POST"
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathGetReport",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }

  public function inquiryStatus(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerServiceId,
    int $customerNo,
    string $inquiryRequestId
  ) {
    $additionalBody = [
      'partnerServiceId' => $partnerServiceId,
      'customerNo' => (string) $customerNo,
      'virtualAccountNo' => "$partnerServiceId$customerNo",
      'inquiryRequestId' => $inquiryRequestId, //'fedca321',
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaWS(
      $clientSecret,
      $partnerId,
      $this->pathInquiryStatus,
      $accessToken,
      $channelId,
      $this->externalId,
      $timestamp,
      json_encode($additionalBody, true),
      'POST'
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->pathInquiryStatus",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }
}
