<?php

namespace App\Enums;

enum ProveedorVideoTipo: string
{
    case Youtube = 'youtube';
    case Vimeo = 'vimeo';
    case Archivo = 'archivo';
}
