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

    public function createAccessory($endPoint, $type_id) {
        $response = $this->postJson($endPoint, [
            'name' => 'Galaxy S4',
            'brand' => 'Samsung',
            'notes' => 'Nothing to say',
            'type_id' => $type_id
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

    public function createTransaction($endPoint, $person_id, $item_id) {
        $response = $this->postJson($endPoint, [
            'person_id' => $person_id,
            'cart' => [
                [
                    'item_id' => 1,
                    'Quantity' => 1,
                    'costPerItem' => 40000 
                ]
            ]
        ]);
        
        $this->Log('idk', $response->original);

        return $response['id'];
    }
}