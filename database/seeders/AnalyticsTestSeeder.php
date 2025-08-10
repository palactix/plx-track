<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Models\Click;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AnalyticsTestSeeder extends Seeder
{
    public function run()
    {
        // Get the first user's first link for testing
        $link = Link::first();
        
        if (!$link) {
            $this->command->error('No links found. Please create a link first.');
            return;
        }

        $countries = ['US', 'CA', 'GB', 'DE', 'FR', 'JP', 'AU', 'BR'];
        $devices = ['desktop', 'mobile', 'tablet'];
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];
        $referrers = [
            null, // Direct
            'https://google.com',
            'https://facebook.com',
            'https://twitter.com',
            'https://linkedin.com',
            'https://reddit.com'
        ];

        // Generate clicks for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i);
            
            // Random number of clicks per day (0-20)
            $clicksCount = rand(0, 20);
            
            for ($j = 0; $j < $clicksCount; $j++) {
                // Random time within the day
                $clickTime = $date->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                
                Click::create([
                    'link_id' => $link->id,
                    'session_id' => 'session_' . rand(100000, 999999),
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'referrer' => $referrers[array_rand($referrers)],
                    'country' => $countries[array_rand($countries)],
                    'city' => 'Test City',
                    'region' => 'Test Region',
                    'device_type' => $devices[array_rand($devices)],
                    'device_name' => 'Test Device',
                    'browser' => $browsers[array_rand($browsers)],
                    'browser_version' => '120.0',
                    'platform' => 'Test Platform',
                    'platform_version' => '10.0',
                    'is_mobile' => rand(0, 1),
                    'is_tablet' => rand(0, 1),
                    'is_desktop' => rand(0, 1),
                    'is_bot' => 0,
                    'utm_parameters' => null,
                    'clicked_at' => $clickTime,
                    'created_at' => $clickTime,
                    'updated_at' => $clickTime,
                ]);
            }
        }

        $totalClicks = Click::where('link_id', $link->id)->count();
        $this->command->info("Generated {$totalClicks} test clicks for link: {$link->title}");
    }

    private function generateRandomIP()
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }

    private function generateRandomUserAgent()
    {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1'
        ];
        
        return $agents[array_rand($agents)];
    }
}
