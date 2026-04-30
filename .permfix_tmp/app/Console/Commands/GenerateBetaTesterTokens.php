<?php

namespace App\Console\Commands;

use App\Models\BetaTester;
use Illuminate\Console\Command;

class GenerateBetaTesterTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'beta-tester:generate-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate access tokens for beta testers who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating access tokens for beta testers...');

        $betaTestersWithoutToken = BetaTester::whereNull('access_token')->get();

        if ($betaTestersWithoutToken->isEmpty()) {
            $this->info('All beta testers already have access tokens.');
            return 0;
        }

        $this->info("Found {$betaTestersWithoutToken->count()} beta testers without tokens.");

        $bar = $this->output->createProgressBar($betaTestersWithoutToken->count());
        $bar->start();

        foreach ($betaTestersWithoutToken as $betaTester) {
            $betaTester->generateAccessToken();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Access tokens generated successfully!');

        return 0;
    }
}
