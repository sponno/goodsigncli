<?php

namespace App\Base;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Yaml\Yaml;

class BaseCommand extends BaseMessages
{
    function accountExists():bool{
        return File::exists(getcwd() . "/account.yaml");
    }

    function checkAccount(){
         if($this->accountExists()){
            $account = $this->getAccount();
            if(empty($account->apikey)){
                $this->warn('We could not find your API key - please run the `./goodsign start` command');
                exit();
            }
        }else{
             $this->warn('We could not find your account - please run the `./goodsign start` command');
             exit();

         }
    }

    // Basic local storage for data
    function getAccount():Object{
        $account = File::get(getcwd() . "/account.yaml");
        return (object)Yaml::parse($account);
    }

    function getApiKey():string{
        $account = File::get(getcwd() . "/account.yaml");
        return ((object)Yaml::parse($account))->apikey;
    }



    public function setAccount(Object $account){
        File::put(getcwd() . "/account.yaml", Yaml::dump((array)$account, 1));
    }

    public function getBasePath(){
        if(File::exists(getcwd() . "/commands.txt")){
            return 'https://localhost:8000'; // dev
        }else{
            return 'https://goodsign.io'; // prod
        }
    }
    public function getApiJson(string $uuid, $toName, $email,$other){
        $json['uuid']=$uuid;
        $json['signers']=[];
        $json['signers'][]=['name'=>$toName, $email ];
    }

    public function addDemoTemplate(){
        $response = Http::withOptions(["verify"=>false])->withToken($this->getApiKey())->post($this->getBasePath().'/api/add-demo', []);
        return  json_decode((string)$response->getBody());
    }
}
