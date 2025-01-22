<?php

namespace BRI\VirtualAccount;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

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
    string $total,
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
    string $total,
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

  public function __construct(
    ExecuteCurlRequest $executeCurlRequest,
    PrepareRequest $prepareRequest
  ) {
    $this->executeCurlRequest = $executeCurlRequest;
    $this->prepareRequest = $prepareRequest;
    $this->externalId = (new VarNumber())->generateVar(9);
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
    string $total,
    string $expiredDate,
    string $trxId,
    ?string $description,
    ?string $currency = 'IDR'
  ) {
    // Validate clientSecret
    if (empty($clientSecret) || strlen($clientSecret) < 8) {
        throw new InvalidArgumentException('Invalid or missing clientSecret. Must be at least 8 characters.');
    }

    // Validate partnerId
    if (empty($partnerId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $partnerId)) {
        throw new InvalidArgumentException('Invalid or missing partnerId.');
    }

    // Validate baseUrl
    if (empty($baseUrl) || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid or missing baseUrl. Must be a valid URL.');
    }

    // Validate accessToken
    if (empty($accessToken) || strlen($accessToken) < 16) {
        throw new InvalidArgumentException('Invalid or missing accessToken. Must be at least 16 characters.');
    }

    // Validate channelId
    if (empty($channelId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $channelId)) {
        throw new InvalidArgumentException('Invalid or missing channelId.');
    }

    // Validate timestamp
    if (!strtotime($timestamp)) {
        throw new InvalidArgumentException('Invalid or missing timestamp.');
    }

    // Validate partnerServiceId
    if (empty($partnerServiceId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $partnerServiceId)) {
        throw new InvalidArgumentException('Invalid or missing partnerServiceId.');
    }

    // Validate customerNo
    if ($customerNo <= 0) {
        throw new InvalidArgumentException('Invalid customerNo. Must be a positive integer.');
    }

    // Validate virtualAccountName
    if (empty($virtualAccountName)) {
        throw new InvalidArgumentException('Invalid or missing virtualAccountName.');
    }

    // Validate total
    if (empty($total) || !is_numeric($total) || $total <= 0) {
        throw new InvalidArgumentException('Invalid total. Must be a positive number.');
    }

    // Validate expiredDate
    if (!strtotime($expiredDate)) {
        throw new InvalidArgumentException('Invalid or missing expiredDate.');
    }

    // Validate trxId
    if (empty($trxId)) {
        throw new InvalidArgumentException('Invalid or missing trxId.');
    }

    // Validate optional description
    if ($description !== null && strlen($description) > 255) {
        throw new InvalidArgumentException('Invalid description. Must not exceed 255 characters.');
    }

    // Validate optional currency
    if ($currency !== null && !in_array(strtoupper($currency), ['IDR', 'USD', 'EUR'])) {
        throw new InvalidArgumentException('Invalid currency. Must be IDR, USD, or EUR.');
    }

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

    try {
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
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
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
    string $total,
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

    try {
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
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
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
