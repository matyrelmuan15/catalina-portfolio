<?php

namespace App\Providers;

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
        $this->usarAlmacenamientoLocalSinCredencialesDeR2();
    }

    /**
     * Sin credenciales de Cloudflare R2 (pendiente de la titular, ver AVANCE.md),
     * los discos "r2" y "r2_privado" se sirven desde disco local para poder
     * desarrollar y probar sin la infraestructura real.
     */
    private function usarAlmacenamientoLocalSinCredencialesDeR2(): void
    {
        if (filled(config('filesystems.disks.r2.endpoint'))) {
            return;
        }

        config([
            'filesystems.disks.r2' => [
                'driver' => 'local',
                'root' => storage_path('app/public/r2'),
                'url' => rtrim(config('app.url'), '/').'/storage/r2',
                'visibility' => 'public',
                'throw' => false,
                'report' => false,
            ],
            'filesystems.disks.r2_privado' => [
                'driver' => 'local',
                'root' => storage_path('app/private/r2-privado'),
                'visibility' => 'private',
                'throw' => false,
                'report' => false,
            ],
        ]);
    }
}
