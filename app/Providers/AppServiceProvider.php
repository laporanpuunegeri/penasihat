<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;  
use Illuminate\Support\Facades\Gate; 
use App\Models\User;                 
use App\Models\Pergerakan;           

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
        // (Jangan buang ini, penting untuk production)
        if (env('APP_ENV') !== 'local') {
            URL::forceScheme('https');
        }

        // --- 2. KOD BARU (GATE PERMISSION) ---
        
        // A. GATE UNTUK CC (PENYOKONG)
        Gate::define('review-cc', function (User $user, Pergerakan $pergerakan) {
            
            $role = strtolower($user->role);
            $bahagianUser = strtoupper($user->bahagian ?? '');
            
            // Admin & Boss boleh buat semua
            if (in_array($role, ['super_admin', 'admin', 'boss'])) {
                return true;
            }

            // Logic untuk CC
            if ($role === 'cc') {
                // PENTING: CC Pentadbiran boleh sokong SEMUA orang
                if (str_contains($bahagianUser, 'PENTADBIRAN')) {
                    return true;
                }

                // Kalau CC bahagian lain, dia cuma boleh sokong staf bahagian dia sendiri
                if ($pergerakan->user) {
                    return $user->bahagian === $pergerakan->user->bahagian;
                }
            }

            return false;
        });

        // B. GATE UNTUK YB (PELULUS)
        Gate::define('review-yb', function (User $user) {
            $role = strtolower($user->role);
            
            // YB atau Boss boleh luluskan
            return in_array($role, ['yb', 'boss', 'super_admin']);
        });
    }
}