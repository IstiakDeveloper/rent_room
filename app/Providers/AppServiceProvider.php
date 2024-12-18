<?php

namespace App\Providers;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Cleanup old messages
        $threshold = Carbon::now()->subHours(24);
        Message::where('created_at', '<', $threshold)->delete();
    }
}
