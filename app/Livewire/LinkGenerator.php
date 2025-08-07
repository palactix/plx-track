<?php

namespace App\Livewire;

use App\Models\Link;
use App\Models\ClaimableSession;
use App\Services\MetadataFetcher;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LinkGenerator extends Component
{
    #[Validate('required|url|max:2048')]
    public string $longUrl = '';
    
    #[Validate('nullable|string|min:3|max:50|regex:/^[a-zA-Z0-9-_]+$/')]
    public string $customAlias = '';
    
    #[Validate('nullable|string|min:6|max:255')]
    public string $password = '';
    
    #[Validate('nullable|string|max:255')]
    public string $title = '';
    
    #[Validate('nullable|string|max:500')]
    public string $description = '';
    
    #[Validate('nullable|date|after:now')]
    public string $expiresAt = '';
    
    // State properties
    public ?Link $generatedLink = null;
    public bool $showAdvancedOptions = false;
    public string $mode = 'public'; // 'public' or 'authenticated'
    public bool $fetchingMetadata = false;
    
    public function mount()
    {
        $this->mode = Auth::check() ? 'authenticated' : 'public';
    }
    
    public function generateLink()
    {
        $this->validate();
        
        // Check if custom alias is available
        if ($this->customAlias && $this->isAliasUnavailable()) {
            $this->addError('customAlias', 'This custom alias is already taken.');
            return;
        }
        
        // Generate unique short code
        $shortCode = $this->customAlias ?: $this->generateUniqueShortCode();
        
        // Auto-fetch metadata if title/description are empty
        $metadata = $this->getMetadataForLink();
        
        // Create the link
        $linkData = [
            'short_code' => $shortCode,
            'original_url' => $this->longUrl,
            'title' => $this->title ?: $metadata['title'],
            'description' => $this->description ?: $metadata['description'],
            'image' => $metadata['image'],
            'password' => $this->password ? bcrypt($this->password) : null,
            'expires_at' => $this->expiresAt ? $this->expiresAt : null,
            'is_active' => true,
        ];
        
        // Handle user assignment based on mode
        if ($this->mode === 'authenticated') {
            $linkData['user_id'] = Auth::id();
        } else {
            // For public users, use session-based tracking
            $linkData['session_id'] = Session::getId();
            $this->handleClaimableSession();
        }
        
        $this->generatedLink = Link::create($linkData);
        
        // Reset form
        $this->resetForm();
        
        // Dispatch success event
        $this->dispatch('link-generated', [
            'shortUrl' => $this->getShortUrl($this->generatedLink),
            'mode' => $this->mode
        ]);
    }
    
    protected function getMetadataForLink(): array
    {
        // If user has provided title and description, still fetch image
        try {
            $this->fetchingMetadata = true;
            
            $metadataFetcher = new MetadataFetcher();
            $metadata = $metadataFetcher->fetch($this->longUrl);
            
            // If user provided title/description, use their input instead
            if ($this->title) {
                $metadata['title'] = $this->title;
            }
            if ($this->description) {
                $metadata['description'] = $this->description;
            }
            
            return [
                'title' => $metadata['title'] ?? null,
                'description' => $metadata['description'] ?? null,
                'image' => $metadata['image'] ?? null
            ];
            
        } catch (\Exception $e) {
            // If metadata fetching fails, return user input or empty metadata
            Log::warning('Failed to fetch metadata for URL: ' . $this->longUrl, [
                'error' => $e->getMessage()
            ]);
            
            return [
                'title' => $this->title ?? null, 
                'description' => $this->description ?? null,
                'image' => null
            ];
        } finally {
            $this->fetchingMetadata = false;
        }
    }
    
    public function toggleAdvancedOptions()
    {
        $this->showAdvancedOptions = !$this->showAdvancedOptions;
    }
    
    public function resetForm()
    {
        $this->longUrl = '';
        $this->customAlias = '';
        $this->password = '';
        $this->title = '';
        $this->description = '';
        $this->expiresAt = '';
        $this->showAdvancedOptions = false;
        $this->fetchingMetadata = false;
    }
    
    public function createAnother()
    {
        $this->generatedLink = null;
        $this->resetForm();
    }
    
    private function generateUniqueShortCode(): string
    {
        do {
            $shortCode = Str::random(6);
        } while (Link::where('short_code', $shortCode)->exists());
        
        return $shortCode;
    }
    
    private function isAliasUnavailable(): bool
    {
        return Link::where('short_code', $this->customAlias)
            ->orWhere('custom_alias', $this->customAlias)
            ->exists();
    }
    
    private function handleClaimableSession(): void
    {
        $sessionId = Session::getId();
        
        $claimableSession = ClaimableSession::firstOrCreate(
            ['session_id' => $sessionId],
            [
                'links_count' => 0,
                'first_link_created' => now(),
                'last_activity' => now(),
                'metadata' => [
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]
            ]
        );
        
        $claimableSession->increment('links_count');
        $claimableSession->update(['last_activity' => now()]);
    }
    
    private function getShortUrl(Link $link): string
    {
        return config('app.url') . '/' . $link->short_code;
    }
    
    public function getIsPublicModeProperty(): bool
    {
        return $this->mode === 'public';
    }
    
    public function getIsAuthenticatedModeProperty(): bool
    {
        return $this->mode === 'authenticated';
    }
    
    public function getCanShowAdvancedOptionsProperty(): bool
    {
        // Show advanced options for authenticated users or if explicitly toggled
        return $this->isAuthenticatedMode || $this->showAdvancedOptions;
    }
    
    public function render()
    {
        return view('livewire.link-generator');
    }
}
