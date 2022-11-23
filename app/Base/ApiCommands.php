<?php

namespace App\Base;

use Illuminate\Support\Facades\Http;

class ApiCommands extends BaseCommand
{
    public function addDemoTemplate(){
        $response = Http::withOptions(["verify"=>false])->withToken($this->getApiKey())->get($this->getBasePath().'/api/api/add-demo', []);
        return  json_decode((string)$response->getBody());
    }

}
