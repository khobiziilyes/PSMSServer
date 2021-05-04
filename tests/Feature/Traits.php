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

    public function createGood($endPoint, $type_id) {
        $response = $this->postJson($endPoint, [
            'name' => 'Redmi note 7',
            'brand' => 'Xiaomi',
            'notes' => 'Nothing',
            'type_id' => $type_id
        ]);
        
        return $response['id'];
    }

    public function createItem($endPoint, $good_id) {
        $response = $this->postJson($endPoint, [
            'good_id' => $good_id,
            'delta' => '0',
            'currentQuantity' => 0,
            'defaultPrice' => 35000
        ]);
        
        $this->Log('idk', $response->original);

        return $response['id'];
    }

    public function createTransaction($endPoint, $person_id, $item_id) {
        $response = $this->postJson($endPoint, [
            'costPerItem' => 34000,
            'Quantity' => 1,
            'person_id' => $person_id,
            'item_id' => $item_id
        ]);
        
        $this->Log('idk', $response->original);

        return $response['id'];
    }
}