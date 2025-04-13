<?php

namespace Agelgil\CBETransactionVerifier;

test('valid content', function () {
    $parser = new Parser;

    $transaction = $parser
        ->parse("
            Reference No. (VAT Invoice No)\tFT12345B0SSY\n
            Payer\tAlice\n
            Receiver\tBob\n
            Transferred Amount\t1,234.56 ETB\n
            Reason / Type of service\ttesting done vie Mobile\n
            Payment Date & Time\t1/1/2025, 11:58:59 AM\n 
        ");

    expect($transaction->id)->toBe('FT12345B0SSY')
        ->and($transaction->payer)->toBe('Alice')
        ->and($transaction->receiver)->toBe('Bob')
        ->and($transaction->reason)->toBe('testing done vie Mobile')
        ->and($transaction->amount)->toBe('1234.56')
        ->and($transaction->paidAt)->toBe('2025-01-01T11:58:59+00:00');
});

test('custom patter', function () {
    $parser = new Parser;

    $transaction = $parser
        ->pattern('payer', '/Paid To: (\w+)\n/')
        ->parse("Paid To: Alice\n");

    expect($transaction->payer)->toBe('Alice');
});

test('invalid date', function () {
    $parser = new Parser;

    $transaction = $parser
        ->pattern('paidAt', '/Paid At-> (\w+)\n/')
        ->parse("Paid At-> lorem\n");

    expect($transaction->paidAt)->toBe('');
});

test('empty content', function () {
    $parser = new Parser;

    $transaction = $parser->parse('');

    expect($transaction->id)->toBeEmpty()
        ->and($transaction->payer)->toBeEmpty()
        ->and($transaction->receiver)->toBeEmpty()
        ->and($transaction->reason)->toBeEmpty()
        ->and($transaction->amount)->toBe('0')
        ->and($transaction->paidAt)->toBeEmpty();
});
