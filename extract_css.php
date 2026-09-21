<?php
$files = [
    'resources/views/layouts/app.blade.php',
    'resources/views/admin/users/index.blade.php'
];

$css_content = [];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        preg_match_all('/<style>(.*?)<\/style>/s', $content, $matches);
        foreach ($matches[1] as $style) {
            $css_content[] = trim($style);
        }
        
        $new_content = preg_replace('/<style>.*?<\/style>/s', '', $content);
        
        if (strpos($file, 'app.blade.php') !== false) {
            $new_content = str_replace(
                '<!-- Chart.js for Dash UI Style Analytics -->',
                '<!-- Extracted Dashboard CSS -->'."\n".'    <link rel="stylesheet" href="{{ asset(\'css/dashboard.css\') }}">'."\n\n".'    <!-- Chart.js for Dash UI Style Analytics -->',
                $new_content
            );
        }
        
        file_put_contents($file, $new_content);
    }
}

if (!is_dir('public/css')) {
    mkdir('public/css', 0777, true);
}

file_put_contents('public/css/dashboard.css', implode("\n\n", $css_content));
echo "CSS extracted successfully.\n";
