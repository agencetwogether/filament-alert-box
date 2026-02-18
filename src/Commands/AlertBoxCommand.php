<?php

namespace Agencetwogether\AlertBox\Commands;

use Illuminate\Console\Command;

class AlertBoxCommand extends Command
{
    public $signature = 'filament-alert-box';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
