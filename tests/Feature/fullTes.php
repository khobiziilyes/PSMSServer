<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class fullTest extends featureBase {
    use Traits;

    public $goods_endPoint = '/api/goods/';
    public $items_endPoint = '/api/items/';
    public $transactions_endPoint = '/api/transactions/';

    protected $cleanUp = false;
    
    public function testPeople() {
        public $people_endPoint = '/api/people/';

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

    public function testItem() {
        foreach (['accessories', 'phones'] as $type) {
            $goods_endPoint = $this->goods_endPoint . $type . '/';
            $items_endPoint = $this->items_endPoint;
            
            $good_id = $this->createGood($goods_endPoint, ($type === 'phones') ? '0' : '1');
            $this->checkExists($goods_endPoint, $good_id);

            $item_id = $this->createItem($items_endPoint, $good_id);
            $this->checkExists($items_endPoint, $item_id);

            if ($this->cleanUp) {
                $this->performDelete($items_endPoint, $item_id);
                $this->checkDeleted($items_endPoint, $item_id);

                $this->performDelete($goods_endPoint, $good_id);
                $this->checkDeleted($goods_endPoint, $good_id);
            }
        }
    }

    public function testTransaction() {
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