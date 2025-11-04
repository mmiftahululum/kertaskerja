<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View; // Tambahkan ini di bagian atas

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
        // Pastikan ini hanya dijalankan jika user sudah login
        View::composer('layouts.navigation', function ($view) {
            if (auth()->check()) {
                $bookmarks = auth()->user()->taskFilterBookmarks()
                                ->orderBy('name')
                                ->get();

                $view->with('bookmarks', $bookmarks);
            } else {
                // Sediakan koleksi kosong jika user belum login
                $view->with('bookmarks', collect());
            }
        });

        

    }
}
