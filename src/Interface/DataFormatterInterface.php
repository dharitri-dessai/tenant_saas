<?php

namespace   App\Interface;

interface DataFormatterInterface {

    public function format(string $data) : string;
}