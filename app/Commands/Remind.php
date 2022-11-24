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

class Remind extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'api:remind';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Send a reminder email to a signer of a document';

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
        $uuid = $this->ask("What is the UUID of the document you want to send a reminder?");

        $response = Http::withOptions(["verify"=>false])->withToken($account->apikey)->get($this->getBasePath().'/api/document/'.$uuid, []);
        ;

        if($response->getStatusCode() == 404){
            $this->warn("Your document was not found");
            goto getuuid;
        }
        $doc = json_decode((string)$response->getBody());


        // create a menu - only show top 15.
        $emails = array_map(function ($signer){return $signer->contact->email;},$doc->master_doc->signers);
        //$templateList = array_slice($templateList,0,15);// max 15 items
        $option = $this->menu('Select a signer – use arrow keys',
        $emails
        )->setBackgroundColour('black')->open();

        if($option === null){
            $this->warn('No signer was selected');
            return;
        }
        $email = $emails[$option];
        $json = new JsonBase();
        $curl = $json->getCurlForReminder($uuid, $email);
        render($curl);

        // Send off document.
//        $json = new JsonBase('');
//        $json->add('name', $this->ask('Document Name?', "GoodSign Guide"));
//        $json->addSigner('signer1', $account->name, $account->email);
//        File::put(getcwd() . "/send_pdf.json", $json->getJsonString());
//
//        $curl = $json->getCurlForPdfUploadLong('send_pdf.json');
//        $this->comment("Success, copy and run the command below to call the GoodSign api.\n");
//        render($curl);
//        $this->comment('');
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
