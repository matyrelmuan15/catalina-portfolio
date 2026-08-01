<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

/**
 * Doce piezas de ejemplo para recorrer el portfolio público y el panel de
 * videos sin depender de material real (docs/04, alcance de la Fase 2).
 */
class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $piezas = [
            ['titulo' => 'Rutina de mañana', 'cliente_texto' => 'Bloom Skincare', 'categoria' => 'UGC', 'fecha' => '2026-06-18', 'destacado' => true, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000001', 'descripcion' => 'Tres piezas verticales de 25 segundos para el lanzamiento del sérum. Guion, grabación y edición.'],
            ['titulo' => 'Colección otoño', 'cliente_texto' => 'Casa Nima', 'categoria' => 'Modelaje', 'fecha' => '2026-06-02', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000002', 'descripcion' => 'Campaña de temporada: modelaje y fotografía de producto en locación.'],
            ['titulo' => 'Café de especialidad', 'cliente_texto' => 'Nube Café', 'categoria' => 'Lifestyle', 'fecha' => '2026-05-24', 'destacado' => true, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000003', 'descripcion' => 'Serie lifestyle para Reels con enfoque en textura y sonido ambiente.'],
            ['titulo' => 'Antes y después', 'cliente_texto' => 'Estudio Lume', 'categoria' => 'Marketing', 'fecha' => '2026-05-10', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000004', 'descripcion' => 'Video testimonial editado en dos versiones: orgánico y para pauta en Meta.'],
            ['titulo' => 'Editorial de verano', 'cliente_texto' => 'Sal & Arena', 'categoria' => 'Fotografía', 'fecha' => '2026-04-28', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000005', 'descripcion' => 'Producción fotográfica de 40 tomas más 6 clips verticales de respaldo.'],
            ['titulo' => 'Unboxing accesorios', 'cliente_texto' => 'Duna Joyas', 'categoria' => 'UGC', 'fecha' => '2026-04-12', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000006', 'descripcion' => 'Formato unboxing con locución propia y subtítulos quemados.'],
            ['titulo' => 'Detrás de escena', 'cliente_texto' => 'Casa Nima', 'categoria' => 'Redes', 'fecha' => '2026-03-30', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000007', 'descripcion' => 'Contenido de relleno mensual para sostener el calendario de publicaciones.'],
            ['titulo' => 'Promo día de la madre', 'cliente_texto' => 'Bloom Skincare', 'categoria' => 'Marketing', 'fecha' => '2026-03-08', 'destacado' => false, 'publicado' => false, 'enlace' => 'https://vimeo.com/900000008', 'descripcion' => 'Pieza estacional archivada. Se vuelve a publicar en octubre.'],
            ['titulo' => 'Calendario de contenidos', 'cliente_texto' => 'Nube Café', 'categoria' => 'Redes', 'fecha' => '2026-02-20', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'descripcion' => 'Resumen mensual de publicaciones y resultados para el cliente.'],
            ['titulo' => 'Sesión de producto', 'cliente_texto' => 'Duna Joyas', 'categoria' => 'Fotografía', 'fecha' => '2026-02-05', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000010', 'descripcion' => 'Fotografía de catálogo con luz natural, veinte piezas de joyería.'],
            ['titulo' => 'Campaña cápsula', 'cliente_texto' => 'Casa Nima', 'categoria' => 'Lifestyle', 'fecha' => '2026-01-22', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000011', 'descripcion' => 'Lookbook en video de la cápsula de verano, formato vertical.'],
            ['titulo' => 'Presentación de marca', 'cliente_texto' => 'Estudio Lume', 'categoria' => 'Modelaje', 'fecha' => '2026-01-08', 'destacado' => false, 'publicado' => true, 'enlace' => 'https://vimeo.com/900000012', 'descripcion' => 'Video institucional con modelaje y locución para la home del sitio.'],
        ];

        foreach ($piezas as $orden => $pieza) {
            Video::create($pieza + ['proveedor' => str_contains($pieza['enlace'], 'youtube') ? 'youtube' : 'vimeo', 'orden' => $orden]);
        }
    }
}
