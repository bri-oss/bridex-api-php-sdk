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

  private function validateInputs(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp = null
  ): void {
    if (empty($partnerId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $partnerId)) {
      throw new InvalidArgumentException('Invalid or missing partnerId.');
    }
    if (empty($clientId) || !preg_match('/^[a-zA-Z0-9\-]{3,}$/', $clientId)) {
      throw new InvalidArgumentException('Invalid or missing clientId.');
    }
    if (empty($clientSecret) || strlen($clientSecret) < 8) {
      throw new InvalidArgumentException('Invalid or missing clientSecret. Must be at least 8 characters.');
    }
    if (empty($baseUrl) || !filter_var($baseUrl, FILTER_VALIDATE_URL)) {
      throw new InvalidArgumentException('Invalid or missing baseUrl. Must be a valid URL.');
    }
    if (empty($accessToken) || strlen($accessToken) < 16) {
      throw new InvalidArgumentException('Invalid or missing accessToken. Must be at least 16 characters.');
    }
    if ($passApp !== null && strlen($passApp) < 6) {
      throw new InvalidArgumentException('Invalid passApp. If provided, it must be at least 6 characters.');
    }
  }

  private function executeRequest(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $accessToken,
    string $baseUrl,
    string $path,
    array $body
  ): string {
    try {
      list($bodyRequest, $headersRequest) = $this->prepareRequest->VABrivaOnline(
        $partnerId,
        $clientId,
        $clientSecret,
        $accessToken,
        "$baseUrl$path",
        'POST',
        json_encode($body, true)
      );

      return $this->executeCurlRequest->execute(
        "$baseUrl$path",
        $headersRequest,
        $bodyRequest,
        'POST'
      );
    } catch (InvalidArgumentException $e) {
      throw new \RuntimeException('Input validation error: ' . $e->getMessage(), 0, $e);
    } catch (\Exception $e) {
      throw new \RuntimeException('Error executing request: ' . $e->getMessage(), 0, $e);
    }
  }

  public function inquiry(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string {
    $this->validateInputs($partnerId, $clientId, $clientSecret, $baseUrl, $accessToken, $passApp);

    $body = [
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

    return $this->executeRequest($partnerId, $clientId, $clientSecret, $accessToken, $baseUrl, $this->pathInquiry, $body);
  }

  public function payment(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string {
    $this->validateInputs($partnerId, $clientId, $clientSecret, $baseUrl, $accessToken, $passApp);

    $body = [
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

    return $this->executeRequest($partnerId, $clientId, $clientSecret, $accessToken, $baseUrl, $this->pathPayment, $body);
  }
}
