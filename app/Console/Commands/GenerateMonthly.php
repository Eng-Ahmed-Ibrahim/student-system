<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\StudentService;

class GenerateMonthly extends Command
{
        private $StudentService;
    function __construct(StudentService $StudentService) {
                parent::__construct(); 

        $this->StudentService=$StudentService;
    }
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->StudentService->generate_genral_monthly_fee();
        $this->info('Daily attendance generated successfully.');
    }
}
