<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class featureBase extends TestCase {
    public function setUp(): void {
        parent::setUp();

        $this->withHeaders([
            'Accept' => 'application/json'
        ]);
        
        $user = User::find(1);
        $this->actingAs($user, 'api')->assertAuthenticated('api');
    }

    public function checkExists($endPoint, $id) {
        $response = $this->getJson($endPoint . $id);
        $response->assertJson(['id' => $id]);
    }

    public function performDelete($endPoint, $id) {
        $response = $this->deleteJson($endPoint . $id);
        $this->assertTrue($response['deleted']);
    }

    public function checkDeleted($endPoint, $id) {
        $response = $this->getJson($endPoint);
        $response->assertJsonMissing(['id' => $id]);
    }

    public function Log($title, $content) {
        $content = 'Type: ' . \gettype($content) . "\n" . (is_string($content) ? $content : print_r($content, true));

        $finalText = "\033[32m $title : \033[0m \n";
        $content = preg_replace('/(?m)^/', "\t", $content);
        $finalText .= $content . "\n";

        fwrite(STDERR, $finalText);
    }
}
