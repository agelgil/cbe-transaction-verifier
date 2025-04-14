<?php

namespace Agelgil\CBETransactionVerifier;

use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Document;
use Smalot\PdfParser\Parser;

class Receipt
{
    public function __construct(
        protected Parser $parser,
        protected string $prefix = 'https://apps.cbe.com.et:100/?id='
    ) {
        //
    }

    public function prefix(string $prefix = 'https://apps.cbe.com.et:100/?id='): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /** @throws Exception|ConnectionException */
    public function fetch(string $transactionId, string ...$accountLast8): ?Document
    {
        foreach ($accountLast8 as $last8) {
            $url = $this->prefix.$transactionId.$last8;

            /** @var Response $response */
            $response = Http::withoutVerifying()->get($url);

            if ($response->successful()) {
                return $this->parser->parseContent($response->body());
            }
        }

        return null;
    }
}
