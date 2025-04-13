<?php

namespace Agelgil\CBETransactionVerifier;

use Illuminate\Support\Str;
use thiagoalessio\TesseractOCR\TesseractOCR;
use thiagoalessio\TesseractOCR\TesseractOcrException;

class Tesseract
{
    public function __construct(
        protected int $timeout = 2,
        protected string $pattern = '/FT[A-Z0-9]{10}/',
    ) {}

    /** @throws TesseractOcrException */
    public function transactionId(string $path): string
    {
        $ocr = new TesseractOCR($path);

        if (is_string($result = $ocr->run($this->timeout))) {
            return Str::match($this->pattern, $result);
        }

        return '';
    }

    public function pattern(string $pattern = '/FT[A-Z0-9]{10}/'): self
    {
        $this->pattern = $pattern;

        return $this;
    }

    public function timeout(int $timeout = 2): self
    {
        $this->timeout = $timeout;

        return $this;
    }
}
