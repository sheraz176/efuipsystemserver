<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteDuplicateLFDT extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:duplicatelfdt';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    public function handle()
    {
        $companyId = 20;

        $this->info("🔍 Checking duplicates for fixed company_id: {$companyId}");

        // --- 1. FIND DUPLICATES ---
        $duplicates = DB::table('customer_subscriptions')
            ->select('subscriber_msisdn', 'transaction_amount', 'productId', DB::raw('COUNT(*) as total'))
            ->where('plan_id', 5)
            ->where('api_source', 'LFDT')
            ->where('company_id', $companyId)
            ->where('policy_status', 1)
            ->groupBy('subscriber_msisdn', 'transaction_amount', 'productId')
            ->havingRaw('COUNT(*) > 1')
            ->get();

            dd($duplicates->counts);

        if ($duplicates->isEmpty()) {
            $this->info("✅ No duplicates found for company_id: {$companyId}");
            return;
        }

        $this->info("⚠️ Total duplicate groups found: " . $duplicates->count());


        // --- 2. LOOP THROUGH DUPLICATES ---
        foreach ($duplicates as $dup) {

            $this->info("➡ MSISDN: {$dup->subscriber_msisdn}, Amount: {$dup->transaction_amount}, ProductID: {$dup->productId} | Records: {$dup->total}");

            // fetch all duplicate record IDs (sorted)
            $ids = DB::table('customer_subscriptions')
                ->where('subscriber_msisdn', $dup->subscriber_msisdn)
                ->where('transaction_amount', $dup->transaction_amount)
                ->where('productId', $dup->productId)
                ->where('plan_id', 5)
                ->where('api_source', 'LFDT')
                ->where('company_id', $companyId)
                ->where('policy_status', 1)
                ->orderBy('id', 'asc')
                ->pluck('id')
                ->toArray();

                // dd($ids);

            // first id ko rakhna hai
            $keepId = array_shift($ids);

            // only delete if more records
            if (!empty($ids)) {

                $deletedCount = count($ids);

                // delete duplicate ids
                DB::table('customer_subscriptions')->whereIn('id', $ids)->delete();

                // terminal output
                $this->warn("🗑 Deleted {$deletedCount} duplicate records | Saved ID: {$keepId}");

                // log in custom channel dublicate_msisdn
                Log::channel('dublicate_msisdn')->info('Duplicate LFDT records deleted', [
                    'company_id'         => $companyId,
                     'subscriber_id'  => $dup->subscription_id,
                    'subscriber_msisdn'  => $dup->subscriber_msisdn,
                    'transaction_amount' => $dup->transaction_amount,
                    'productId'          => $dup->productId,
                    'kept_record_id'     => $keepId,
                    'deleted_records'    => $deletedCount,
                    'deleted_ids'        => $ids,
                ]);
            }
        }

        $this->info("🎉 Cleanup complete for company_id = {$companyId}");
    }
}
