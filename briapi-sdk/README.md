# SNAP-BI-PHP-SDK

This SDK is to help to integrate easily with [SNAP BI](https://developers.bri.co.id/index.php/id/snap-bi/create-snap-key).
You can get the account balance inquiry and balance statement. aside from that you can also get the access token and create the signature api access for SNAP BI, if you wish to access other SNAP BI product's.

## Installation

1. extract the zip file to the folder of your choosing.
2. edit the '$baseDir' variable in autoload.php file so that it points to the correct directory where you extracted.

```php
$baseDir = __DIR__ . '/../briapi-sdk/src/';
```

3. put include/require 'autoload.php' in your script where you want to use this library

## Usage

### Balance-Inquiry-API

This api is used to inquiry account balance. For detailed documentation for this API, you can access this [link](https://developers.bri.co.id/id/snap-bi/apidocs-balance-inquiry-snap-bi)

Example:

```php
require __DIR__ . '/../briapi-sdk/autoload.php';

use BRI\Balance\Balance;
use BRI\Token\AccessToken;
use BRI\Util\RandomNumber;
use BRI\Signature\Signature;

$account = '111231271284142'; //account number
$partnerId = 'foobar'; // Partner ID
$channelId = 'foobar'; // Channel ID

//generate random External ID
$externalId = (new RandomNumber())->generateRandomNumber(9);;

//get current timestamp based on current timezone
$timestamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d\TH:i:s.000P');

// user's SNAP BI credentials
$clientId = 'foobar';
$clientSecret = 'foobar';
$pKeyId = 'foobar';

// API Path URL
$baseUrl = 'Base URL Path'; // ex: https://xxxxxx.co.id
$accessTokenPath = 'Access token path'; // ex: /xxx/xxx/xxx
$path = 'API path'; // ex: /xxx/xxx/xxx

//get access token
$accessToken = (new AccessToken(new Signature()))->getAccessToken(
  $clientId,
  $pKeyId,
  $timestamp,
  $baseUrl,
  $accessTokenPath,
);

// print response to terminal
echo (new Balance())->inquiry($account, $clientSecret, $partnerId, $baseUrl, $path, $accessToken, $channelId, $externalId, $timestamp);
```

Response:

```
  {
         "responseCode": "2001100",
         "responseMessage": "Successful",
         "accountNo": "111231271284142",
         "name": "JONOMADE",
         "accountInfos": [
      {
           "holdAmount": {
           "value": "20000.00",
           "currency": "IDR"
      },
         "availableBalance": {
         "value": "130000.00",
         "currency": "IDR"
      },
        "ledgerBalance": {
        "value": "30000.00",
        "currency": "IDR"
    },
        "status": "0001"
   }
 ],
       "additionalInfo": {
       "productCode": "TV",
       "accountType": "SA"
   }
}
```

### Balance-Statement-API

This can be used to get the balance statement. For detailed documentation you can access this [link](https://developers.bri.co.id/id/snap-bi/api-bank-statement-snap-bi)

Example:

```php
require __DIR__ . '/../briapi-sdk/autoload.php';

use BRI\Balance\Balance;
use BRI\Token\AccessToken;
use BRI\Util\RandomNumber;
use BRI\Signature\Signature;

$account = '234567891012348'; //account number
$partnerId = 'foobar'; // Partner ID
$channelId = 'foobar'; // Channel ID

//generate random External ID
$externalId = (new RandomNumber())->generateRandomNumber(9);;

//get current timestamp based on current timezone
$timestamp = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))->format('Y-m-d\TH:i:s.000P');

// user's SNAP BI credentials
$clientId = 'foobar';
$clientSecret = 'foobar';
$pKeyId = 'foobar';

// API Path URL
$baseUrl = 'Base URL Path'; // ex: https://xxxxxx.co.id
$accessTokenPath = 'Access token path'; // ex: /xxx/xxx/xxx
$path = 'API path'; // ex: /xxx/xxx/xxx

//Date format is in local timestamp
$startDate = '2022-06-06T12:09:00+07:00'; // start date
$endDate = '2022-06-08T20:09:00+07:00'; // end date

//get access token
$accessToken = (new AccessToken(new Signature()))->getAccessToken(
  $clientId,
  $pKeyId,
  $timestamp,
  $baseUrl,
  $accessTokenPath,
);

// print response to terminal
echo (new Balance())->statement($account, $startDate, $endDate, $clientSecret, $partnerId, $baseUrl, $path, $accessToken, $channelId, $externalId, $timestamp);
```

Response:

```
  {
         "responseCode": "2001400",
         "responseMessage": "Successful",
         "totalCreditEntries": {
         "numberOfEntries": "500",
         "amount": {
         "value": "100000000.00",
         "currency": "IDR"
      }
   },

         "totalDebitEntries": {
         "numberOfEntries": "500",
         "amount": {
         "value": "100000000.00",
         "currency": "IDR"
      }
    },
         "detailData": [
      {
         "detailBalance": {
          "startAmount": [
      {
         "amount": {
         "value": "100000000.00",
         "currency": "IDR"
       }
     }
   ],
         "endAmount": [
        {
             "amount": {
             "value": "20000.00",
             "currency": "IDR"
          }
       }
     ]
  },
                "amount": {
                "value": "20000.00",
                "currency": "IDR"
       },
             "transactionDate": "2022-05-01T08:09:00+07:00",
             "remark": "Payment to Warung Ikan Bakar 1",
             "transactionId": "20200801198230912830091121",
             "type": "Credit"
         }
      ]
    }
```

### Access Token & Signature API Access

This can be used to get an account token and signature token to be used in BRI API calls. For detailed documentation for this BRI API, you can access this [link](https://developers.bri.co.id/id/snap-bi/apidocs-oauth-snap-bi)

#### Access Token example:

```php
require __DIR__ . '/../briapi-sdk/autoload.php';

use BRI\Token\AccessToken;
use BRI\Signature\Signature;

// user's SNAP BI credentials
$clientId = 'foobar';
$pKeyId = 'foobar';

// API Path URL
$baseUrl = 'Base URL Path'; // ex: https://xxxxxx.co.id
$accessTokenPath = 'Access token path'; // ex: /xxx/xxx/xxx

$timestamp = (new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d\TH:i:s.000P');

// print response to terminal
echo (new AccessToken(new Signature()))->getAccessToken(
         $clientId,
         $pKeyId,
         $timestamp,
         $baseUrl,
         $accessTokenPath,
      );
```

Response:

```
{
    "accessToken": "jwy7GgloLqfqbZ9OnxGxmYOuGu85",
    "tokenType": "BearerToken",
    "expiresIn": "899"
}
```

#### Signature API Access example:

```php
require __DIR__ . '/../briapi-sdk/autoload.php';

use BRI\Signature\Signature;

// user's SNAP BI credentials
$clientSecret = 'foobar';

// API Path URL
$accessToken = 'jwy7GgloLqfqbZ9OnxGxmYOuGu85';
$path = 'API path'; // ex: /xxx/xxx/xxx

// body request for account informations API
$dataRequest = ['accountNo' => $account];

// body request for account mutations informations API
$dataRequest = [
         'accountNo' => $account,
         'fromDateTime' => $startDate,
         'toDateTime' => $endDate
      ];

$bodyRequest = json_encode($dataRequest, true);

$timestamp = (new DateTime('now', new DateTimeZone($timezone)))->format('Y-m-d\TH:i:s.000P');

// print response to terminal
echo  (new Signature())->generateRequest($clientSecret, 'POST', $timestamp, $accessToken, $bodyRequest, $path);
```

Response:

```
5c3caab43f550e0f5680d128aca799150cff35a9255038ff5cf07d853a5198b05605759d8639a38b06a43ad5466da18ae22c08bd62238da1116ff26f71c8eb39
```
