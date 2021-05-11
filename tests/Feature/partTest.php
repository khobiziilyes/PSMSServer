<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function what() {
        $response = $this->putJson('/api/accessories/4', ['query' => 'Redmi Note 10']);
        $this->Log('idk', $response->json());
    }

    public function does() {
        $delete = false;
        $type = 'buy';

        $endPoint = "/api/transactions/$type";

        $response = $delete ? $this->deleteJson("$endPoint/$delete") : $this->postJson($endPoint, [
            'person_id' => 13,
            'cart' => [
                [
                    'item_id' => 6,
                    'costPerItem' => 38670,
                    'Quantity' => 3
                ]
            ]
        ]);
        
        $this->Log('idk', $response->original);

        //return $response['id'];
    }
}