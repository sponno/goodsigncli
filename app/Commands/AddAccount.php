<?php

namespace App\Commands;

use App\Base\BaseCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Yaml\Yaml;

class AddAccount extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'login';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add your API key if you have an existing account';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $account = (object)[];
        // check to see if we have an account but no api key
        if($this->accountExists()){
            $account = $this->getAccount();
            if(!empty($account->apikey)){
                $this->comment('Your account is setup and we have saved your API key in account.yaml');
                if(!$this->ask('Do you want to continue?','yes')){
                    return;
                }
            }
        }

        $account->name = $this->ask("What's your name");
        $account->email = $this->ask("What's your email");
        $this->comment("Add your existing API key from GoodSign. You can find it here https://goodsign.io/profile/apikeys");
        $account->apikey = $this->ask("Add your existing API key from GoodSign.");
        $this->setAccount($account);
        $this->comment("Your account details and API key have been saved to ./account.yaml");


    }

    function processEmailCodeForApiKey(){
        $account = $this->getAccount();
        getcode: // loop back to try again.
        $code = $this->ask('Please enter the code we emailed you');

        $response = Http::withOptions(["verify"=>false])->post($this->getBasePath().'/api/qs/verifycode', ['code'=>$code, 'email'=>$account->email]);
        $data = json_decode((string)$response->getBody());

        if($data->success){
            $account->apikey = $data->key;
            File::put(getcwd() . "/account.yaml", Yaml::dump((array)$account, 1));
            $this->info($data->msg);
            $this->alert('I have saved your API key the ./account.yaml file located in this dir');
            $this->comment('You can also login to your account at https://goodsign.io/');
            $this->comment('Move onto our API section and send a document for signing');
        }else{
            $this->info($data->msg);
            $tryAgain =$this->confirm('Try again',true);
            if($tryAgain) goto getcode;
        }
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
