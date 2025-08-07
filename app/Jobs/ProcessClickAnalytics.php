<?php

namespace App\Jobs;

use App\Models\Click;
use App\Models\ClickAnalytics;
use App\Models\Link;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Jenssegers\Agent\Agent;
use Torann\GeoIP\Facades\GeoIP;
use Carbon\Carbon;

class ProcessClickAnalytics implements ShouldQueue
{
    use Queueable;

    private Click $click;
    private array $requestData;

    /**
     * Create a new job instance.
     */
    public function __construct(Click $click, array $requestData = [])
    {
        $this->click = $click;
        $this->requestData = $requestData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // First, gather and update all the detailed click data
        $this->updateClickWithDetailedData();
        
        // Then update link counters
        $this->updateLinkCounters();
        
        // Finally, process analytics
        $this->processAnalytics();
    }

    /**
     * Update click record with detailed data gathering
     */
    private function updateClickWithDetailedData(): void
    {
        $agent = new Agent();
        $agent->setUserAgent($this->click->user_agent);
        
        // Get geographical data
        $geoData = $this->getGeoData($this->click->ip_address);
        
        // Get device and browser information
        $deviceType = $this->getDeviceType($agent);
        
        // Get referrer information
        $referrer = $this->getReferrer($this->click->referrer);
        
        // Extract UTM parameters
        $utmParams = $this->extractUtmParameters();
        
        // Update the click record with all detailed data
        $this->click->update([
            'country' => $geoData['country'] ?? null,
            'city' => $geoData['city'] ?? null,
            'region' => $geoData['region'] ?? null,
            'device_type' => $deviceType,
            'device_name' => $agent->device(),
            'browser' => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'platform' => $agent->platform(),
            'platform_version' => $agent->version($agent->platform()),
            'is_mobile' => $agent->isMobile(),
            'is_tablet' => $agent->isTablet(),
            'is_desktop' => $agent->isDesktop(),
            'is_bot' => $agent->isRobot(),
            'referrer' => $referrer,
            'utm_parameters' => $utmParams,
        ]);
    }

    /**
     * Update link counters
     */
    private function updateLinkCounters(): void
    {
        $link = $this->click->link;
        
        // Check if this is a unique click
        $isUnique = $this->isUniqueClick($link, $this->click->ip_address);
        
        // Update link counters
        $link->incrementClicks($isUnique);
    }

    /**
     * Process analytics data
     */
    private function processAnalytics(): void
    {
        $date = $this->click->clicked_at->toDateString();
        
        // Get or create analytics record for the date
        $analytics = ClickAnalytics::firstOrCreate(
            [
                'link_id' => $this->click->link_id,
                'date' => $date,
            ],
            [
                'total_clicks' => 0,
                'unique_clicks' => 0,
                'countries' => [],
                'devices' => [],
                'browsers' => [],
                'platforms' => [],
                'referrers' => [],
                'hourly_distribution' => array_fill(0, 24, 0),
            ]
        );

        // Update analytics
        $this->updateAnalytics($analytics);
    }

    /**
     * Get geographical data from IP
     */
    private function getGeoData(string $ip): array
    {
        try {
            $location = GeoIP::getLocation($ip);
            
            return [
                'country' => $location->iso_code ?? null,
                'city' => $location->city ?? null,
                'region' => $location->state_name ?? null,
                'latitude' => $location->lat ?? null,
                'longitude' => $location->lon ?? null,
            ];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get device type classification
     */
    private function getDeviceType(Agent $agent): string
    {
        if ($agent->isMobile()) {
            return 'mobile';
        } elseif ($agent->isTablet()) {
            return 'tablet';
        } elseif ($agent->isDesktop()) {
            return 'desktop';
        } elseif ($agent->isRobot()) {
            return 'bot';
        }
        
        return 'unknown';
    }

    /**
     * Get referrer information
     */
    private function getReferrer(?string $referrer): string
    {
        if (!$referrer) {
            return 'direct';
        }

        // Parse referrer domain
        $parsedUrl = parse_url($referrer);
        $domain = $parsedUrl['host'] ?? 'unknown';

        // Classify common referrer types
        return $this->classifyReferrer($domain, $referrer);
    }

    /**
     * Classify referrer type
     */
    private function classifyReferrer(string $domain, string $fullReferrer): string
    {
        // Social media platforms
        $socialPlatforms = [
            'facebook.com' => 'Facebook',
            'twitter.com' => 'Twitter',
            'linkedin.com' => 'LinkedIn',
            'instagram.com' => 'Instagram',
            'tiktok.com' => 'TikTok',
            'youtube.com' => 'YouTube',
        ];

        // Search engines
        $searchEngines = [
            'google.com' => 'Google',
            'bing.com' => 'Bing',
            'yahoo.com' => 'Yahoo',
            'duckduckgo.com' => 'DuckDuckGo',
        ];

        foreach ($socialPlatforms as $platform => $name) {
            if (strpos($domain, $platform) !== false) {
                return "Social: $name";
            }
        }

        foreach ($searchEngines as $engine => $name) {
            if (strpos($domain, $engine) !== false) {
                return "Search: $name";
            }
        }

        return $domain;
    }

    /**
     * Extract UTM parameters from request data
     */
    private function extractUtmParameters(): ?array
    {
        $utmParams = [];
        
        foreach ($this->requestData as $key => $value) {
            if (strpos($key, 'utm_') === 0) {
                $utmParams[$key] = $value;
            }
        }

        return empty($utmParams) ? null : $utmParams;
    }

    /**
     * Check if this is a unique click
     */
    private function isUniqueClick(Link $link, string $ip): bool
    {
        // Check if this IP has clicked this link in the last 24 hours
        return !Click::where('link_id', $link->id)
                   ->where('ip_address', $ip)
                   ->where('clicked_at', '>=', now()->subDay())
                   ->where('id', '!=', $this->click->id)
                   ->exists();
    }

    /**
     * Update analytics record with click data
     */
    private function updateAnalytics(ClickAnalytics $analytics): void
    {
        // Increment total clicks
        $analytics->increment('total_clicks');

        // Check if this is a unique click for the day
        $isUnique = !Click::where('link_id', $this->click->link_id)
                          ->where('ip_address', $this->click->ip_address)
                          ->whereDate('clicked_at', $analytics->date)
                          ->where('id', '!=', $this->click->id)
                          ->exists();

        if ($isUnique) {
            $analytics->increment('unique_clicks');
        }

        // Update country data
        if ($this->click->country) {
            $analytics->addCountryClick($this->click->country);
        }

        // Update device data
        if ($this->click->device_type) {
            $analytics->addDeviceClick($this->click->device_type);
        }

        // Update browser data
        if ($this->click->browser) {
            $analytics->addBrowserClick($this->click->browser);
        }

        // Update referrer data
        $referrer = $this->click->referrer ?: 'direct';
        $analytics->addReferrerClick($referrer);

        // Update hourly distribution
        $hour = $this->click->clicked_at->hour;
        $analytics->addHourlyClick($hour);

        // Save all changes
        $analytics->save();
    }
}
