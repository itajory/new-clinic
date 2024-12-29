<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Language::firstOrCreate([
            'name' => 'العربية',
            'short' => 'ar',
            'unicode' => 'ar_EG',
            'dir' => 'rtl',
            'status' => 1,
        ]);
        Language::firstOrCreate([
            'name' => 'English',
            'short' => 'en',
            'unicode' => 'en_US',
            'dir' => 'ltr',
            'status' => 1,
        ]);
        Language::firstOrCreate([
            'name' => 'Hebrew',
            'short' => 'he',
            'unicode' => 'he',
            'dir' => 'rtl',
            'status' => 1,
        ]);
    }
}
