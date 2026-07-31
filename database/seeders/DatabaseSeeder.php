<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Carga una administradora y cuatro clientes de ejemplo con sus accesos,
     * suficiente para recorrer el sistema completo sin datos reales (README.md §5).
     *
     * Las claves nunca quedan fijas en el código: si no se definen
     * SEEDER_ADMIN_PASSWORD / SEEDER_CLIENTE_PASSWORD en el .env local, se
     * genera una al azar por corrida y se imprime una sola vez en consola.
     */
    public function run(): void
    {
        $this->call(VideoSeeder::class);

        $claveAdmin = env('SEEDER_ADMIN_PASSWORD') ?: Str::password(16);
        $claveClientes = env('SEEDER_CLIENTE_PASSWORD') ?: Str::password(16);

        User::create([
            'name' => 'Catalina Avendaño',
            'email' => 'catalina@catalinaavendanio.com',
            'password' => Hash::make($claveAdmin),
            'rol' => 'admin',
            'activo' => true,
            'email_verified_at' => now(),
        ]);

        $clientes = [
            ['marca' => 'Bloom Skincare', 'contacto' => 'Julieta Ruiz', 'correo' => 'bloom@catalina.test', 'color' => 'rosa'],
            ['marca' => 'Casa Nima', 'contacto' => 'Marcos Peña', 'correo' => 'nima@catalina.test', 'color' => 'ciruela'],
            ['marca' => 'Nube Café', 'contacto' => 'Sol Ibarra', 'correo' => 'nube@catalina.test', 'color' => 'arena'],
            ['marca' => 'Duna Joyas', 'contacto' => 'Vera Lauría', 'correo' => 'duna@catalina.test', 'color' => 'nocturno'],
        ];

        foreach ($clientes as $datos) {
            $cliente = Cliente::create([
                'marca' => $datos['marca'],
                'contacto' => $datos['contacto'],
                'correo_contacto' => $datos['correo'],
                'telefono' => '+54 9 2931 40-3502',
                'color' => $datos['color'],
                'archivado' => false,
            ]);

            User::create([
                'name' => $datos['contacto'],
                'email' => $datos['correo'],
                'password' => Hash::make($claveClientes),
                'rol' => 'cliente',
                'cliente_id' => $cliente->id,
                'activo' => true,
                'email_verified_at' => now(),
            ]);
        }

        $this->call(AgendaYPublicacionesSeeder::class);

        $this->command?->newLine();
        $this->command?->warn('Claves de acceso de esta corrida (no se guardan en ningún lado, anotalas ahora):');
        $this->command?->line("  Administradora (catalina@catalinaavendanio.com): {$claveAdmin}");
        $this->command?->line("  Clientes de ejemplo (mismo correo de cada marca): {$claveClientes}");
        $this->command?->newLine();
    }
}
