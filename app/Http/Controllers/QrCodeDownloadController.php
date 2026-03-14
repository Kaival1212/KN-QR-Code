<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class QrCodeDownloadController extends Controller
{
    public function download(QrCode $qrCode, string $format = 'svg'): Response
    {
        Gate::authorize('download', $qrCode);

        $url = $qrCode->getRedirectUrl();

        if ($format === 'png') {
            return $this->downloadPng($qrCode, $url);
        }

        return $this->downloadSvg($qrCode, $url);
    }

    private function downloadSvg(QrCode $qrCode, string $url): Response
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400),
            new SvgImageBackEnd
        );

        $content = (new Writer($renderer))->writeString($url);

        return response($content, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="'.$qrCode->slug.'.svg"',
        ]);
    }

    private function downloadPng(QrCode $qrCode, string $url): Response
    {
        $renderer = new ImageRenderer(
            new RendererStyle(400, 2),
            new ImagickImageBackEnd
        );

        $content = (new Writer($renderer))->writeString($url);

        return response($content, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="'.$qrCode->slug.'.png"',
        ]);
    }
}
