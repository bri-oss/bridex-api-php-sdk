<?php

namespace BRI\TransferCredit;

use BRI\Util\ExecuteCurlRequest;
use BRI\Util\PrepareRequest;
use BRI\Util\VarNumber;
use InvalidArgumentException;

interface InterbankTransferInterface {
  public function inquiry(
    string $clientSecret,
    string $partnerId,
    string $baseUrl,
    string $accessToken,
    string $channelId,
    string $timestamp,
    string $beneficiaryBankCode,
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
    string $beneficiaryAccountName,
    string $beneficiaryAccountNo,
    string $beneficiaryBankCode,
    string $sourceAccountNo,
    string $transactionDate,
    ?string $currency,
    ?string $beneficiaryAddress,
    ?string $beneficiaryBankName,
    ?string $beneficiaryEmail,
    ?string $customerReference,
    ?string $deviceId,
    ?string $channel
  ): string;
}

class InterbankTransfer implements InterbankTransferInterface {
  private ExecuteCurlRequest $executeCurlRequest;
  private PrepareRequest $prepareRequest;
  private string $externalId;
  private string $pathInquiry = '/interbank/snap/v1.0/account-inquiry-external';
  private string $pathTransfer = '/interbank/snap/v1.0/transfer-interbank';

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
    string $beneficiaryBankCode,
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
      $beneficiaryBankCode,
      $beneficiaryAccountNo
    );

    $baseUrl = rtrim($baseUrl, '/');
    $url = "$baseUrl/{$this->pathInquiry}";

    $additionalBody = [
      'beneficiaryBankCode' => $beneficiaryBankCode,
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
        $url,
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
    string $beneficiaryAccountName,
    string $beneficiaryAccountNo,
    string $beneficiaryBankCode,
    string $sourceAccountNo,
    string $transactionDate,
    ?string $currency = 'IDR',
    ?string $beneficiaryAddress,
    ?string $beneficiaryBankName,
    ?string $beneficiaryEmail,
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
      $beneficiaryAccountName,
      $beneficiaryAccountNo,
      $beneficiaryBankCode,
      $sourceAccountNo,
      $transactionDate
    );

    $baseUrl = rtrim($baseUrl, '/');
    $url = "$baseUrl/{$this->pathTransfer}";

    $additionalBody = [
      'partnerReferenceNo' => $partnerReferenceNo,
      'amount' => (object) [
        'value' => $value,
        'currency' => $currency
      ],
      'beneficiaryAccountName' => $beneficiaryAccountName,
      'beneficiaryAccountNo' => $beneficiaryAccountNo,
      'beneficiaryAddress' => $beneficiaryAddress, // optional
      'beneficiaryBankCode' => $beneficiaryBankCode,
      'beneficiaryBankName' => $beneficiaryBankName, // optional
      'beneficiaryEmail' => $beneficiaryEmail, // optional
      'customerReference' => $customerReference, // optional
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
        $url,
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
