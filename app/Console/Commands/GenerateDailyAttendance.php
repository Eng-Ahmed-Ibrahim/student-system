<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\AttendanceService;

class GenerateDailyAttendance extends Command
{
    private $attendanceService;
    function __construct(AttendanceService $attendanceService) {
                parent::__construct(); 

        $this->attendanceService=$attendanceService;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-daily-attendance';

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
        $this->attendanceService->generateAttendanceIfNotExists();
        $this->info('Daily attendance generated successfully.');
    }
}
