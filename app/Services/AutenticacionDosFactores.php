<?php

namespace App\Services;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;

/**
 * Envoltorio del segundo factor TOTP (RF-93). Genera el secreto, arma el
 * código QR para el enrolamiento y verifica los códigos de 6 dígitos.
 */
class AutenticacionDosFactores
{
    public function __construct(private readonly Google2FA $google2fa = new Google2FA) {}

    public function generarSecreto(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function codigoQrSvg(string $correo, string $secreto): string
    {
        $url = $this->google2fa->getQRCodeUrl('Catalina Avendaño', $correo, $secreto);

        $renderer = new ImageRenderer(
            new RendererStyle(220),
            new SvgImageBackEnd
        );

        return (new Writer($renderer))->writeString($url);
    }

    public function verificar(string $secreto, string $codigo): bool
    {
        return (bool) $this->google2fa->verifyKey($secreto, $codigo);
    }
}
