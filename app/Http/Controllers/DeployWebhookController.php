<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class DeployWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = env('GITHUB_WEBHOOK_SECRET');
        $signature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($signature, $request->header('X-Hub-Signature-256', ''))) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        $data = json_decode($request->input('payload'), true);
        
        // Only run if pushed branch is "main"
        if (($data['ref'] ?? '') !== 'refs/heads/main') {
            Log::info("Push to non-main branch ignored: " . ($data['ref'] ?? 'unknown'));
            return response('Ignored branch', 200);
        }
        // Run deployment
        Artisan::call('deploy:run');

        return response()->json([
            'status' => 'ok',
            'output' => Artisan::output()
        ]);
    }
}
