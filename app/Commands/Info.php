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

class Info extends BaseCommand
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'info';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'About the GoodSign CLI app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkAccount();
        $this->alert("This is DONE! \n space here hello");
        $this->line("bold line \033[1mbold\033[0m text");
        $this->comment("This is a comment <b>bold</b> text");
        render('text <b class="text-red-600">bold'.(10-3).'</b> text');
        $this->title("hello friends");
        $this->question('? Whats going on here');
        $this->info('Some info here');
        $this->warn("testing warning");
        $this->error('You failed');
        $this->msg('my Message here');
        //$this->alert("this is really goocl");
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
