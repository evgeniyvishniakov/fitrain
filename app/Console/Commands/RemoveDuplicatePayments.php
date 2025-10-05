<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TrainerFinance;

class RemoveDuplicatePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:remove-duplicates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove duplicate payments from payment_history';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $finances = TrainerFinance::all();
        
        foreach ($finances as $finance) {
            $history = $finance->payment_history ?? [];
            $unique = [];
            
            foreach ($history as $payment) {
                $unique[$payment['id']] = $payment;
            }
            
            $totalPaid = collect(array_values($unique))->sum('amount');
            
            $finance->update([
                'payment_history' => array_values($unique),
                'total_paid' => $totalPaid
            ]);
            
            $this->info("Removed duplicates for finance ID: {$finance->id}");
        }
        
        $this->info('All duplicates removed successfully!');
    }
}
