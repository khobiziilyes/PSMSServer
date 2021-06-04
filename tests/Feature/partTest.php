<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function What() {
        //$response = $this->postJson('/api/phones', ['query' => 'Gal']);
        $response = $this->putJson('/api/accessories/2', ['name' => 'Tnaket']);
        $this->Log('idk', $response->json());
    }

    public function testA() {
        $isBuy = false;
        $endPoint = "/api/transactions" . ($isBuy ? '/?isBuy' : '');

        $response = (gettype($isBuy) === 'string') ? $this->deleteJson("$endPoint/$isBuy") : $this->postJson($endPoint, [
            'person_id' => $isBuy ? 1 : 2,
            'cart' => [
                [1, [
                        //[35000, 1],
                        [32000, 1]
                    ]
                ],
                /*
                [2, [
                        [1200, 1]
                    ]
                ]
                */
            ]
        ]);
        
        $this->Log('idk', $response->original);

        //return $response['id'];
    }
}