<?php
use App\Http\Controllers\DeployWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/deploy-webhook', [DeployWebhookController::class, 'handle']);
