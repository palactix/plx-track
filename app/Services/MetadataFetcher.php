<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MetadataFetcher
{
    /**
     * @var int seconds
     */
    protected int $timeout;
    /**
     * @var int bytes
     */
    protected int $maxSize;
    /** @var int|null cache ttl seconds */
    protected ?int $cacheTtl;

    public function __construct(?int $timeout = null, ?int $maxSize = null, ?int $cacheTtl = null)
    {
        $this->timeout = $timeout ?? 10; // seconds
        $this->maxSize = $maxSize ?? (1024 * 1024); // 1MB
        $this->cacheTtl = $cacheTtl ?? 3600; // null disables caching
    }

    /**
     * Fetch and parse metadata from a URL with safeguards.
     *
     * @return array{title:?string,description:?string,image:?string,site_name:?string,type:string,fetched:bool,truncated:bool,error:?string,raw_content_type:?string}
     */
    public function fetch(string $url): array
    {
        // Return cached if available
        $cacheKey = 'metadata:' . md5($url);
        if ($this->cacheTtl && Cache::has($cacheKey)) {
           # dd($cacheKey);
          #  return Cache::get($cacheKey);
        }

        $default = $this->decorateDefault($this->getDefaultMetadata($url));

        try {
            // Validate URL format
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->cacheAndReturn($cacheKey, $default);
            }

            // SSRF guard (basic) – resolve host and reject private/reserved IPs
            if (!$this->isPublicHttpUrl($url)) {
                $default['error'] = 'private_or_invalid_host';
                return $this->cacheAndReturn($cacheKey, $default);
            }

            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; PLX-Track Link Shortener; +' . config('app.url') . ')',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])
                ->get($url);
            
            $status = $response->status();
            $default['raw_content_type'] = $response->header('Content-Type');

            // Rate limit handling
            if ($status === 429) {
                $default['error'] = 'rate_limited';
                return $this->cacheAndReturn($cacheKey, $default);
            }

            if (!$response->successful()) {
                $default['error'] = 'http_status_' . $status;
                return $this->cacheAndReturn($cacheKey, $default);
            }

            // Content-Type check
            $contentType = strtolower((string) $response->header('Content-Type'));
            if (!str_starts_with($contentType, 'text/html')) {
                $default['error'] = 'non_html_content';
                return $this->cacheAndReturn($cacheKey, $default);
            }

            $html = $response->body();
            
            $truncated = false;
            if (strlen($html) > $this->maxSize) {
                $safeSlice = substr($html, 0, $this->maxSize);
                // Try to cut at last '>' to reduce broken tags
                $lastTagEnd = strrpos($safeSlice, '>');
                if ($lastTagEnd !== false && $lastTagEnd > $this->maxSize * 0.8) {
                    $safeSlice = substr($safeSlice, 0, $lastTagEnd + 1);
                }
                $html = $safeSlice;
                $truncated = true;
            }

            // Attempt charset detection (basic)
            $encoding = null;
            if (preg_match('/charset=([^;\s]+)/i', $contentType, $m)) {
                $encoding = trim($m[1], "\"' ");
            } elseif (preg_match('/<meta[^>]+charset=["\']?([^"\'>\s]+)/i', $html, $m)) {
                $encoding = $m[1];
            }
            if ($encoding && !Str::of(strtolower($encoding))->startsWith('utf-8')) {
                $converted = @mb_convert_encoding($html, 'UTF-8', $encoding);
                if ($converted !== false) {
                    $html = $converted;
                }
            }

            $parsed = $this->parseMetadata($html, $url);
           
            $parsed['fetched'] = true;
            $parsed['truncated'] = $truncated;
            $parsed['error'] = null;
            $parsed['raw_content_type'] = $contentType;

            return $this->cacheAndReturn($cacheKey, $parsed);

        } catch (\Throwable $e) {
            Log::warning('Metadata fetch error', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            $default['error'] = 'exception';
            return $this->cacheAndReturn($cacheKey, $default);
        }
    }
    
    protected function parseMetadata(string $html, string $url): array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $xpath = new \DOMXPath($dom);

        $baseHref = $this->getBaseHref($xpath);

        $pick = fn(array $candidates) => collect($candidates)->first(fn($c) => !is_null($c));

        $titles = [
            $this->getMetaContent($xpath, "meta[@property='og:title']"),
            $this->getMetaContent($xpath, "meta[@name='twitter:title']"),
            $this->getTitleTag($xpath),
            $this->getDomainFromUrl($url),
        ];
       
        $title = $pick($titles);

        $description = $pick([
            $this->getMetaContent($xpath, "meta[@property='og:description']"),
            $this->getMetaContent($xpath, "meta[@name='twitter:description']"),
            $this->getMetaContent($xpath, "meta[@name='description']"),
            null,
        ]);

        $image = $pick([
            $this->getMetaContent($xpath, "meta[@property='og:image']"),
            $this->getMetaContent($xpath, "meta[@name='twitter:image']"),
            $this->getMetaContent($xpath, "meta[@name='twitter:image:src']"),
            $this->getLinkHref($xpath, "link[@rel='apple-touch-icon']"),
            $this->getLinkHref($xpath, "link[@rel='icon']"),
            null,
        ]);

        $siteName = $pick([
            $this->getMetaContent($xpath, "meta[@property='og:site_name']"),
            $this->getDomainFromUrl($url),
        ]);

        $type = $pick([
            $this->getMetaContent($xpath, "meta[@property='og:type']"),
            'website',
        ]);

        $metadata = $this->cleanMetadata([
            'title' => $title,
            'description' => $description,
            'image' => $image ? $this->resolveUrl($image, $baseHref ?: $url) : null,
            'site_name' => $siteName,
            'type' => $type,
        ], $url);

        // Decorate with defaults
        return $this->decorateDefault($metadata);
    }
    
    protected function getMetaContent(\DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query("//{$query}");
        if ($nodes && $nodes->length > 0) {
            $node = $nodes->item(0);
            if ($node instanceof \DOMElement) {
                $content = $node->getAttribute('content');
                return trim($content) ?: null;
            }
        }
        return null;
    }
    
    protected function getLinkHref(\DOMXPath $xpath, string $query): ?string
    {
        $nodes = $xpath->query("//{$query}");
        if ($nodes && $nodes->length > 0) {
            $node = $nodes->item(0);
            if ($node instanceof \DOMElement) {
                $href = $node->getAttribute('href');
                return trim($href) ?: null;
            }
        }
        return null;
    }
    
    protected function getTitleTag(\DOMXPath $xpath): ?string
    {
        $nodes = $xpath->query('//title');
        if ($nodes && $nodes->length > 0) {
            $node = $nodes->item(0);
            if ($node) {
                $title = trim($node->textContent);
                return $title ?: null;
            }
        }
        return null;
    }
    
    protected function cleanMetadata(array $metadata, string $url): array
    {
        // Clean title
        if ($metadata['title']) {
            $metadata['title'] = html_entity_decode(strip_tags($metadata['title']));
            $metadata['title'] = preg_replace('/\s+/', ' ', $metadata['title']);
            $metadata['title'] = substr(trim($metadata['title']), 0, 255); // Limit to 255 chars
        }
        
        // Clean description
        if ($metadata['description']) {
            $metadata['description'] = html_entity_decode(strip_tags($metadata['description']));
            $metadata['description'] = preg_replace('/\s+/', ' ', $metadata['description']);
            $metadata['description'] = substr(trim($metadata['description']), 0, 500); // Limit to 500 chars
        }
        
        // Validate and clean image URL
        if ($metadata['image']) {
            $metadata['image'] = $this->resolveUrl($metadata['image'], $url);
            if (!filter_var($metadata['image'], FILTER_VALIDATE_URL)) {
                $metadata['image'] = null;
            }
        }
        
        return $metadata;
    }
    
    protected function resolveUrl(string $imageUrl, string $baseUrl): string
    {
        // If it's already a full URL, return as is
        if (parse_url($imageUrl, PHP_URL_SCHEME)) {
            return $imageUrl;
        }
        
        // Parse base URL
        $baseParts = parse_url($baseUrl);
        if (!$baseParts || !isset($baseParts['host'])) {
            return $imageUrl; // Return original if base URL is invalid
        }
        
        $baseScheme = $baseParts['scheme'] ?? 'https';
        $baseHost = $baseParts['host'];
        
        // Handle protocol-relative URLs
        if (str_starts_with($imageUrl, '//')) {
            return $baseScheme . ':' . $imageUrl;
        }
        
        // Handle absolute paths
        if (str_starts_with($imageUrl, '/')) {
            return $baseScheme . '://' . $baseHost . $imageUrl;
        }
        
        // Handle relative paths
        $basePath = dirname($baseParts['path'] ?? '/');
        if ($basePath === '.') $basePath = '';
        return $baseScheme . '://' . $baseHost . rtrim($basePath, '/') . '/' . ltrim($imageUrl, '/');
    }
    
    protected function getDomainFromUrl(string $url): string
    {
        $parsed = parse_url($url);
        return isset($parsed['host']) ? $parsed['host'] : 'Unknown';
    }
    
    protected function getDefaultMetadata(string $url): array
    {
        return [
            'title' => $this->getDomainFromUrl($url),
            'description' => null,
            'image' => null,
            'site_name' => $this->getDomainFromUrl($url),
            'type' => 'website'
        ];
    }
    
    /**
     * Quick method to get just title and description
     */
    public function getTitleAndDescription(string $url): array
    {
        $metadata = $this->fetch($url);
        return [
            'title' => $metadata['title'],
            'description' => $metadata['description'],
            'fetched' => $metadata['fetched'],
            'error' => $metadata['error']
        ];
    }

    protected function isPublicHttpUrl(string $url): bool
    {
        $parts = parse_url($url);
        if (!$parts || !isset($parts['scheme'], $parts['host'])) return false;
        if (!in_array(strtolower($parts['scheme']), ['http','https'])) return false;
        $host = $parts['host'];
        // Skip IP check for localhost style hosts
        $ips = @gethostbynamel($host) ?: [];
        foreach ($ips as $ip) {
            if (!$this->isPublicIp($ip)) return false;
        }
        return true;
    }

    protected function isPublicIp(string $ip): bool
    {
        if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;
        // FILTER_FLAG_NO_PRIV_RANGE | NO_RES_RANGE ensures public
        return (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }

    protected function cacheAndReturn(string $key, array $data): array
    {
        if ($this->cacheTtl) {
            Cache::put($key, $data, $this->cacheTtl);
        }
        return $data;
    }

    protected function decorateDefault(array $data): array
    {
        return array_merge([
            'fetched' => false,
            'truncated' => false,
            'error' => null,
            'raw_content_type' => null,
        ], $data);
    }

    protected function getBaseHref(\DOMXPath $xpath): ?string
    {
        $nodes = $xpath->query("//base[@href]");
        if ($nodes && $nodes->length > 0) {
            /** @var \DOMElement $el */
            $el = $nodes->item(0);
            $href = trim($el->getAttribute('href'));
            return $href ?: null;
        }
        return null;
    }
}
