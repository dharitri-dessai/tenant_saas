<?php

namespace App\Service;

use App\Interface\DataFormatterInterface;

class UppercaseFormatter implements DataFormatterInterface {

    public function format(string $data): string
    {
        return strtoupper($data);
    }
}