<?php

namespace Agelgil\CBETransactionVerifier;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class Verify
{
    public function __construct(
        protected Receipt $receipt,
        protected Tesseract $tesseract,
        protected Parser $parser,
        protected string $pattern = '/FT[A-Z0-9]{10}/'
    ) {
        //
    }

    public function pattern(string $pattern = '/FT[A-Z0-9]{10}/'): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    /** @throws TesseractOcrException|ConnectionException|RequestException */
    public function fromImage(string $path, string $accountLast8): ?Transaction
    {
        $content = $this->tesseract->transactionId($path);

        if ($transactionId = Str::match($this->pattern, $content)) {
            return $this->fromTransactionId($transactionId, $accountLast8);
        }

        return null;
    }

    /** @throws ConnectionException|RequestException */
    public function fromTransactionId(string $transactionId, string $accountLast8): Transaction
    {
        $document = $this->receipt->fetch($transactionId, $accountLast8);

        return $this->parser->parse($document->getText());
    }
}
