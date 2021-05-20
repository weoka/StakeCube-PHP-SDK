<?php

namespace Stakecube;

class Stakecube{
    const API_BASE_URL = "https://stakecube.io/api/v2";

    public function __construct($public_key, $private_key)
    {
        $this->public_key = $public_key;
        $this->private_hey = $private_key;
    }
}