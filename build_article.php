<?php
declare(strict_types=1);

/* ========= Teacher's function (UNCHANGED) ========= */
function buildArticle(array $row): array {
    $row['title']     ??= 'Sans titre';
    $row['author']    ??= 'N/A';
    $row['published'] ??= true;

    $title   = trim((string)$row['title']);
    $excerpt = isset($row['excerpt']) ? trim((string)$row['excerpt']) : null;
    $excerpt = ($excerpt === '') ? null : $excerpt;

    $views   = (int)($row['views'] ?? 0);
    $views   = max(0, $views);

    return [
        'title'     => $title,
        'excerpt'   => $excerpt,
        'views'     => $views,
        'published' => (bool)$row['published'],
        'author'    => trim((string)$row['author']),
    ];
}
/* =============== End teacher's code =============== */


/* ===== Our test runner (just to see results) ===== */

/* Case 1: Many fields missing */
$case1 = [
    'title' => '  PHP  ',   // others missing
];

/* Case 2: Zeros and empty strings (0, '') */
$case2 = [
    'title'     => 'Test',
    'excerpt'   => '',      // empty -> should become null
    'views'     => 0,       // stays 0
    'published' => 0,       // stays false (bool cast)
    'author'    => '',      // stays empty string (spec only says "string")
];

/* Case 3: Nulls and numeric strings */
$case3 = [
    'title'     => null,    // -> default 'Sans titre'
    'excerpt'   => null,    // -> null
    'views'     => '300',   // -> 300 (int)
    'published' => null,    // -> default true
    'author'    => null,    // -> default 'N/A'
];

echo "=== Case 1 ===\n";
print_r(buildArticle($case1));

echo "\n=== Case 2 ===\n";
print_r(buildArticle($case2));

echo "\n=== Case 3 ===\n";
print_r(buildArticle($case3));
