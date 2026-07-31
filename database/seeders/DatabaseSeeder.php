<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Carga una administradora y cuatro clientes de ejemplo con sus accesos,
     * suficiente para recorrer el sistema completo sin datos reales (README.md §5).
     */
    public function run(): void
    {
        User::create([
            'name' => 'Catalina Avendaño',
            'email' => 'catalina@catalinaavendanio.com',
            'password' => Hash::make('admin-catalina-2026'),
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
                'password' => Hash::make('cliente-portal-2026'),
                'rol' => 'cliente',
                'cliente_id' => $cliente->id,
                'activo' => true,
                'email_verified_at' => now(),
            ]);
        }
    }
}
