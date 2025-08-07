<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link Preview - {{ $link->title ?: $link->original_url }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Header -->
            <div class="border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">Link Preview</h1>
                    <a href="{{ $link->short_url }}" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200">
                        Visit Link
                    </a>
                </div>
            </div>

            <!-- Link Information -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div>
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Destination</h2>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <p class="text-sm text-gray-600 break-all">{{ $link->original_url }}</p>
                            </div>
                        </div>

                        @if($link->title)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Title</h2>
                            <p class="text-gray-700">{{ $link->title }}</p>
                        </div>
                        @endif

                        @if($link->description)
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Description</h2>
                            <p class="text-gray-700">{{ $link->description }}</p>
                        </div>
                        @endif

                        <div class="mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 mb-2">Short URL</h2>
                            <div class="bg-gray-50 p-4 rounded-md flex items-center justify-between">
                                <span class="text-sm text-gray-600">{{ $link->short_url }}</span>
                                <button onclick="copyToClipboard('{{ $link->short_url }}')" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    Copy
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Analytics -->
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Stats</h2>
                        
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-50 p-4 rounded-md">
                                <div class="text-2xl font-bold text-blue-600">{{ $analytics['clicks_last_24h'] ?? 0 }}</div>
                                <div class="text-sm text-blue-600">Clicks (24h)</div>
                            </div>
                            <div class="bg-green-50 p-4 rounded-md">
                                <div class="text-2xl font-bold text-green-600">{{ $analytics['unique_visitors_24h'] ?? 0 }}</div>
                                <div class="text-sm text-green-600">Unique Visitors</div>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-md">
                                <div class="text-2xl font-bold text-purple-600">{{ $analytics['clicks_last_1h'] ?? 0 }}</div>
                                <div class="text-sm text-purple-600">Last Hour</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="text-2xl font-bold text-gray-600">{{ $link->clicks_count }}</div>
                                <div class="text-sm text-gray-600">Total Clicks</div>
                            </div>
                        </div>

                        @if(isset($analytics['latest_clicks']) && count($analytics['latest_clicks']) > 0)
                        <div>
                            <h3 class="text-md font-semibold text-gray-900 mb-3">Recent Activity</h3>
                            <div class="space-y-2">
                                @foreach($analytics['latest_clicks'] as $click)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 bg-green-400 rounded-full"></span>
                                        <span class="text-gray-600">{{ $click['country'] ?? 'Unknown' }}</span>
                                        <span class="text-gray-400">•</span>
                                        <span class="text-gray-600">{{ $click['device'] ?? 'Unknown' }}</span>
                                    </div>
                                    <span class="text-gray-500">{{ $click['time'] }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Warning if password protected -->
                @if($link->isPasswordProtected())
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-yellow-700">This link is password protected. You will be asked for a password when you visit it.</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex justify-center space-x-4">
            <a href="{{ $link->short_url }}" class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-200 font-medium">
                Visit This Link
            </a>
            <a href="{{ route('home') }}" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-md hover:bg-gray-300 transition duration-200 font-medium">
                Create Your Own
            </a>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // You could add a toast notification here
                alert('Link copied to clipboard!');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</body>
</html>
