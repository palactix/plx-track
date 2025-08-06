<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetadataFetcher
{
    protected int $timeout = 10; // 10 seconds timeout
    protected int $maxSize = 1024 * 1024; // 1MB max content size
    
    public function fetch(string $url): array
    {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return $this->getDefaultMetadata($url);
            }
            
            // Fetch the webpage content
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; PLX-Track Link Shortener; +' . config('app.url') . ')',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])
                ->get($url);
            
            if (!$response->successful()) {
                return $this->getDefaultMetadata($url);
            }
            
            $html = $response->body();
            
            // Limit content size to prevent memory issues
            if (strlen($html) > $this->maxSize) {
                $html = substr($html, 0, $this->maxSize);
            }
            
            return $this->parseMetadata($html, $url);
            
        } catch (\Exception $e) {
            Log::warning('Failed to fetch metadata for URL: ' . $url, [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->getDefaultMetadata($url);
        }
    }
    
    protected function parseMetadata(string $html, string $url): array
    {
        // Create DOMDocument to parse HTML
        $dom = new \DOMDocument();
        
        // Suppress warnings for malformed HTML
        libxml_use_internal_errors(true);
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        
        $xpath = new \DOMXPath($dom);
        
        // Initialize metadata array
        $metadata = [
            'title' => null,
            'description' => null,
            'image' => null,
            'site_name' => null,
            'type' => 'website'
        ];
        
        // Extract title (priority: og:title > twitter:title > title tag)
        $metadata['title'] = 
            $this->getMetaContent($xpath, "meta[@property='og:title']") ??
            $this->getMetaContent($xpath, "meta[@name='twitter:title']") ??
            $this->getTitleTag($xpath) ??
            $this->getDomainFromUrl($url);
        
        // Extract description (priority: og:description > twitter:description > meta description)
        $metadata['description'] = 
            $this->getMetaContent($xpath, "meta[@property='og:description']") ??
            $this->getMetaContent($xpath, "meta[@name='twitter:description']") ??
            $this->getMetaContent($xpath, "meta[@name='description']") ??
            null;
        
        // Extract image (priority: og:image > twitter:image)
        $metadata['image'] = 
            $this->getMetaContent($xpath, "meta[@property='og:image']") ??
            $this->getMetaContent($xpath, "meta[@name='twitter:image']") ??
            null;
        
        // Extract site name
        $metadata['site_name'] = 
            $this->getMetaContent($xpath, "meta[@property='og:site_name']") ??
            $this->getDomainFromUrl($url);
        
        // Extract type
        $metadata['type'] = 
            $this->getMetaContent($xpath, "meta[@property='og:type']") ??
            'website';
        
        // Clean and validate metadata
        return $this->cleanMetadata($metadata, $url);
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
        $baseScheme = $baseParts['scheme'] ?? 'https';
        $baseHost = $baseParts['host'] ?? '';
        
        // Handle protocol-relative URLs
        if (str_starts_with($imageUrl, '//')) {
            return $baseScheme . ':' . $imageUrl;
        }
        
        // Handle absolute paths
        if (str_starts_with($imageUrl, '/')) {
            return $baseScheme . '://' . $baseHost . $imageUrl;
        }
        
        // Handle relative paths (basic implementation)
        $basePath = dirname($baseParts['path'] ?? '/');
        return $baseScheme . '://' . $baseHost . $basePath . '/' . $imageUrl;
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
            'description' => $metadata['description']
        ];
    }
}
