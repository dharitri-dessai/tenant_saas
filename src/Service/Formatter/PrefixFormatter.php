<?php

namespace App\Service;

use App\Interface\DataFormatterInterface;

class PrefixFormatter implements DataFormatterInterface {

    public function format(string $data): string
    {
        return 'Tenant: '. $data;
    }
}