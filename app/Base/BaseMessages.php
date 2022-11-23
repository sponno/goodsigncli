<?php

namespace App\Base;

use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class BaseMessages extends Command
{

     /**
     * Write a string in an alert box."Two line \n comment use new line"
     *
     * @param  string  $string
     * @param  int|string|null  $verbosity
     * @return void
     */
    public function alert($string, $verbosity = null)
    {

        $lines = explode("\n", $string);
        $lines = array_map("trim", $lines);
        $lengths = array_map('strlen', $lines);
        $lengths = array_map('strip_tags', $lengths);
        $length = max($lengths)+12;
        $this->comment(str_repeat('*', $length), $verbosity);
        foreach ($lines as $line) {
            $this->comment('*     '.str_pad($line,$length-12).'     *', $verbosity);
        }
        $this->comment(str_repeat('*', $length), $verbosity);

        $this->comment('', $verbosity);
    }

    /*
     * Displays the given string as title.
     */
    public function title(string $title): Command
    {
        $size = strlen($title);
        $spaces = str_repeat(' ', $size);

        $this->output->newLine();
        $this->output->writeln("<bg=gray;fg=black>$spaces$spaces$spaces</>");
        $this->output->writeln("<bg=gray;fg=black>$spaces$title$spaces</>");
        $this->output->writeln("<bg=gray;fg=black>$spaces$spaces$spaces</>");
        $this->output->newLine();

        return $this;
    }


    public function msg($string, $verbosity = null)
    {
        if (! $this->output->getFormatter()->hasStyle('msg')) {
            $style = new OutputFormatterStyle('gray');
            $this->output->getFormatter()->setStyle('msg', $style);
        }
        $this->line($string, 'msg', $verbosity);
    }

}
