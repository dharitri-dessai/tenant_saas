<?php

namespace App\Service\Formatter;

use App\Interface\DataFormatterInterface;

class DataFormatterManager
{
    private array $formatters = [];

    public function addFormatter(DataFormatterInterface $formatter): void
    {
        $this->formatters[] = $formatter;
    }

    public function formatAll(string $data): array
    {
        $results = [];
        foreach ($this->formatters as $formatter) {
            $results[] = $formatter->format($data);
        }
        return $results;
    }
}