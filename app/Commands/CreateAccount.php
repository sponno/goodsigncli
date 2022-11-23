<?php

namespace App\Commands;

use App\Base\BaseCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Yaml\Yaml;

class CreateAccount extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'register';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Get started with GoodSign - Create a brand new account and get your API key';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $account = null;
        // check to see if we have an account but no api key
        if($this->accountExists()){
            $account = $this->getAccount();
            if($account->email!='' && empty($account->apikey)){
                return $this->processEmailCodeForApiKey();
            }
            if(!empty($account->apikey)){
                $this->comment('Your account is setup and we have saved your API key in account.yaml');
                $this->comment('run `./goodsign` to see all available commands');
                return;
            }
        }

        $name = $this->ask("What's your name");
        $email = $this->ask("What's your email");
        $password = $this->secret("What's your password");
        $this->info('One sec… registering your account');

        $response = Http::withOptions(["verify"=>false])->post($this->getBasePath().'/api/qs/test', ['name'=>$name,'email'=>$email,'password'=>$password]);
        $data = json_decode((string)$response->getBody());

        if($data->success){
            $this->setAccount((object)['name'=>$name, 'email'=>$email]);
            //File::put(getcwd() . "/account.yaml", Yaml::dump(, 1));
            $this->info("Your account has been created.");
            $this->comment("Final step : we need to verify your email account");
            $this->processEmailCodeForApiKey(); // captured.
        } else{
            $this->info($data->message);
        }
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
