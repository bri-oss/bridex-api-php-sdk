<?php

namespace BRI\CardlessCashWithdrawal;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use InvalidArgumentException;

interface CardlessWithdrawalInterface {
  public function cardlessWithdrawal(
    string $baseUrl,
    string $clientId,
    string $secretKey,
    string $accessToken
  ): string;
}

class CardlessWithdrawal implements CardlessWithdrawalInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $path = '/v1/cardless/withdrawal';

  public function __construct(
    ExecuteCurlRequest $executeCurlRequest,
    PrepareRequest $prepareRequest
  ) {
    $this->executeCurlRequest = $executeCurlRequest;
    $this->prepareRequest = $prepareRequest;
  }

  public function cardlessWithdrawal(
    string $baseUrl,
    string $clientId,
    string $secretKey,
    string $accessToken
  ): string {
    if (empty($baseUrl) || empty($clientId) || empty($secretKey) || empty($accessToken)) {
      throw new InvalidArgumentException('All input parameters (baseUrl, clientId, secretKey, accessToken) are required.');
    }

    $additionalBody = [
      "token" => "920331011",
      "msisdn" => "811882168118292736",
      "merchantTrxID" => "1000000007039547384739451",
      "atmLocation" => "KCK. 0206-CRM Hitachi"
    ];

    list($bodyRequest, $headersRequest) = $this->prepareRequest->CardlessCashWithdrawal(
      $clientId,
      $secretKey,
      $accessToken,
      "$baseUrl$this->path",
      "POST",
      json_encode($additionalBody, true)
    );

    try {
      $response = $this->executeCurlRequest->execute(
        "$baseUrl$this->path",
        $headersRequest,
        $bodyRequest,
        'POST'
      );
    } catch (\Exception $e) {
      // Log the exception, handle retry mechanisms, etc.
      throw new \RuntimeException('Error executing cardless withdrawal: ' . $e->getMessage());
    }

    return $response;
  }
}
