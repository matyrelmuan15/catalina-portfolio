<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

it('corre contra PostgreSQL', function () {
    expect(DB::connection()->getDriverName())->toBe('pgsql');
});

it('tiene declarada la collation es-AR-x-icu en las columnas que se ordenan', function () {
    $columnas = [
        'clientes' => ['marca', 'contacto'],
        'videos' => ['titulo'],
        'publicaciones' => ['titulo'],
    ];

    foreach ($columnas as $tabla => $nombres) {
        if (! Schema::hasTable($tabla)) {
            continue;
        }

        foreach ($nombres as $columna) {
            $collation = DB::selectOne(
                'select collation_name from information_schema.columns where table_name = ? and column_name = ?',
                [$tabla, $columna]
            );

            expect($collation?->collation_name)
                ->toBe('es-AR-x-icu', "La columna {$tabla}.{$columna} no tiene la collation es-AR-x-icu.");
        }
    }
});
