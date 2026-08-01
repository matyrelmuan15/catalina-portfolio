<?php

namespace Database\Seeders;

use App\Enums\RolUsuario;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Carga la administradora y cuatro clientes de ejemplo con su acceso al portal.
 * Credenciales de prueba, pensadas solo para recorrer el sistema en local y en
 * staging — no se usan en producción.
 */
class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Catalina Avendaño',
            'email' => 'catalinaavendanio@gmail.com',
            'password' => Hash::make('AdminCatalina2026'),
        ]);

        $clientes = [
            [
                'marca' => 'Bloom Skincare',
                'contacto' => 'Julieta Ruiz',
                'correo_contacto' => 'julieta@bloomskincare.com.ar',
                'telefono' => '+54 9 2931 40-1101',
                'color' => 'rosa',
            ],
            [
                'marca' => 'Casa Nima',
                'contacto' => 'Marcos Peña',
                'correo_contacto' => 'marcos@casanima.com.ar',
                'telefono' => '+54 9 2931 40-1102',
                'color' => 'ciruela',
            ],
            [
                'marca' => 'Nube Café',
                'contacto' => 'Sol Ibarra',
                'correo_contacto' => 'sol@nubecafe.com.ar',
                'telefono' => '+54 9 2931 40-1103',
                'color' => 'arena',
            ],
            [
                'marca' => 'Duna Joyas',
                'contacto' => 'Vera Lauría',
                'correo_contacto' => 'vera@dunajoyas.com.ar',
                'telefono' => '+54 9 2931 40-1104',
                'color' => 'nocturno',
            ],
        ];

        foreach ($clientes as $datos) {
            $cliente = Cliente::create($datos + ['archivado' => false]);

            User::factory()->create([
                'name' => $datos['contacto'],
                'email' => $datos['correo_contacto'],
                'password' => Hash::make('ClientePrueba2026'),
                'rol' => RolUsuario::Cliente,
                'cliente_id' => $cliente->id,
            ]);
        }
    }
}
