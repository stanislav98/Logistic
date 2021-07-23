<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    /**
     * A basic unit test example.
     * Used to test xss
     * @return void
     */
    public function test_contact_form()
    {   
        $name = 'Some Name';
        $email = 'email@abv.bg';
        $phone = '0885588111';
        $message = 'Test Example Message';
        $response = $this->postJson('/api/contact', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message,
        ]);

        $response
        ->assertStatus(200)
        ->assertExactJson(['notification' => 
            [
                'msg' => 'Успешно изпратихте съобщението си! Благодарим ви!' ,
                'type' => 1, 
                'active' => 1
            ] 
        ]);
    }

     /**
     * Test the validation of the form
     * Im passing correct data to the controller and ensure that we get the correct response
     * If mail or inserting in the database or validation fails the test will be not valid
     * @return void
     */
public function test_xss_sanitizer()
{   
    $name = 'Some Name Asd';
    $email = 'email@abv.bg';
    $phone = '0885588333';
    $message = 'Test Example Message Test Example MessageTest Example message Test Example Message Test Example Message Test Example Message Test Example Message Test Example Message Test Example Message Test Example MessageTest Example MessageTest Test Example Message Test Example Message Test <script>st Example MessageTest Example MessageTest Example MessageTest Example MessageTest Example MessageTest Example MessageTest Example Message';
    $response = $this->postJson('/api/contact', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message,
    ]);

    $response
    ->assertStatus(200);
}


    /**
     * A basic unit test example.
     * Used to test xss
     * @return void
     */
public function test_login()
{   
    $email = 'staskata_1998@abv.bg';
    $password = 'password';
    $response = $this->postJson('/api/login', [
        'email' => $email,
        'password' => $password,
    ]);

    $response
    ->assertStatus(200)
    ->assertJsonStructure([
        'user',
        'firm',
        'vehicles',
        'token',
        'users'
    ]);
}

      /**
     * A basic unit test example.
     * Used to test xss
     * @return void
     */
public function test_firms_unauthorized()
{   
    $response = $this->getJson('/api/firms', [
        'id' => 1,
    ]);

    $response
    ->assertStatus(401);
}

}
