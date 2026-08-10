<?php

$sources = env('SUPERMARKET_SOURCES');
$sources = $sources
    ? array_values(array_filter(array_map('trim', explode(',', $sources))))
    : [
        'seoudi',
        'mahmoud_elfar',
        'hyperone',
        'carrefour',
    ];

if (filter_var(env('AMAZON_EG_ENABLED', false), FILTER_VALIDATE_BOOLEAN) && !in_array('amazon_eg', $sources, true)) {
    $sources[] = 'amazon_eg';
}

return [
    'margin_percent' => (float) env('SUPERMARKET_MARGIN_PERCENT', 15),
    'sync_time'      => env('SUPERMARKET_SYNC_TIME', '03:00'),
    'products_path'  => env('SUPERMARKET_PRODUCTS_PATH', base_path('../data/latest/products.jsonl')),
    'clusters_path'  => env('SUPERMARKET_CLUSTERS_PATH', base_path('../data/latest/clusters.json')),
    'scraper_root'   => env('SUPERMARKET_SCRAPER_ROOT', base_path('..')),
    'python'         => env('SUPERMARKET_PYTHON', base_path('../.venv/bin/python')),
    'memory_limit'   => env('SUPERMARKET_IMPORT_MEMORY_LIMIT', '512M'),
    'sources'        => $sources,
];
