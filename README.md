# CBE Transaction Verifier

## Installation

```shell
composer require agelgil/cbe-transaction-verifier
```

## Dependency

> Make sure you installed [tesseract-ocr](https://github.com/tesseract-ocr/tesseract)
> for verification based on image (e.g. screenshots).

## Usage

```php
use Agelgil\CBETransactionVerifier/Verify;
use Agelgil\CBETransactionVerifier/Transaction;

$verify = resolve(Verify::class);

$path = '/home/user/.../cbe-transaction****.png';
$accountLast8 = '12345678';

/** @var Transaction $transaction */
$transaction = $verifier->fromImage($path, $accountLast8);
// or
$transaction = $verifier->fromTransactionId($path, $accountLast8);

// $transaction->id
// $transaction->payer
// $transaction->receiver
// $transaction->amount
// $transaction->reason
// $transaction->paidAt
```
