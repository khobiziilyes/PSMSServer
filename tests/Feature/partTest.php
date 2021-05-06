<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function testAnything() {
        $delete = 6;

        $response = $delete ? $this->deleteJson('/api/transactions/sell/' . $delete) : $this->postJson('/api/transactions/sell', [
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