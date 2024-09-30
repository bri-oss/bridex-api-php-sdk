<?php

namespace BRI\CardlessCashWithdrawal;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;

interface CardlessWithdrawalInterface {
  public function cardlessWithdrawal(
    string $baseUrl,
    string $clientId,
    string $secretKey
  ): string;
}

class CardlessWithdrawal implements CardlessWithdrawalInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $path = '/v1/cardless/withdrawal';

  public function __construct() {
    $this->executeCurlRequest = new ExecuteCurlRequest();
    $this->prepareRequest = new PrepareRequest();
  }

  public function cardlessWithdrawal(
    string $baseUrl,
    string $clientId,
    string $secretKey
  ): string {
    $additionalBody = [
      "token" => "920331011",
      "msisdn" => "811882168118292736",
      "merchantTrxID" => "1000000007039547384739451",
      "atmLocation" => "KCK. 0206-CRM Hitachi"
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->CardlessCashWithdrawal(
      $clientId,
      $secretKey,
      json_encode($additionalBody, true)
    );

    $response = $this->executeCurlRequest->execute(
      "$baseUrl$this->path",
      $headersRequest,
      $bodyRequest,
      'POST'
    );

    return $response;
  }
}
