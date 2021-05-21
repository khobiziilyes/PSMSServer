<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function testSearch() {
        $response = $this->postJson('/api/phones', ['query' => 'Gal']);
        $this->Log('idk', $response->json());
    }

    public function A() {
        $delete = false;
        $type = 'buy';

        $endPoint = "/api/transactions/$type";

        $response = $delete ? $this->deleteJson("$endPoint/$delete") : $this->postJson($endPoint, [
            'person_id' => 1,
            'cart' => [
                /*[1, [
                        [35000, 1],
                        [34000, 2]
                    ]
                ],*/
                [2, [
                        [1000, 2]
                    ]
                ]
            ]
        ]);
        
        $this->Log('idk', $response->original);

        //return $response['id'];
    }
}