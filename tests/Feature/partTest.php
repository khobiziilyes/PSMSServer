<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function F() {
        //$response = $this->postJson('/api/phones', ['query' => 'Gal']);
        $CRUD_PERMISSIONS = config('app.CRUD_PERMISSIONS');
        
        $PD = $CRUD_PERMISSIONS->flatMap(function($permission) {
            return [$permission => true];
        })->toArray();

        $response = $this->patchJson('/api/owner/users/5', $PD);
        $this->Log('idk', $response->json());
    }

    public function testA() {
        $B1 = [
            [1, [
                    [35000, 2],
                ]
            ],
            [2, [
                    [1000, 2],
                ]
            ]
        ];

        $B2 = [
            [1, [
                    [36000, 1],
                    [35000, 1]
                ]
            ]
        ];

        $S1 = [
            [2, [
                    [1200, 1]
                ]
            ]
        ];

        $B3 = [
            [1, [
                    [34000, 1]
                ]
            ]
        ];

        $S2 = [
            [2, [
                    [900, 1]
                ]
            ],
            [1, [
                    [35000, 1]
                ]
            ]
        ];

        $B4 = [
            [2, [
                    [1000, 3],
                    [900, 2]
                ]
            ],
            [1, [
                    [35000, 1],
                    [33000, 1]
                ]
            ]
        ];

        $this->performTransaction(true, $B1);
        $this->performTransaction(true, $B2);

        $this->performTransaction(false, $S1);

        $this->performTransaction(true, $B3);

        $this->performTransaction(false, $S2);

        $this->performTransaction(true, $B4);
    }

    function performTransaction($isBuy, $cart) {
         $response = $this->postJson('/api/' . ($isBuy ? 'buy' : 'sell'), [
            'person_id' => $isBuy ? 1 : 2,
            'cart' => $cart
        ]);

        $this->Log('idk', $response->original);

        return $response['id'];
    }

    function deleteTransaction($id) {
        $response = $this->deleteJson('/api/transaction/' . $id);
        // $this->Log('idk', $response->original);

        return $response['id'];
    }
}