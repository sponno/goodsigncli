<?php

namespace App\Providers;

use LaravelZero\Framework\Components\Updater\Strategy\StrategyInterface;
use Phar;

final class GoodSignUpdateStrategy extends  \Humbug\SelfUpdate\Strategy\DirectDownloadStrategyAbstract
{

    /**
     * Returns the Download Url.
     *
     * @param  array  $package
     * @return string
     */
    public function getDownloadUrl(): string
    {
        //https://github.com/laravel-zero/laravel-zero/raw/v9.2.0//builds/goodsigncli):
        //https://github.com/sponno/goodsigncli/blob/master/builds/goodsigncli
        return 'https://github.com/sponno/goodsigncli/raw/f05d6bf43d37f8299816f632d261807e8083ea75/builds/goodsigncli';

        //$downloadUrl = str_replace('releases/download', 'raw', $downloadUrl);

        //return $downloadUrl.'/builds/'.basename(Phar::running());
    }


}
