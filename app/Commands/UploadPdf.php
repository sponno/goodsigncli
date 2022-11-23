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

class UploadPdf extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'api:pdf';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Upload a PDF with `texttags` and send to signers';

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

        if (!File::exists('goodsign_guide.pdf')) {

            $this->info("Downloading GoodSign Quickstart PDF");
            $this->info("  We'll be working with this file, and I'll show you how to add fields to the document");
            $this->info("  Then we will upload the document, GoodSign will process the new fields and send it off.");
            $this->warn("  Downloading quickstrart_guide.pdf to this directory.");
            $response = Http::withOptions(["verify" => false])->withToken($account->apikey)->get('https://goodsign.io/assets/pdf/goodsign_guide_api_v1.pdf', []);
            File::put(getcwd() . "/goodsign_guide.pdf", $response->getBody());
            $this->warn("Success - guide created");
        }

        // Send off document.
        $json = new JsonBase('');
        $json->add('name', $this->ask('Document Name?', "GoodSign Guide"));
        $json->addSigner('signer1', $account->name, $account->email);
        File::put(getcwd() . "/send_pdf.json", $json->getJsonString());

        $curl = $json->getCurlForPdfUploadLong('send_pdf.json');
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
