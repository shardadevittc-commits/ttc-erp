<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bundle details</title>
    <style>
        :root { color-scheme: light; font-family: Arial, Helvetica, sans-serif; color: #20252a; }
        body { margin: 0; padding: 24px 16px; background: #f3f6f8; }
        main { max-width: 560px; margin: 0 auto; overflow: hidden; border: 1px solid #d5dce1; border-radius: 12px; background: #fff; }
        header { padding: 22px 24px; background: #064d78; color: #fff; }
        h1 { margin: 0 0 6px; font-size: 1.25rem; overflow-wrap: anywhere; }
        header p { margin: 0; opacity: .9; overflow-wrap: anywhere; }
        dl { margin: 0; padding: 8px 24px 20px; }
        dl div { display: grid; grid-template-columns: minmax(110px, 1fr) 1.4fr; gap: 12px; padding: 13px 0; border-bottom: 1px solid #e3e7ea; }
        dl div:last-child { border-bottom: 0; }
        dt { color: #59636b; font-size: .88rem; }
        dd { margin: 0; font-weight: 700; overflow-wrap: anywhere; }
        @media (max-width: 380px) { dl { padding-right: 16px; padding-left: 16px; } dl div { grid-template-columns: 1fr; gap: 4px; } }
    </style>
</head>
<body>
    <main>
        <header>
            <h1>{{ $bundle['company'] }}</h1>
            <p>Bundle details</p>
        </header>
        <dl>
            <div><dt>Product</dt><dd>{{ $bundle['product'] }}</dd></div>
            <div><dt>Work order</dt><dd>{{ $bundle['work_order'] }}</dd></div>
            <div><dt>Sale order</dt><dd>{{ $bundle['sale_order'] }}</dd></div>
            <div><dt>Bundle</dt><dd>{{ $bundle['bundle'] }} ({{ strtoupper($bundle['type']) }})</dd></div>
            <div><dt>Heat number</dt><dd>{{ $bundle['heat'] ?: '-' }}</dd></div>
            <div><dt>Grade</dt><dd>{{ $bundle['grade'] }}</dd></div>
            <div><dt>Size</dt><dd>{{ $bundle['size'] }}</dd></div>
            <div><dt>Weight</dt><dd>{{ $bundle['weight'] }} {{ $bundle['unit'] }}{{ $bundle['type'] === 'loose' ? ' (L)' : '' }}</dd></div>
            @if (!empty($bundle['pieces']))
                <div><dt>Pieces</dt><dd>{{ $bundle['pieces'] }}</dd></div>
            @endif
        </dl>
    </main>
</body>
</html>
