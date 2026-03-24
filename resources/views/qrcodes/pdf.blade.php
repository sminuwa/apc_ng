<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #0f172a;
            margin: 0;
            padding: 12px;
        }

        h1 {
            font-size: 13px;
            margin: 0 0 14px;
            font-weight: 700;
        }

        table.grid {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td.cell {
            width: 25%;
            text-align: center;
            vertical-align: top;
            padding: 6px 4px 10px;
        }

        td.cell img {
            display: block;
            margin: 0 auto;
        }

        .pin-label {
            margin-top: 3px;
            font-size: 7px;
            font-family: DejaVu Sans Mono, DejaVu Sans, sans-serif;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <h1>{{ $stateName }} — {{ $stateCode }}</h1>
    <table class="grid">
        @foreach ($rows as $row)
            <tr>
                @foreach ($row as $cell)
                    <td class="cell">
                        <img src="{{ $cell['qr_data_uri'] }}" width="72" height="72" alt=""/>
                        <div class="pin-label">{{ $cell['code'] }}</div>
                    </td>
                @endforeach
            </tr>
        @endforeach
    </table>
</body>
</html>
