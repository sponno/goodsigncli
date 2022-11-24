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

class VoidDoc extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'api:void';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Void a document that has been sent';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkAccount();
        $account = $this->getAccount();

        getuuid:
        $uuid = trim($this->ask("What is the UUID of the document you want void?"));

        $response = Http::withOptions(["verify"=>false])->withToken($account->apikey)->get($this->getBasePath().'/api/document/'.$uuid, []);


        if($response->getStatusCode() == 404){
            $this->warn("Your document was not found");
            goto getuuid;
        }
        $doc = json_decode((string)$response->getBody());
        if($doc->master_doc->status != 'sent'){
            $this->warn("You can only void docs that have been `sent` and are not yet complete - you doc's status is ".$doc->master_doc->status);
            goto getuuid;
        }

        $notify = $this->ask('Notify signers document has been voided [y/N]','y') =='y';
        $msg =  $this->ask('Add a message why the document was voided [enter to skip]   ');


        $json = new JsonBase();
        $curl = $json->getCurlForVoid($uuid, $notify, $msg);
        render($curl);
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
