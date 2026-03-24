<?php

namespace App\Http\Controllers;

use App\Models\Pincode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFactory;
use SimpleSoftwareIO\QrCode\Generator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class QrCodeDownloadController extends Controller
{
    private const PDF_QR_PIXELS = 96;

    public function index()
    {
        $stateRank = array_flip(config('pincodes.state_codes_display_order', []));
        $conventionRank = array_flip(config('pincodes.convention_codes_display_order', []));

        $states = Pincode::query()
            ->selectRaw('state_code, state_name, COUNT(*) as pincode_count')
            ->groupBy('state_code', 'state_name')
            ->get()
            ->sortBy(function ($row) use ($stateRank, $conventionRank): int {
                $code = $row->state_code;
                if (isset($stateRank[$code])) {
                    return $stateRank[$code];
                }
                if (isset($conventionRank[$code])) {
                    return 1000 + $conventionRank[$code];
                }

                return 2000;
            })
            ->values();

        $qrFormat = extension_loaded('imagick') ? 'png' : 'svg';

        return view('qrcodes.index', [
            'states' => $states,
            'qrFormat' => $qrFormat,
        ]);
    }

    public function download(Request $request, string $stateCode): StreamedResponse
    {
        $stateCode = strtoupper($stateCode);

        if (! Pincode::where('state_code', $stateCode)->exists()) {
            abort(404);
        }

        $extension = extension_loaded('imagick') ? 'png' : 'svg';
        if ($request->query('format') === 'svg' && $extension === 'png') {
            $extension = 'svg';
        }

        $pincodes = Pincode::query()
            ->where('state_code', $stateCode)
            ->orderBy('serial')
            ->get(['code', 'state_name']);

        $zipBaseName = sprintf('%s_qr_codes', $stateCode);
        $zipFileName = $zipBaseName.'.zip';

        return ResponseFactory::streamDownload(function () use ($pincodes, $extension, $zipBaseName): void {
            $tmp = tempnam(sys_get_temp_dir(), 'zip');
            if ($tmp === false) {
                abort(500, 'Could not create temporary file.');
            }

            $zipPath = $tmp.'.zip';
            if (! @rename($tmp, $zipPath)) {
                @unlink($tmp);
                abort(500, 'Could not prepare zip archive.');
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                @unlink($zipPath);
                abort(500, 'Could not open zip archive.');
            }

            $zip->addEmptyDir($zipBaseName);

            foreach ($pincodes as $pincode) {
                $gen = new Generator;
                if ($extension === 'png') {
                    $body = $gen->format('png')
                        ->size(280)
                        ->margin(2)
                        ->errorCorrection('M')
                        ->generate($pincode->code);
                } else {
                    $body = $gen->format('svg')
                        ->size(280)
                        ->margin(2)
                        ->errorCorrection('M')
                        ->generate($pincode->code);
                }

                $zip->addFromString(
                    $zipBaseName.'/'.$pincode->code.'.'.$extension,
                    $body
                );
            }

            $zip->close();

            readfile($zipPath);
            @unlink($zipPath);
        }, $zipFileName, [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function downloadPdf(string $stateCode): Response
    {
        $stateCode = strtoupper($stateCode);

        if (! Pincode::where('state_code', $stateCode)->exists()) {
            abort(404);
        }

        set_time_limit(180);

        $stateName = (string) Pincode::query()
            ->where('state_code', $stateCode)
            ->value('state_name');

        $pincodes = Pincode::query()
            ->where('state_code', $stateCode)
            ->orderBy('serial')
            ->pluck('code');

        $cells = [];
        foreach ($pincodes as $code) {
            $cells[] = [
                'code' => $code,
                'qr_data_uri' => $this->qrDataUriForPdf($code),
            ];
        }

        $rows = array_chunk($cells, 4);

        $pdf = Pdf::loadView('qrcodes.pdf', [
            'stateCode' => $stateCode,
            'stateName' => $stateName,
            'rows' => $rows,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download(sprintf('%s_qr_codes.pdf', $stateCode));
    }

    private function qrDataUriForPdf(string $payload): string
    {
        $gen = new Generator;

        if (extension_loaded('imagick')) {
            $binary = $gen->format('png')
                ->size(self::PDF_QR_PIXELS)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($payload);

            return 'data:image/png;base64,'.base64_encode($binary);
        }

        $svg = $gen->format('svg')
            ->size(self::PDF_QR_PIXELS)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($payload);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
