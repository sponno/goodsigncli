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

class UploadPdfWithFields extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'api:pdf-fields';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Upload a PDF with `texttags` and `extra fields` then send to signers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkAccount();
        $account = $this->getAccount();
        // check to see if we have an account but no api key

        if (!File::exists('pdf_grid.pdf')) {

            $this->info("Downloading GoodSign pdf_grid.pdf");
            $this->info("  We'll be working with this file, and I'll show you how to add fields to the document");
            $this->info("  Then we will upload the document, GoodSign will process the new fields and send it off.");
            $this->warn("  Downloading quickstrart_guide.pdf to this directory.");
            $response = Http::withOptions(["verify" => false])->withToken($account->apikey)->get('https://goodsign.io/assets/pdf/pdf_grid.pdf', []);
            File::put(getcwd() . "/pdf_grid.pdf", $response->getBody());
            $this->warn("Saved 'pdf_grid.pdf' to this directory.");
        }

        // Send off document.
        $json = new JsonBase('');
        $json->add('name', $this->ask('Document Namex?', "GoodSign Guide"));
        $json->addSigner('signer1', $account->name, $account->email);
        $json->addExtraField('signer1','sign','','',10,20,100,10,1);
        $json->addExtraField('signer1','signxl','','',200,50,100,10,1); // need to move this one down a bit
        $json->addExtraField('signer1','c1','','x',10,100,10,10,1);
        $json->addExtraField('signer1','c1','','',10,120,10,10,1);
        $json->addExtraField('signer1','input','','Created via the API',10,200,100,10,1);
        $json->addExtraField('signer1','input','?','I am optional',10,220,100,10,1);
        $json->addExtraField('signer1','name','','',10,300,100,10,1);
        $json->addExtraField('signer1','email','','',10,320,100,10,1,'color:red');
        $json->addExtraField('signer1','date','','',10,340,100,10,1);
        File::put(getcwd() . "/send_pdf.json", $json->getJsonString());

        $curl = $json->getCurlForPdfUploadLong('send_pdf.json','pdf_grid.pdf');
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
