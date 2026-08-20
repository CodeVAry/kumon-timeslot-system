<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin\Guardian;

class NormalizeGuardianContacts extends Command
{
    protected $signature =
        'guardians:normalize-contacts';

    protected $description =
        'Normalize existing guardian email and phone numbers';


    public function handle()
    {
        Guardian::chunkById(
            100,
            function ($guardians) {

                foreach ($guardians as $guardian) {

                    $guardian->normalized_email =
                        Guardian::normalizeEmail(
                            $guardian->email
                        );

                    $guardian->normalized_phone =
                        Guardian::normalizePhone(
                            $guardian->phone
                        );


                    /*
                     * We update only the normalized fields.
                     */
                    $guardian->saveQuietly();
                }
            }
        );


        $this->info(
            'Guardian contacts normalized successfully.'
        );


        return Command::SUCCESS;
    }
}
