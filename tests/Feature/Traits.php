<?php

namespace Tests\Feature;

trait Traits {
    public function createPerson($endPoint, $type) {
        $response = $this->postJson($endPoint, [
            'name' => 'Anything',
            'phone1' => '0559451776'
        ]);
        
        return $response['id'];
    }

    public function createAccessory($endPoint) {
        $response = $this->postJson($endPoint, [
            'name' => 'Wirless Earphone',
            'brand' => 'Samsung',
            'notes' => 'Nothing to say',
            'type_id' => 1
        ]);
        
        return $response['id'];
    }

    public function createItem($endPoint, $good_id, $defaultPrice) {
        $response = $this->postJson($endPoint . $good_id, [
            'delta' => 1,
            'currentQuantity' => 0,
            'defaultPrice' => $defaultPrice
        ]);
        
        return $response['id'];
    }

    public function createTransaction($endPoint, $person_id) {
        $response = $this->postJson($endPoint, [
            'person_id' => $person_id,
            'cart' => [
                [1, [
                        [35000, 1],
                        [34000, 2]
                    ]
                ],
                [2, [
                        [1000, 2]
                    ]
                ]
            ]
        ]);
        
        return $response['id'];
    }
}