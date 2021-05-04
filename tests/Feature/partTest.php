<?php

namespace Tests\Feature;
use Tests\Feature\Traits;

class partTest extends featureBase {
    use Traits;

    public function testAnything() {
        $response = $this->deleteJson('/api/buy/2');
        
        $this->Log('idk', $response->original);

        //return $response['id'];
    }
}