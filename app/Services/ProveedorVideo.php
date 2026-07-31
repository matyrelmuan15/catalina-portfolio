<?php

namespace App\Services;

/**
 * Resuelve identificador, enlace embebible y miniatura de un video alojado
 * en YouTube o Vimeo a partir de la URL que carga la administradora
 * (docs/02-arquitectura-y-datos.md §5: los videos no se alojan en el sistema).
 */
class ProveedorVideo
{
    public function detectarProveedor(string $enlace): string
    {
        if (str_contains($enlace, 'youtube.com') || str_contains($enlace, 'youtu.be')) {
            return 'youtube';
        }

        if (str_contains($enlace, 'vimeo.com')) {
            return 'vimeo';
        }

        return 'archivo';
    }

    public function id(string $proveedor, string $enlace): ?string
    {
        return match ($proveedor) {
            'youtube' => $this->idDeYoutube($enlace),
            'vimeo' => $this->idDeVimeo($enlace),
            default => null,
        };
    }

    public function urlEmbebida(string $proveedor, string $enlace): string
    {
        $id = $this->id($proveedor, $enlace);

        return match ($proveedor) {
            'youtube' => $id ? "https://www.youtube.com/embed/{$id}" : $enlace,
            'vimeo' => $id ? "https://player.vimeo.com/video/{$id}" : $enlace,
            default => $enlace,
        };
    }

    /**
     * Miniatura provista por el proveedor cuando no se cargó una propia
     * (docs/01-especificacion-funcional.md, tabla de campos de videos).
     */
    public function urlMiniatura(string $proveedor, string $enlace): ?string
    {
        $id = $this->id($proveedor, $enlace);

        if (! $id) {
            return null;
        }

        return match ($proveedor) {
            'youtube' => "https://i.ytimg.com/vi/{$id}/hqdefault.jpg",
            // Vimeo no entrega una miniatura predecible por id sin llamar a su API;
            // se documenta como límite conocido en el formulario de carga (fase 3).
            default => null,
        };
    }

    private function idDeYoutube(string $enlace): ?string
    {
        if (preg_match('#youtu\.be/([A-Za-z0-9_-]{6,})#', $enlace, $coincidencias)) {
            return $coincidencias[1];
        }

        if (preg_match('#[?&]v=([A-Za-z0-9_-]{6,})#', $enlace, $coincidencias)) {
            return $coincidencias[1];
        }

        if (preg_match('#youtube\.com/embed/([A-Za-z0-9_-]{6,})#', $enlace, $coincidencias)) {
            return $coincidencias[1];
        }

        return null;
    }

    private function idDeVimeo(string $enlace): ?string
    {
        if (preg_match('#vimeo\.com/(?:video/)?(\d+)#', $enlace, $coincidencias)) {
            return $coincidencias[1];
        }

        return null;
    }
}
