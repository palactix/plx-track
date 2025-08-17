<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DeployWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = env('GITHUB_WEBHOOK_SECRET');
        $signature = 'sha256=' . hash_hmac('sha256', $request->getContent(), $secret);

        if (!hash_equals($signature, $request->header('X-Hub-Signature-256', ''))) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Run deployment
        Artisan::call('deploy:run');

        return response()->json([
            'status' => 'ok',
            'output' => Artisan::output()
        ]);
    }
}
