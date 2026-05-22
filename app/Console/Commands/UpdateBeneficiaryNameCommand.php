<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\BeneficiaryNameUpdateLog;

class UpdateBeneficiaryNameCommand extends Command
{
    protected $signature = 'beneficiary:update';

    protected $description = 'Update beneficiary names from CSV file';

    public function handle()
    {
        $filePath = storage_path('app/beneficiary_update.csv');

        if (!file_exists($filePath)) {

            $this->error("CSV file not found:");
            $this->error($filePath);

            return;
        }

        $this->info("======================================");
        $this->info("Beneficiary Update Process Started");
        $this->info("======================================");

        $handle = fopen($filePath, 'r');

        if (!$handle) {

            $this->error('Unable to open CSV file.');

            return;
        }

        // header skip
        fgetcsv($handle);

        $chunkSize = 500;

        $rows = [];

        $totalProcessed = 0;

        while (($row = fgetcsv($handle)) !== false) {

            if (count($row) < 3) {
                continue;
            }

            $rows[] = $row;

            if (count($rows) >= $chunkSize) {

                $this->processChunk($rows);

                $totalProcessed += count($rows);

                $this->line("Processed Rows: {$totalProcessed}");

                $rows = [];
            }
        }

        // remaining rows
        if (!empty($rows)) {

            $this->processChunk($rows);

            $totalProcessed += count($rows);
        }

        fclose($handle);

        $this->info("======================================");
        $this->info("Process Completed");
        $this->info("Total Processed: {$totalProcessed}");
        $this->info("======================================");
    }

    private function processChunk($rows)
    {
        $requests = [];

        foreach ($rows as $row) {

            $msisdn          = trim($row[0]);
            $beneficiaryName = trim($row[1]);
            $subscriberCnic  = trim($row[2]);

            $formattedNumber = $this->formatMsisdn($msisdn);

            // already updated check
            $alreadyUpdated = BeneficiaryNameUpdateLog::where(
                'subscriber_msisdn',
                $formattedNumber
            )->exists();

            if ($alreadyUpdated) {

                $this->warn("SKIPPED => {$formattedNumber}");

                continue;
            }

            $requests[] = [
                'subscriber_msisdn' => $formattedNumber,
                'beneficiary_name'  => $beneficiaryName,
                'subscriber_cnic'   => $subscriberCnic,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 20 Parallel Requests
        |--------------------------------------------------------------------------
        */

        $requestChunks = array_chunk($requests, 20);

        foreach ($requestChunks as $batch) {

            try {

                $responses = Http::pool(function ($pool) use ($batch) {

                    $poolRequests = [];

                    foreach ($batch as $data) {

                        $poolRequests[$data['subscriber_msisdn']] =
                            $pool->as($data['subscriber_msisdn'])
                                ->timeout(30)
                                ->post(
                                    'https://jazzcash-ips.efulife.com/mgmt/public/api/updateBeneficiaryName',
                                    $data
                                );
                    }

                    return $poolRequests;
                });

                foreach ($batch as $data) {

                    $number = $data['subscriber_msisdn'];

                    try {

                        $response = $responses[$number];

                        if ($response->successful()) {

                           BeneficiaryNameUpdateLog::updateOrCreate(
    [
        'subscriber_msisdn' => $number
    ],
    [
        'subscriber_cnic'  => $data['subscriber_cnic'],
        'beneficiary_name' => $data['beneficiary_name'],
        'status'           => 'success',
        'api_response'     => $response->body(),
    ]
);

                            $this->info("SUCCESS => {$number}");

                        } else {

                          BeneficiaryNameUpdateLog::updateOrCreate(
    [
        'subscriber_msisdn' => $number
    ],
    [
        'subscriber_cnic'  => $data['subscriber_cnic'],
        'beneficiary_name' => $data['beneficiary_name'],
        'status'           => 'success',
        'api_response'     => $response->body(),
    ]
);

                            $this->error("FAILED => {$number}");
                        }

                    } catch (\Exception $e) {

                        BeneficiaryNameUpdateLog::create([
                            'subscriber_msisdn' => $number,
                            'subscriber_cnic'   => $data['subscriber_cnic'],
                            'beneficiary_name'  => $data['beneficiary_name'],
                            'status'            => 'failed',
                            'api_response'      => $e->getMessage(),
                        ]);

                        $this->error("ERROR => {$number}");
                    }
                }

                // 1 second pause after every 20 requests
                sleep(1);

            } catch (\Exception $e) {

                $this->error("Batch Error => " . $e->getMessage());
            }
        }
    }

    private function formatMsisdn($number)
    {
        // remove spaces
        $number = preg_replace('/\s+/', '', $number);

        // keep digits only
        $number = preg_replace('/[^0-9]/', '', $number);

        /*
            INPUTS:

            923008758478
            3008758478
            03008758478

            OUTPUT:
            03008758478
        */

        // remove 92
        if (substr($number, 0, 2) == '92') {

            $number = substr($number, 2);
        }

        // add 0 if missing
        if (substr($number, 0, 1) != '0') {

            $number = '0' . $number;
        }

        // keep only 11 digits
        $number = substr($number, 0, 11);

        return $number;
    }
}
