<?php
namespace BRI\TransferCredit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

interface IntrabankTransferInterface {
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $beneficiaryAccountNo,
    ?string $deviceId,
    ?string $channel
  ): string;
  public function transfer(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerReferenceNo,
    string $value,
    string $beneficiaryAccountNo,
    string $sourceAccountNo,
    string $feeType,
    string $remark,
    string $transactionDate,
    ?string $currency = 'IDR',
    ?string $customerReference,
    ?string $deviceId,
    ?string $channel
  ): string;
}

class IntrabankTransfer implements IntrabankTransferInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInquiry = '/intrabank/snap/v1.0/account-inquiry-internal';
  private string $pathTransfer = '/intrabank/snap/v1.0/transfer-intrabank';

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

  /**
   * 
   * @param string $beneficiaryAccountNo
   */
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $beneficiaryAccountNo,
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
      $beneficiaryAccountNo,
      $deviceId,
      $channel
    );

    $additionalBody = [
      'beneficiaryAccountNo' => $beneficiaryAccountNo,
      'additionalInfo' => (object) [
        'deviceId' => $deviceId,
        'channel' => $channel
      ]
    ];

    try {
      list($bodyRequest, $headersRequest) = $this->prepareRequest->TransferCredit(
        $clientSecret,
        $partnerId,
        $this->pathInquiry,
        $accessToken,
        $channelId,
        $this->externalId,
        $timestamp,
        json_encode($additionalBody, true),
        'POST'
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

  public function transfer(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $partnerReferenceNo,
    string $value,
    string $beneficiaryAccountNo,
    string $sourceAccountNo,
    string $feeType,
    string $remark,
    string $transactionDate,
    ?string $currency = 'IDR',
    ?string $customerReference,
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
      $partnerReferenceNo,
      $value,
      $beneficiaryAccountNo,
      $sourceAccountNo,
      $feeType,
      $remark,
      $transactionDate,
      $currency,
      $customerReference,
      $deviceId,
      $channel
    );

    $additionalBody = [
      'partnerReferenceNo' => $partnerReferenceNo,
      'amount' => (object) [
        'value' => $value,
        'currency' => $currency
      ],
      'beneficiaryAccountNo' => $beneficiaryAccountNo,
      'customerReference' => $customerReference, // optional
      'feeType' => $feeType,
      'remark' => $remark,
      'sourceAccountNo' => $sourceAccountNo,
      'transactionDate' => $transactionDate,
      'additionalInfo' => (object) [ // optional
        'deviceId' => $deviceId,
        'channel' => $channel
      ]
    ];

    try {
      list($bodyRequest, $headersRequest) = $this->prepareRequest->TransferCredit(
        $clientSecret,
        $partnerId,
        $this->pathTransfer,
        $accessToken,
        $channelId,
        $this->externalId,
        $timestamp,
        json_encode($additionalBody, true),
        'POST'
      );

      $response = $this->executeCurlRequest->execute(
        "$baseUrl$this->pathTransfer",
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
