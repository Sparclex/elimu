<?php

namespace App\Cards;

use Illuminate\Support\Facades\Cache;
use Laravel\Nova\Card;

class LatestUpdatesCard extends Card
{
    /**
     * The width of the card (1/3, 1/2, or full).
     *
     * @var string
     */
    public $width = '1/2';

    public function __construct($component = null)
    {
        parent::__construct($component);

        $this->withMeta(['changes' => $this->loadLatestCommits()]);
    }

    /**
     * Get the component name for the element.
     *
     * @return string
     */
    public function component()
    {
        return 'cards-latest-updates';
    }

    public function loadLatestCommits()
    {
        return Cache::remember('changelog', now()->addMinutes(5), function () {
            exec('git log -n 5 --pretty=format:"%ar:%s"', $output);
            return collect($output)->map(function ($line) {
                list($time, $message) = explode(':', $line);
                $message = str_limit($message, 50);
                return compact('time', 'message');
            });
        });
    }
}
