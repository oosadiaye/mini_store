<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Labels</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
            background: #f3f4f6;
        }
        .controls {
            background: white;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
        }
        .btn {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background: #4338ca;
        }

        /* Label Grid */
        .page {
            background: white;
            width: 210mm; /* A4 Width */
            min-height: 297mm; /* A4 Height */
            margin: 0 auto;
            padding: 10mm;
            box-sizing: border-box;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 Columns */
            grid-auto-rows: 40mm; /* Approx label height */
            gap: 5mm;
            align-content: start;
        }

        .label {
            border: 1px dashed #e5e7eb; /* Guide lines for screen, distinct for print? */
            padding: 10px;
            display: flex;
            flex-col: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .product-name {
            font-size: 10pt;
            font-weight: bold;
            margin-bottom: 5px;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .price {
            font-size: 10pt;
            margin-top: 2px;
        }
        
        svg.barcode {
            max-width: 100%;
            height: 40px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .controls {
                display: none;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
            }
            .label {
                border: none; /* Hide border on print usually, or keep if cutting manually */
                page-break-inside: avoid;
            }
            @page {
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="controls">
        <div>
            <strong>{{ count($printQueue) }} items</strong> ready to print.
        </div>
        <button class="btn" onclick="window.print()">Print Labels</button>
    </div>

    <div class="page">
        @foreach($printQueue as $item)
            @for($i = 0; $i < $item['quantity']; $i++)
            <div class="label">
                <div class="product-name">{{ $item['product']->name }}</div>
                <svg class="jsbarcode"
                    data-format="CODE128"
                    data-value="{{ $item['product']->barcode ?? $item['product']->sku }}"
                    data-textmargin="0"
                    data-fontoptions="bold"
                    data-height="30"
                    data-width="2"
                    data-fontsize="12">
                </svg>
                <div class="price">${{ number_format($item['product']->price, 2) }}</div>
            </div>
            @endfor
        @endforeach
    </div>

    <script>
        JsBarcode(".jsbarcode").init();
    </script>
</body>
</html>
