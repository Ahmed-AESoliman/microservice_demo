<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    public function test_user_can_login_with_correct_credentials()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/login', [
            'email' => 'ahmd@gmail.com',
            'password' => '123456',
        ]);

        $response->assertJson(function (AssertableJson $json) {
            $json
                ->has('data')
                ->whereType('data', 'array')
                ->has('data.userData.id')
                ->has('data.userData.email')
                ->has('data.userData.name')
                ->has('data.userData.img')
                ->has('data.accessToken')
                ->etc();
        });
        $response->assertStatus(200);
    }
    public function test_create_post()
    {
        $this->withoutExceptionHandling();
        $user = $this->post('/api/login', [
            'email' => 'ahmd@gmail.com',
            'password' => '123456',
        ]);
        $postData=$this->useCreatePostData($user['data']['userData']['id']);

        $post =$this->withCookies(['accessToken'=>$user['data']['accessToken']])->post('/api/posts', $postData)
        ->assertStatus(201);
    }

    private function userRegistrationBaseData(): array
    {
        return [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
            'img' => $this->faker->image,
            'password' => '123456',
            'password_confirmation' => '123456',
        ];
    }
    private function useCreatePostData($userId): array
    {
        return [
            'title' => $this->faker->title,
            'text' => $this->faker->text(200),
            'img' => $this->faker->image,
            'creator'=>$userId
        ];
    }
}
