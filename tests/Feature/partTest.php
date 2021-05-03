<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function testAnything() {
        $response = $this->postJson('/api/sell', [
            'costPerItem' => 35000,
            'Quantity' => 1,
            'person_id' => 14,
            'item_id' => 1,
            'imei' => ['867142047842606'] //['867142047842606', '867142048042602']
        ]);
        
        $this->Log('idk', $response->original);


        //return $response['id'];
    }
}