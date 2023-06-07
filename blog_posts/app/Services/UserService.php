<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpKernel\Exception\HttpException;


class UserService extends ApiService
{
    public function __construct()
    {
        $this->endpoint = env('USERS_MS') . '/api';
       
    }
    
}

abstract class ApiService
{
    protected string $endpoint;

    public function request($method, $path, $data = [])
    {
        $response = $this->getRequest($method, $path, $data);

        if ($response->ok()) {return $response->json();};

        throw new HttpException($response->status(), $response->body());
    }

    public function getRequest($method, $path, $data = [])
    {
        return Http::acceptJson()->withHeaders([
            'Authorization' =>  'Bearer ' . request()->cookie('token')
        ])->$method("http://127.0.0.1:8000/api/{$path}", $data);
    }

    public function post($path, $data)
    {
        return $this->request('post', $path, $data);
    }

    public function get($path)
    {
        return $this->request('get', $path);
    }

    public function put($path, $data)
    {
        return $this->request('put', $path, $data);
    }

    public function delete($path)
    {
        return $this->request('delete', $path);
    }
}