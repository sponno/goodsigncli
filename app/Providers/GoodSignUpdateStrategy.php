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
        return 'https://goodsign.io/downloads/goodsign';
    }


}
