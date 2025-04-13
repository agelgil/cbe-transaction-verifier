<?php

namespace Agelgil\CBETransactionVerifier;

use DateMalformedStringException;
use DateTime;

class Parser
{
    /** @var array<string,string> */
    protected array $pattern = [
        'id' => '/Reference No\. \(VAT Invoice No\)\t((?:FT|TT)[A-Z0-9]{10})\n/',
        'payer' => '/Payer\t([^\n]+)\n/',
        'receiver' => '/Receiver\t([^\n]+)\n/',
        'amount' => '/Transferred Amount\t([\d,.]+) ETB\n/',
        'reason' => '/Reason \/ Type of service\t([^\n]+)\n/',
        'paidAt' => '/Payment Date & Time\t(\d{1,2}\/\d{1,2}\/\d{4}, \d+:\d+:\d+ (?:AM|PM))\n/',
    ];

    public function pattern(string $field, string $pattern): self
    {
        $this->pattern[$field] = $pattern;

        return $this;
    }

    public function parse(string $text): Transaction
    {
        return new Transaction(
            id: $this->parseString($this->pattern['id'], $text),
            payer: $this->parseString($this->pattern['payer'], $text),
            receiver: $this->parseString($this->pattern['receiver'], $text),
            amount: $this->parseFloat($this->pattern['amount'], $text),
            reason: $this->parseString($this->pattern['reason'], $text),
            paidAt: $this->parseDate($this->pattern['paidAt'], $text),
        );
    }

    protected function parseString(string $pattern, string $subject): string
    {
        preg_match($pattern, $subject, $matches);

        return $matches ? $matches[1] ?? '' : '';
    }

    protected function parseFloat(string $pattern, string $subject): string
    {
        $number = $this->parseString($pattern, $subject);

        return strval(floatval(str_replace(',', '', $number)));
    }

    protected function parseDate(string $pattern, string $subject): string
    {
        $date = $this->parseString($pattern, $subject);

        if ($date) {
            try {
                return (new DateTime($date))->format(DATE_ATOM);
            } catch (DateMalformedStringException) {
            }
        }

        return '';
    }
}
