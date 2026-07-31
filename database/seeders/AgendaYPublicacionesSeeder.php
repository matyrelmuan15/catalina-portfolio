<?php

namespace Database\Seeders;

use App\Models\AgendaEvento;
use App\Models\Cliente;
use App\Models\Publicacion;
use App\Models\PublicacionMetrica;
use Illuminate\Database\Seeder;

/**
 * Fechas y publicaciones de ejemplo para poder recorrer las fases 5 y 6 sin
 * datos reales, con el mismo criterio que docs/01 §14.4 (el sistema tiene
 * que poder probarse de punta a punta).
 */
class AgendaYPublicacionesSeeder extends Seeder
{
    public function run(): void
    {
        $bloom = Cliente::where('correo_contacto', 'bloom@catalina.test')->first();
        $nima = Cliente::where('correo_contacto', 'nima@catalina.test')->first();

        if (! $bloom || ! $nima) {
            return;
        }

        AgendaEvento::insert([
            ['cliente_id' => $bloom->id, 'fecha' => now()->addDays(4)->toDateString(), 'tipo' => 'grabacion', 'titulo' => 'Sesión de producto', 'nota' => 'Estudio, 10 a 14 h. Traer los tres sérums.', 'created_at' => now(), 'updated_at' => now()],
            ['cliente_id' => $bloom->id, 'fecha' => now()->addDays(8)->toDateString(), 'tipo' => 'entrega', 'titulo' => '3 videos UGC', 'nota' => 'Formato 9:16 con subtítulos.', 'created_at' => now(), 'updated_at' => now()],
            ['cliente_id' => $bloom->id, 'fecha' => now()->addDays(21)->toDateString(), 'tipo' => 'reunion', 'titulo' => 'Revisión de campaña', 'nota' => 'Videollamada de 30 minutos.', 'created_at' => now(), 'updated_at' => now()],
            ['cliente_id' => $nima->id, 'fecha' => now()->addDays(6)->toDateString(), 'tipo' => 'grabacion', 'titulo' => 'Lookbook primavera', 'nota' => 'Locación a definir.', 'created_at' => now(), 'updated_at' => now()],
            ['cliente_id' => $nima->id, 'fecha' => now()->addDays(14)->toDateString(), 'tipo' => 'entrega', 'titulo' => 'Editorial + 4 reels', 'nota' => '', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $publicacionMedida = Publicacion::create([
            'cliente_id' => $bloom->id,
            'titulo' => 'Rutina de mañana',
            'estado' => 'Medida',
            'pilar' => 'Producto',
            'fecha' => now()->subDays(22)->toDateString(),
            'plataforma' => 'instagram',
            'formato' => 'reel',
            'copy_texto' => 'Tres pasos y listo. El sérum de noche se absorbe en segundos.',
            'hashtags' => '#skincare #rutinadenoche #serum',
            'id_media' => '17912345678901234',
            'permalink' => 'https://www.instagram.com/p/C9aBcDeFgH1/',
            'archivo_final_url' => 'https://drive.google.com/file/d/1aBcRutina',
        ]);

        PublicacionMetrica::create([
            'publicacion_id' => $publicacionMedida->id,
            'medido_el' => now()->toDateString(),
            'alcance' => 42000,
            'vistas' => 51300,
            'interacciones' => 3100,
            'me_gusta' => 2410,
            'comentarios' => 118,
            'compartidos' => 342,
            'guardados' => 230,
            'seguidores_al_publicar' => 18420,
        ]);

        Publicacion::create([
            'cliente_id' => $bloom->id,
            'titulo' => 'Reel de lanzamiento',
            'estado' => 'Aprobada',
            'pilar' => 'Promoción',
            'fecha' => now()->addDays(13)->toDateString(),
            'plataforma' => 'instagram',
            'formato' => 'reel',
            'creativo_figma_url' => 'https://figma.com/file/bloom-lanzamiento',
        ]);

        Publicacion::create([
            'cliente_id' => $nima->id,
            'titulo' => 'Detrás de escena',
            'estado' => 'Publicada',
            'pilar' => 'Detrás de escena',
            'fecha' => now()->subDays(5)->toDateString(),
            'plataforma' => 'instagram',
            'formato' => 'historia',
        ]);
    }
}
