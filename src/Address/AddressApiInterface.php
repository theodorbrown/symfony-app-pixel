<?php

namespace App\Address;

interface AddressApiInterface {

    public function searchAddress(string $s): array;
}