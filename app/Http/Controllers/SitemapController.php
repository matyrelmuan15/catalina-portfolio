<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $actualizado = Video::query()->where('publicado', true)->max('updated_at') ?? now();

        $xml = view('publico.sitemap', ['actualizado' => $actualizado])->render();

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
