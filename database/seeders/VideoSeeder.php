<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

/**
 * Doce piezas de ejemplo, con los mismos datos de referencia del prototipo
 * (mockup/portfolio-mockup.html), para poder mostrar la grilla completa
 * sin depender todavía de los textos y fotos reales (pendiente #4 de AVANCE.md).
 */
class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $piezas = [
            ['titulo' => 'Rutina de mañana', 'cliente_texto' => 'Bloom Skincare', 'categoria' => 'UGC', 'fecha' => '2026-06-18', 'destacado' => true, 'descripcion' => 'Tres piezas verticales de 25 segundos para el lanzamiento del sérum. Guion, grabación y edición.'],
            ['titulo' => 'Colección otoño', 'cliente_texto' => 'Casa Nima', 'categoria' => 'Modelaje', 'fecha' => '2026-06-02', 'destacado' => false, 'descripcion' => 'Campaña de temporada: modelaje y fotografía de producto en locación.'],
            ['titulo' => 'Café de especialidad', 'cliente_texto' => 'Nube Café', 'categoria' => 'Lifestyle', 'fecha' => '2026-05-24', 'destacado' => true, 'descripcion' => 'Serie lifestyle para Reels con enfoque en textura y sonido ambiente.'],
            ['titulo' => 'Antes y después', 'cliente_texto' => 'Estudio Lume', 'categoria' => 'Marketing', 'fecha' => '2026-05-10', 'destacado' => false, 'descripcion' => 'Video testimonial editado en dos versiones: orgánico y para pauta en Meta.'],
            ['titulo' => 'Editorial de verano', 'cliente_texto' => 'Sal & Arena', 'categoria' => 'Fotografía', 'fecha' => '2026-04-28', 'destacado' => false, 'descripcion' => 'Producción fotográfica de 40 tomas más 6 clips verticales de respaldo.'],
            ['titulo' => 'Unboxing accesorios', 'cliente_texto' => 'Duna Joyas', 'categoria' => 'UGC', 'fecha' => '2026-04-12', 'destacado' => false, 'descripcion' => 'Formato unboxing con locución propia y subtítulos quemados.'],
            ['titulo' => 'Detrás de escena', 'cliente_texto' => 'Casa Nima', 'categoria' => 'Redes', 'fecha' => '2026-03-30', 'destacado' => false, 'descripcion' => 'Contenido de relleno mensual para sostener el calendario de publicaciones.'],
            ['titulo' => 'Promo día de la madre', 'cliente_texto' => 'Bloom Skincare', 'categoria' => 'Marketing', 'fecha' => '2026-03-08', 'destacado' => false, 'descripcion' => 'Pieza estacional archivada. Se vuelve a publicar en octubre.', 'publicado' => false],
            ['titulo' => 'Lookbook primavera', 'cliente_texto' => 'Casa Nima', 'categoria' => 'Modelaje', 'fecha' => '2026-02-20', 'destacado' => false, 'descripcion' => 'Editorial de indumentaria con seis modelos en locación exterior.'],
            ['titulo' => 'Textura del sérum', 'cliente_texto' => 'Bloom Skincare', 'categoria' => 'UGC', 'fecha' => '2026-02-05', 'destacado' => false, 'descripcion' => 'Macro de producto con locución en primera persona.'],
            ['titulo' => 'Recorrido de marca', 'cliente_texto' => 'Nube Café', 'categoria' => 'Redes', 'fecha' => '2026-01-22', 'destacado' => false, 'descripcion' => 'Serie de historias mostrando el local y el equipo de trabajo.'],
            ['titulo' => 'Campaña Google Ads', 'cliente_texto' => 'Duna Joyas', 'categoria' => 'Marketing', 'fecha' => '2026-01-10', 'destacado' => false, 'descripcion' => 'Set de piezas verticales pensadas para pauta paga en Performance Max.'],
        ];

        foreach ($piezas as $indice => $datos) {
            Video::create([
                'titulo' => $datos['titulo'],
                'cliente_texto' => $datos['cliente_texto'],
                'categoria' => $datos['categoria'],
                'fecha' => $datos['fecha'],
                'proveedor' => 'vimeo',
                'enlace' => 'https://vimeo.com/'.(100000000 + $indice),
                'descripcion' => $datos['descripcion'],
                'publicado' => $datos['publicado'] ?? true,
                'destacado' => $datos['destacado'],
                'orden' => $indice,
            ]);
        }
    }
}
