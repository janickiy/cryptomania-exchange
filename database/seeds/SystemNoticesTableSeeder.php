<?php

use App\Models\Core\SystemNotice;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SystemNoticesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        SystemNotice::insert([
            [
                'title' => 'Welcome to Cryptomania',
                'description' => 'The exchange platform is ready for trading.',
                'type' => 'success',
                'start_at' => $now,
                'end_at' => $now->copy()->addMonth(),
                'status' => ACTIVE_STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Security reminder',
                'description' => 'Enable two-factor authentication to keep your account protected.',
                'type' => 'info',
                'start_at' => $now,
                'end_at' => $now->copy()->addMonth(),
                'status' => ACTIVE_STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'title' => 'Withdrawal review',
                'description' => 'Large withdrawals may require additional administrative review.',
                'type' => 'warning',
                'start_at' => $now,
                'end_at' => $now->copy()->addMonth(),
                'status' => ACTIVE_STATUS_ACTIVE,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
