<?php
declare(strict_types=1);

/* ---- 0) Test data (needed so the code can run) ---- */
$articles = [
  ['id'=>1,'title'=>'Intro Laravel','views'=>120,'author'=>'Amina','category'=>'php','published'=>true],
  ['id'=>2,'title'=>'PHP 8 en pratique','views'=>300,'author'=>'Yassine','category'=>'php','published'=>true],
  ['id'=>3,'title'=>'Composer & Autoload','views'=>90,'author'=>'Amina','category'=>'tools','published'=>false],
  ['id'=>4,'title'=>'Routing in Laravel','views'=>210,'author'=>'Sara','category'=>'laravel','published'=>true],
];

/* ---- 1) Helper: slugify (needed by the teacher's code) ---- */
function slugify(string $s): string {
    $s = mb_strtolower($s, 'UTF-8');
    // replace non letters/digits with hyphens
    $s = preg_replace('~[^\p{L}\p{Nd}]+~u', '-', $s);
    // trim hyphens
    $s = trim($s, '-');
    // collapse multiple hyphens
    $s = preg_replace('~-+~', '-', $s);
    return $s ?? '';
}

/* ---- 2) Teacher's solution (UNCHANGED) ---- */
$published = array_values(array_filter($articles, fn($a) => $a['published'] ?? false));

$normalized = array_map(
  fn($a) => [
    'id'       => $a['id'],
    'slug'     => slugify($a['title']),
    'views'    => $a['views'],
    'author'   => $a['author'],
    'category' => $a['category'],
  ],
  $published
);

usort($normalized, fn($x, $y) => $y['views'] <=> $x['views']);

$summary = array_reduce(
  $published,
  function(array $acc, array $a): array {
      $acc['count']      = ($acc['count'] ?? 0) + 1;
      $acc['views_sum']  = ($acc['views_sum'] ?? 0) + $a['views'];
      $cat = $a['category'];
      $acc['by_category'][$cat] = ($acc['by_category'][$cat] ?? 0) + 1;
      return $acc;
  },
  ['count'=>0, 'views_sum'=>0, 'by_category'=>[]]
);

print_r($normalized);
print_r($summary);
