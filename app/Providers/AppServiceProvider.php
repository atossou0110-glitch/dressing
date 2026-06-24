<?php

namespace App\Providers;

use App\Models\Product;
use App\Support\CartManager;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Throwable;

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
        $this->removeStaleViteHotFile();

        Route::bind('product', fn (string $value) => Product::resolveCatalogProduct($value) ?? abort(404));

        View::composer('*', function ($view): void {
            $cartItemCount = 0;

            try {
                if (request()->hasSession()) {
                    $cartItemCount = app(CartManager::class)->count();
                }
            } catch (Throwable) {
                $cartItemCount = 0;
            }

            $view->with('cartItemCount', $cartItemCount);
        });
    }

    /**
     * Fall back to the compiled Vite build when the hot file points to a dead dev server.
     */
    private function removeStaleViteHotFile(): void
    {
        $hotFile = public_path('hot');

        if (! is_file($hotFile)) {
            return;
        }

        $hotUrl = trim((string) file_get_contents($hotFile));

        if ($hotUrl === '') {
            @unlink($hotFile);

            return;
        }

        $host = parse_url($hotUrl, PHP_URL_HOST);
        $port = parse_url($hotUrl, PHP_URL_PORT);

        if (! is_string($host) || ! is_int($port) || $this->isTcpPortReachable($host, $port)) {
            return;
        }

        @unlink($hotFile);
    }

    private function isTcpPortReachable(string $host, int $port): bool
    {
        $connection = @fsockopen($host, $port, $errorCode, $errorMessage, 0.2);

        if ($connection === false) {
            return false;
        }

        fclose($connection);

        return true;
    }
}
