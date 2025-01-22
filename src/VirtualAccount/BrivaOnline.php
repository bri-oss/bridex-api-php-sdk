<?php
namespace BRI\VirtualAccount;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use InvalidArgumentException;

interface BrivaOnlineInterface {
  public function inquiry(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string;

  public function payment(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string;
}

class BrivaOnline implements BrivaOnlineInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $pathInquiry = '/v1/transfer-va/inquiry';
  private string $pathPayment = '/v1/transfer-va/payment';

  public function __construct(
    ExecuteCurlRequest $executeCurlRequest,
    PrepareRequest $prepareRequest
  ) {
    $this->executeCurlRequest = $executeCurlRequest;
    $this->prepareRequest = $prepareRequest;
  }

  public function inquiry(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string {
    // Validate partnerId
    if (empty($partnerId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $partnerId)) {
        throw new InvalidArgumentException('Invalid or missing partnerId.');
    }

    // Validate clientId
    if (empty($clientId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $clientId)) {
        throw new InvalidArgumentException('Invalid or missing clientId.');
    }

    // Validate clientSecret
    if (empty($clientSecret) || strlen($clientSecret) < 8) {
        throw new InvalidArgumentException('Invalid or missing clientSecret. Must be at least 8 characters.');
    }

    // Validate baseUrl
    if (empty($baseUrl) || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid or missing baseUrl. Must be a valid URL.');
    }

    // Validate accessToken
    if (empty($accessToken) || strlen($accessToken) < 16) {
        throw new InvalidArgumentException('Invalid or missing accessToken. Must be at least 16 characters.');
    }

    // Validate passApp (optional)
    if ($passApp !== null && strlen($passApp) < 6) {
        throw new InvalidArgumentException('Invalid passApp. If provided, it must be at least 6 characters.');
    }

    $additionalBody = [
      'partnerServiceId' => "   77777",
      'customerNo' => "0000000000001",
      'virtualAccountNo' => "          777770000000000001",
      'trxDateInit' => "2021-11-25T22:01:07+07:00", //'optional',
      'channelCode' => 1, //'optional',
      'sourceBankCode' => "002",//optional
      'amount' => (object) [
        'value' => "200000.2",
        'currency' => "IDR"
      ], // optional
      'passApp' => $passApp, //optional
      'inquiryRequestId' => "e3bcb9a2-e253-40c6-aa77-d72cc138b744",
      'additionalInfo' => (object) [
        'idApp' => "TEST1234" //optional
      ]
    ];

    try {
      list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaOnline(
        $partnerId,
        $clientId,
        $clientSecret,
        $accessToken,
        "$baseUrl$this->pathInquiry",
        'POST',
        json_encode($additionalBody, true)
      );

      $response = $this->executeCurlRequest->execute(
        "$baseUrl$this->pathInquiry",
        $headersRequest,
        $bodyRequest,
        'POST'
      );

      return $response;
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
  }

  public function payment(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string {
    // Validate partnerId
    if (empty($partnerId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $partnerId)) {
        throw new InvalidArgumentException('Invalid or missing partnerId.');
    }

    // Validate clientId
    if (empty($clientId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $clientId)) {
        throw new InvalidArgumentException('Invalid or missing clientId.');
    }

    // Validate clientSecret
    if (empty($clientSecret) || strlen($clientSecret) < 8) {
        throw new InvalidArgumentException('Invalid or missing clientSecret. Must be at least 8 characters.');
    }

    // Validate baseUrl
    if (empty($baseUrl) || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
        throw new InvalidArgumentException('Invalid or missing baseUrl. Must be a valid URL.');
    }

    // Validate accessToken
    if (empty($accessToken) || strlen($accessToken) < 16) {
        throw new InvalidArgumentException('Invalid or missing accessToken. Must be at least 16 characters.');
    }

    // Validate passApp (optional)
    if ($passApp !== null && strlen($passApp) < 6) {
        throw new InvalidArgumentException('Invalid passApp. If provided, it must be at least 6 characters.');
    }

    $additionalBody = [
      'partnerServiceId' => "   77777",
      'customerNo' => "0000000000001",
      'virtualAccountNo' => "          777770000000000001",
      'virtualAccountName' => "John Doe",
      'trxDateInit' => "2021-11-25T22:01:07+07:00", //'optional',
      'channelCode' => 1, //'optional',
      'sourceBankCode' => "002",//optional
      'trxId' => "2132902068917559061",
      'paidAmount' => (object) [
        'value' => "200000.2",
        'currency' => "IDR"
      ], // optional
      'paymentRequestId' => "e3bcb9a2-e253-40c6-aa77-d72cc138b744", //optional
      'additionalInfo' => (object) [
        'idApp' => "TEST", //optional,
        'passApp' => $passApp
      ]
    ];

    try {
      list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaOnline(
        $partnerId,
        $clientId,
        $clientSecret,
        $accessToken,
        "$baseUrl$this->pathPayment",
        'POST',
        json_encode($additionalBody, true)
      );

      $response = $this->executeCurlRequest->execute(
        "$baseUrl$this->pathPayment",
        $headersRequest,
        $bodyRequest,
        'POST'
      );

      return $response;
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error fetching access token: ' . $e->getMessage(), 0, $e);
    }
  }
}
