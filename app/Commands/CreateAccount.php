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
        $this->title( 'CREATE A NEW GOODSIGN ACCOUNT' );
        $this->line( "We'll get you setup with a brand new goodsign.io account" );

        // check to see if we have an account but no api key
        if($this->accountExists()){
            $account = $this->getAccount();
            if($account->email!='' && empty($account->apikey)){
                return $this->processEmailCodeForApiKey();
            }
            if(!empty($account->apikey)){
                $this->comment('Your account is setup and we have saved your API key in account.yaml');
                $this->comment('run `./goodsign` to see all available commands');
                $new=$this->ask('Would you like to create a new account? [y/N] ','N');
                if(strtolower($new)!='y'){
                    exit();
                }
            }
        }

        $name = $this->ask("What's your name");
        $email = $this->ask("What's your email");
        $password = $this->secret("What's your password");
        $this->info('Registering your GoodSign account...');

        $response = Http::withOptions(["verify"=>false])->post($this->getBasePath().'/api/qs/test', ['name'=>$name,'email'=>$email,'password'=>$password]);
        $data = json_decode((string)$response->getBody());

        if($data->success){
            $this->setAccount((object)['name'=>$name, 'email'=>$email]);
            //File::put(getcwd() . "/account.yaml", Yaml::dump(, 1));
            $this->info("Success!");
            $this->line("");
            $this->comment("FINAL STEP : we need to verify your email account.");
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
            $this->alert($data->msg);
//             success:
//            $this->alert("Your account has been verified! I've added 50 credits to get you started");
            $this->comment('Your details including API have been saved to ./account.yaml');
            $this->comment('You can also login with those details here  https://goodsign.io/');
            $this->line('');
            $this->comment('Next Step: run `./goodsign api:template` to send off a document');
        }else{
            $this->comment($data->msg);
            goto getcode;
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
