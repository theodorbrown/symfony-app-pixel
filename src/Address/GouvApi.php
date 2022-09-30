<?php

namespace App\Address;

use App\Address\AddressApiInterface;

class GouvApi implements AddressApiInterface {

    const URL = 'https://api-adresse.data.gouv.fr/search/';

    public function searchAddress(string $s): array {
       $curl = curl_init(); 
       curl_setopt($curl, CURLOPT_URL, self::URL . '?' . http_build_query(['q' => $s]));

        //indiquer que la valeur de retour est une chaine
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);


        $result = curl_exec($curl);
        curl_close($curl);

        return json_decode($result, true);
    }
}