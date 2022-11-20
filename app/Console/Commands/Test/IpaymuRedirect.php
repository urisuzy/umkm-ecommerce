<?php

namespace App\Console\Commands\Test;

use Illuminate\Console\Command;

class IpaymuRedirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ipaymu:redirect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $service = new \App\Services\IPaymu;
        print_r($service->makeRedirectPayment(['Baju'], [1], [100000], '1'));
        return Command::SUCCESS;
    }
}
