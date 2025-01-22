<?php

namespace BRI\TransferCredit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

interface TransactionStatusInquiryInterface {
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $originalPartnerReferenceNo,
    string $serviceCode,
    string $transactionDate,
    ?string $deviceId,
    ?string $channel
  ): string;
}

class TransactionStatusInquiry implements TransactionStatusInquiryInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $path = '/snap/v1.0/transfer/status';

  public function __construct(
    ExecuteCurlRequest $executeCurlRequest,
    PrepareRequest $prepareRequest
  ) {
    $this->executeCurlRequest = $executeCurlRequest;
    $this->prepareRequest = $prepareRequest;
    $this->externalId = (new VarNumber())->generateVar(9);
  }

  private function validateInputs(string ...$inputs): void {
    foreach ($inputs as $input) {
      if (empty($input)) {
        throw new InvalidArgumentException("Required parameter is missing or empty.");
      }
    }
  }

  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $originalPartnerReferenceNo,
    string $serviceCode,
    string $transactionDate,
    ?string $deviceId,
    ?string $channel
  ): string {
    $this->validateInputs(
      $clientSecret,
      $partnerId,
      $baseUrl,
      $accessToken,
      $channelId,
      $timestamp,
      $originalPartnerReferenceNo,
      $serviceCode,
      $transactionDate,
      $deviceId,
      $channel
    );

    $additionalBody = [
      'originalPartnerReferenceNo' => $originalPartnerReferenceNo,
      'serviceCode' => $serviceCode,
      'transactionDate' => $transactionDate,
      'additionalInfo' => (object) [
        'deviceId' => $deviceId,
        'channel' => $channel
      ]
    ];

    try {
      list($bodyRequest, $headersRequest) = $this->prepareRequest->TransferCredit(
        $clientSecret,
        $partnerId,
        $this->path,
        $accessToken,
        $channelId,
        $this->externalId,
        $timestamp,
        json_encode($additionalBody, true),
        'POST'
      );

      $response = $this->executeCurlRequest->execute(
        "$baseUrl$this->path",
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
