<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testExample()
    {
        
        $this->assertDatabaseHas('users', [
            'email' => 'niyobuhungiro.yves@gmail.com',
        ]);
        $response = $this->get('/');
        $response->assertStatus(200);
    }


}
