<?php

namespace Agelgil\CBETransactionVerifier;

readonly class Transaction
{
    public function __construct(
        public string $id,
        public string $payer,
        public string $receiver,
        public string $amount,
        public string $reason,
        public string $paidAt,
    ) {}
}
