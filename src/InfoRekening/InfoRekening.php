<?php

namespace BRI\InfoRekening;

use BRI\Token\AccessToken;
use BRI\Signature\Signature;
use BRI\Util\RandomNumber;
use DateTime;
use DateTimeZone;

class InfoRekening
{
   private const METHOD = 'POST';
   private const CONTENT_TYPE = 'application/json';
   private const CHANNEL_ID = 'SNPBI';

   public function getInfoRekening(
      string $account,
      string $clientId,
      string $clientSecret,
      string $pKeyId,
      string $partnerId,
      string $baseUrl,
      string $path,
      string $accessTokenPath,
      string $timezone = 'Asia/Jakarta',
      int $randomLength = 9
   ) {

      // body request
      $dataRequest = ['accountNo' => $account];
      $bodyRequest = json_encode($dataRequest, true);

      //Generate number random for X-External-id and titmestamp
      $randomNumber = (new RandomNumber())->generateRandomNumber($randomLength);
      $timestamp = (new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d\TH:i:s.000P');

      // access Token
      $accessToken = (new AccessToken(new Signature()))->getAccessToken(
         $clientId,
         $pKeyId,
         $timestamp,
         $baseUrl,
         $accessTokenPath,
      );

      //Signature request
      $signatureRequest = (new Signature())->generateRequest($clientSecret, self::METHOD, $timestamp, $accessToken, $bodyRequest, $path);


      //Header request 
      $headersRequest = array(
         "X-TIMESTAMP:" . $timestamp,
         "X-SIGNATURE:" . $signatureRequest,
         "Content-Type: " . self::CONTENT_TYPE,
         "X-PARTNER-ID: " . $partnerId,
         "CHANNEL-ID: " . self::CHANNEL_ID,
         "X-EXTERNAL-ID:" . $randomNumber,
         "Authorization: Bearer " . $accessToken,
      );

      // Execute CURL request
      $chPost1 = curl_init();
      curl_setopt_array($chPost1, array(
         CURLOPT_URL => "$baseUrl$path",
         CURLOPT_HTTPHEADER => $headersRequest,
         CURLOPT_POSTFIELDS => $bodyRequest,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_CUSTOMREQUEST => "POST",
      ));

      $response = curl_exec($chPost1);
      curl_close($chPost1);

      return $response;
   }
}
