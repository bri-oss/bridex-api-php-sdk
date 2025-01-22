<?php
namespace BRI\VirtualAccount;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;

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

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->prepareRequest = new PrepareRequest();
  }

  public function inquiry(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string {
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
  }

  public function payment(
    string $partnerId,
    string $clientId,
    string $clientSecret,
    string $baseUrl,
    string $accessToken,
    ?string $passApp
  ): string {
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
  }
}
