<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function testAnything() {
        $delete = 1;
        $type = 'buy';

        $endPoint = "/api/transactions/$type";

        $response = $delete ? $this->deleteJson("$endPoint/$delete") : $this->postJson($endPoint, [
            'person_id' => 14,
            'cart' => [
                [
                    'item_id' => 2,
                    'costPerItem' => 38670,
                    'Quantity' => 3
                ]
            ]
        ]);
        
        $this->Log('idk', $response->original);

        //return $response['id'];
    }
}