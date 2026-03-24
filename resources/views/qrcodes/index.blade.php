@extends('layouts.app')

@section('title', 'Pincode QR downloads')

@section('content')
    <h1 class="page-title">Pincode QR downloads</h1>
    <p class="page-lead">
        Nigerian states and convention categories. Use <strong>Download</strong> for a <strong>ZIP</strong> of images or a <strong>PDF</strong> grid. Each QR encodes the pincode (e.g. <span class="mono">JIG-001</span> or <span class="mono">VIP-001</span>).
    </p>

    <div class="notice">
        ZIP archives use <strong>{{ strtoupper($qrFormat) }}</strong> files
        @if ($qrFormat === 'svg')
            when the PHP <code>imagick</code> extension is not installed. Install Imagick for PNG in ZIPs.
        @else
            (PNG via Imagick).
        @endif
        PDFs embed the same QR format (PNG or SVG) for each code.
    </div>

    <div class="data-card">
        <div class="data-table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Category</th>
                        <th scope="col" class="col-narrow">Prefix</th>
                        <th scope="col" class="col-narrow">Count</th>
                        <th scope="col" class="col-actions">Download</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($states as $row)
                        <tr>
                            <td>{{ $row->state_name }}</td>
                            <td class="col-narrow"><span class="mono">{{ $row->state_code }}</span></td>
                            <td class="col-narrow count-cell">{{ number_format($row->pincode_count) }}</td>
                            <td class="col-actions">
                                <div class="download-wrap">
                                    <details class="download-menu">
                                        <summary class="btn btn--primary download-menu__summary">Download</summary>
                                        <div class="download-menu__panel" role="menu">
                                            <a class="download-menu__item" role="menuitem" href="{{ route('qrcodes.download', ['stateCode' => $row->state_code]) }}">ZIP</a>
                                            <a class="download-menu__item" role="menuitem" href="{{ route('qrcodes.download.pdf', ['stateCode' => $row->state_code]) }}">PDF</a>
                                        </div>
                                    </details>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <p class="page-footer"><a href="{{ url('/') }}">← Back to home</a></p>
@endsection
