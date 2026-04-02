<?php

namespace Database\Seeders;

use App\Models\FinancialRecord;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialRecordSeeder extends Seeder
{
    private array $categories = [
        'salary',
        'rent',
        'food',
        'investment',
        'utilities',
        'transport',
        'healthcare',
        'education',
        'entertainment',
        'freelance',
    ];

    // Realistic income/expense pairings per category
    private array $categoryTypes = [
        'salary' => 'income',
        'freelance' => 'income',
        'investment' => 'income',
        'rent' => 'expense',
        'food' => 'expense',
        'utilities' => 'expense',
        'transport' => 'expense',
        'healthcare' => 'expense',
        'education' => 'expense',
        'entertainment' => 'expense',
    ];

    private array $notes = [
        'salary' => ['Monthly salary payment', 'Bonus included', 'Base salary', 'Annual increment applied'],
        'freelance' => ['Client project payment', 'Consulting fees', 'Design work', 'Development contract'],
        'investment' => ['Dividend received', 'Stock sale profit', 'Mutual fund return', 'Interest income'],
        'rent' => ['Monthly office rent', 'Apartment rent', 'Storage unit', 'Parking space'],
        'food' => ['Grocery shopping', 'Team lunch', 'Restaurant dinner', 'Office snacks'],
        'utilities' => ['Electricity bill', 'Water bill', 'Internet bill', 'Gas bill'],
        'transport' => ['Fuel costs', 'Metro pass', 'Cab rides', 'Vehicle maintenance'],
        'healthcare' => ['Doctor consultation', 'Pharmacy purchase', 'Lab tests', 'Health insurance'],
        'education' => ['Online course subscription', 'Books purchase', 'Workshop fee', 'Certification exam'],
        'entertainment' => ['Streaming subscription', 'Movie tickets', 'Sports event', 'Weekend outing'],
    ];

    private array $amountRanges = [
        'salary' => [50000, 150000],
        'freelance' => [10000, 80000],
        'investment' => [5000, 50000],
        'rent' => [8000, 30000],
        'food' => [500, 5000],
        'utilities' => [1000, 8000],
        'transport' => [500, 5000],
        'healthcare' => [500, 15000],
        'education' => [2000, 20000],
        'entertainment' => [500, 5000],
    ];

    public function run(): void
    {
        $admin = User::where('email', 'admin@finance.test')->first();
        $analyst = User::where('email', 'analyst@finance.test')->first();

        if (!$admin || !$analyst) {
            $this->command->warn('Run UserSeeder first. Skipping FinancialRecordSeeder.');
            return;
        }

        DB::table('financial_records')->truncate();

        $records = [];

        // Generate 40 realistic records spread over the last 6 months
        for ($i = 0; $i < 40; $i++) {
            $category = $this->categories[$i % count($this->categories)];
            $type = $this->categoryTypes[$category];

            [$min, $max] = $this->amountRanges[$category];
            $amount = round(rand($min * 100, $max * 100) / 100, 2);

            $notesPool = $this->notes[$category];
            $note = $notesPool[array_rand($notesPool)];

            // Spread dates across the last 6 months
            $daysAgo = rand(0, 180);
            $date = Carbon::today()->subDays($daysAgo)->format('Y-m-d');

            // Alternate ownership between admin and analyst
            $userId = $i % 3 === 0 ? $admin->id : $analyst->id;

            $records[] = [
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $type,
                'category' => $category,
                'date' => $date,
                'notes' => $note,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        FinancialRecord::insert($records);

        $this->command->info('40 financial records seeded across the last 6 months.');
    }
}