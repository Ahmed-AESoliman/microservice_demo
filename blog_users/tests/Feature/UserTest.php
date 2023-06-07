<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase,WithFaker;

    /** @test */
    public function user_can_sign_up_normal()
    {
        $this->withoutExceptionHandling();
        $userData=$this->userRegistrationBaseData();
        $response = $this->post('/api/register',$userData);
        $response->assertJson(function (AssertableJson $json) use($userData) {
            $json
                ->has('data')
                ->whereType('data', 'array')
                ->where('data.userData.email',$userData['email'])
                ->where('data.userData.name',$userData['name'])
                ->where('data.userData.img',$userData['img'])
                ->etc();
        });
        $response->assertStatus(201);
    }

    /** @test */
    public function validates_passwords_are_same()
    {
        $userData = $this->userRegistrationBaseData();
        $userData['password_confirmation'] = 'cpassword';

        $this->json('POST', route('register'), $userData)
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has('errors.password')
                    ->etc();
            });
    }
    /** @test */
    public function validates_unique_email()
    {
        $user = User::factory()->create();
        $userData = $this->userRegistrationBaseData();
        $userData['email'] = $user->email;

        $this->json('POST', route('register'), $userData)
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has('errors.email')
                    ->etc();
            });
    }
    /** @test */
    public function validates_user_data()
    {
        $userData = [];

        $this->json('POST', route('register'), $userData)
            ->assertStatus(422)
            ->assertJson(function (AssertableJson $json) {
                $json->has('errors.name')
                ->has('errors.email')
                ->has('errors.password')
                ->etc();
            });
    }
    /** @test */
    public function send_verify_email()
    {
        $this->withoutExceptionHandling();
        $userData = $this->userRegistrationBaseData();

        Notification::fake();

        $response = $this->post('/api/register',$userData);
        $response->assertStatus(201);
        $user = User::firstWhere('email', $userData['email']);
        Notification::assertSentTo($user,VerifyEmailNotification::class);
        Notification::assertCount(1);
    }
        
    public function test_verify_email_validates_user()
    {
        $this->withoutExceptionHandling();
        $notification =  new VerifyEmail();

        $user = User::factory()->unverified()->create();
        $this->assertFalse($user->hasVerifiedEmail());

        $mail = $notification->toMail($user);
        $uri = $mail->actionUrl;
        $this->actingAs($user)->get($uri);
        $this->assertTrue(User::find($user->id)->hasVerifiedEmail());
    }
    public function test_email_already_verified()
    {
        $this->withoutExceptionHandling();
        $notification =  new VerifyEmail();

        $user = User::factory()->create();
        $this->assertTrue($user->hasVerifiedEmail());
        $mail = $notification->toMail($user);
        $uri = $mail->actionUrl;
        $this->actingAs($user)->get($uri)
        ->assertStatus(200)
        ->assertJson(function (AssertableJson $json) {
            $json->has('message')
            ->where('message','Account already Activated Successfully !')
            ->etc();
        });
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
}
