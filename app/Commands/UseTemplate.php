<?php

namespace App\Commands;

use App\Base\BaseCommand;
use App\Base\JsonBase;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Yaml\Yaml;
use function Termwind\render;

class UseTemplate extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'api:template';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'GoodSign API - Send Templates and Documents';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkAccount();
        $account = $this->getAccount();

        $this->title("Let's find some Templates");
        $this->comment($this->getBasePath().'/api/templates');

        templateloader:
        $response = Http::withOptions(["verify"=>false])->withToken($account->apikey)->get($this->getBasePath().'/api/templates', []);

        $templates = (string)$response->getBody();
        //dd($response->getStatusCode());
        $templates = json_decode($templates);


        if($response->getStatusCode() == 404){
            $this->warn("Your account doesn't have any templates");
            if($this->confirm("Would you like me to add a Demo template?",true)){
                $result = $this->addDemoTemplate();
                print_r($result);
                if($result->success){
                    $this->info('Demo template created');
                    goto templateloader;
                }
            }else{
                $this->info('Ok, you can create your own template here https://goodsign.io/dashboard?filter=template');
                return;
            }
        }

        // create a menu - only show top 15.
        $templateList = array_map(function ($item){return $item->name;},$templates);
        $templateList = array_slice($templateList,0,15);// max 15 items
        $option = $this->menu('Select Template – use arrow keys',
        $templateList
        )->setBackgroundColour('black')->open();

        if($option === null){
            $this->warn('No template selected');
            return;
        }

        $template = $templates[$option];

        $this->info('Template selected: '. $template->name);
        $this->info('Template UUID: '. $template->uuid);
        $this->info('Total Signers: '. count($template->signers));
        $this->line('----------------------','info');

        $json = new JsonBase($template->uuid);
        $json->add('name',$this->ask('Document Name?',$template->name));
        $signerCount = 1;
        foreach($template->signers as $signer){
            if($signerCount == 1){
                $this->info("Signer '{$signer->key}' : {$account->name}, {$account->email}" );
                //$this->comment("Using  - {$account->name},{$account->email}" );
                $json->addSigner($signer->key,$account->name, $account->email);
            }else{
                $this->info("Signer '{$signer->key}' : add a new contact below");
                $name = $this->ask("Enter a name for the '{$signer->key}'?");
                nameagain:
                $email = trim($this->ask("And their email '{$signer->key}'?"));
                if($email == $account->email){
                    $this->warn("Sorry you cannot use the same emails as your email eg {$account->email}");
                    goto nameagain;
                }
                $json->addSigner($signer->key,$name, $email);
            }

            $signerCount++;

        }
        File::put(getcwd() . "/sendtemplate.json", $json->getJsonString() );

        $curl = $json->getCurlForTemplateLong('sendtemplate.json');
        $this->comment("Success, copy and run the command below to call the GoodSign api.\n");
        render($curl);
        $this->comment('');

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
