<?php

namespace App\Services;

use App\Enums\ProveedorVideoTipo;

/**
 * Interpreta el enlace de un video (YouTube, Vimeo o archivo directo): de
 * qué proveedor es, cómo se embebe y de dónde sale su miniatura cuando no se
 * sube una a mano.
 */
class ProveedorVideo
{
    public function detectar(string $enlace): ProveedorVideoTipo
    {
        if (preg_match('/(youtube\.com|youtu\.be)/i', $enlace)) {
            return ProveedorVideoTipo::Youtube;
        }

        if (preg_match('/vimeo\.com/i', $enlace)) {
            return ProveedorVideoTipo::Vimeo;
        }

        return ProveedorVideoTipo::Archivo;
    }

    public function idExterno(string $enlace, ?ProveedorVideoTipo $proveedor = null): ?string
    {
        $proveedor ??= $this->detectar($enlace);

        return match ($proveedor) {
            ProveedorVideoTipo::Youtube => $this->idYoutube($enlace),
            ProveedorVideoTipo::Vimeo => $this->idVimeo($enlace),
            ProveedorVideoTipo::Archivo => null,
        };
    }

    public function urlEmbebido(string $enlace): ?string
    {
        $proveedor = $this->detectar($enlace);
        $id = $this->idExterno($enlace, $proveedor);

        return match ($proveedor) {
            ProveedorVideoTipo::Youtube => $id ? "https://www.youtube-nocookie.com/embed/{$id}" : null,
            ProveedorVideoTipo::Vimeo => $id ? "https://player.vimeo.com/video/{$id}" : null,
            ProveedorVideoTipo::Archivo => $enlace,
        };
    }

    /**
     * Miniatura del proveedor cuando no se sube una a mano (RF de la Fase 3).
     * Vimeo no expone una URL predecible sin llamar a su API —pendiente #6
     * de AVANCE.md—; por ahora solo se resuelve YouTube.
     */
    public function urlMiniatura(string $enlace): ?string
    {
        $proveedor = $this->detectar($enlace);
        $id = $this->idExterno($enlace, $proveedor);

        return match ($proveedor) {
            ProveedorVideoTipo::Youtube => $id ? "https://i.ytimg.com/vi/{$id}/maxresdefault.jpg" : null,
            ProveedorVideoTipo::Vimeo, ProveedorVideoTipo::Archivo => null,
        };
    }

    private function idYoutube(string $enlace): ?string
    {
        if (preg_match('#(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})#', $enlace, $coincidencia)) {
            return $coincidencia[1];
        }

        return null;
    }

    private function idVimeo(string $enlace): ?string
    {
        if (preg_match('#vimeo\.com/(?:[^/]+/)*(\d+)#', $enlace, $coincidencia)) {
            return $coincidencia[1];
        }

        return null;
    }
}
