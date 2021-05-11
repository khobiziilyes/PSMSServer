<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class fullTest extends featureBase {
    use Traits;

    public $accessories_endPoint = '/api/accessories/';
    public $items_endPoint = '/api/items/';
    public $transactions_endPoint = '/api/transactions/';
    public $people_endPoint = '/api/';

    protected $cleanUp = true;

    public function testPeople() {
        foreach (['vendors', 'customers'] as $type) {
            $people_endPoint = $this->people_endPoint . $type . '/';

            $person_id = $this->createPerson($people_endPoint, $type);
            $this->checkExists($people_endPoint, $person_id);
            
            if ($this->cleanUp) {
                $this->performDelete($people_endPoint, $person_id);
                $this->checkDeleted($people_endPoint, $person_id);
            }
        }
    }

    public function testAccessories() {
        $accessories_endPoint = $this->accessories_endPoint;
        
        $accessory_id = $this->createAccessory($accessories_endPoint, 1);
        $this->checkExists($accessories_endPoint, $accessory_id);

        if ($this->cleanUp) {
            $this->performDelete($accessories_endPoint, $accessory_id);
            $this->checkDeleted($accessories_endPoint, $accessory_id);
        }
    }

    public function testItems() {
        foreach (['phone', 'accessory'] as $i => $type) {
            $items_endPoint = $this->items_endPoint . $type . '/';
            $item_id = $this->createItem($items_endPoint, $i + 1, 3500 * (10 ^ $i));
            
            $this->checkExists($this->items_endPoint, $item_id);

            if ($this->cleanUp) {
                $this->performDelete($this->items_endPoint, $item_id);
                $this->checkDeleted($this->items_endPoint, $item_id);
            }
        }
    }

    public function transactions() {
        $transactions_endPoint = $this->transactions_endPoint;
        $transaction_id = $this->createTransaction($transactions_endPoint . 'buy/', 13, 3,
            [
                '867142047842606',
                //'867142048042602'
            ]
        );
        
        $transaction_id = $this->createTransaction($transactions_endPoint . 'sell/', 14, 3,
            [
                //'867142047842606',
                '867142048042602'
            ]
        );
    }
}