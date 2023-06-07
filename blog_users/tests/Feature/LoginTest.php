<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase,WithFaker;

     public function test_user_cannot_login_when_authenticated()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/api/login');
        $response->assertRedirect('/');
    }
    public function test_user_can_login_with_correct_credentials()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => '123456',
        ]);

        $response->assertJson(function (AssertableJson $json) use($user) {
            $json
                ->has('data')
                ->whereType('data', 'array')
                ->where('data.userData.id',$user->id)
                ->where('data.userData.email',$user->email)
                ->where('data.userData.name',$user->name)
                ->where('data.userData.img',$user->img)
                ->etc();
        });
        $response->assertStatus(200);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        

        $this->json('POST','/api/login',  [
            'email' => $user->email,
            'password' => 'invalid-password',
        ])
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has('error.msg')
                ->where('error.msg','The provided credentials are not correct')
                    ->etc();
            });
    }
    
    public function test_validates_user_login_data()
    {
        $this->json('POST', '/api/login', [])
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has('errors.email')
                ->has('errors.password')
                ->etc();
            });
    }
    public function test_user_login_email_not_unverified()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->unverified()->create();
        $this->assertFalse($user->hasVerifiedEmail());
        
        $this->json('POST', '/api/login', [
            'email' => $user->email,
            'password' => '123456',
        ])
            ->assertStatus(403)
            ->assertJson(function (AssertableJson $json) {
                $json->has('error.msg')
                ->where('error.msg','Your email address is not verified.')
                ->etc();
            });
    }
}
