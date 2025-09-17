<?php
// The Main Goal: Learn how to clean, transform, and summarize data (arrays) using modern PHP 8 tools.

declare(strict_types=1);

$articles = [
  ['id'=>1,'title'=>'Intro Laravel','category'=>'php','views'=>120,'author'=>'Amina','published'=>true,  'tags'=>['php','laravel']],
  ['id'=>2,'title'=>'PHP 8 en pratique','category'=>'php','views'=>300,'author'=>'Yassine','published'=>true,  'tags'=>['php']],
  ['id'=>3,'title'=>'Composer & Autoload','category'=>'outils','views'=>90,'author'=>'Amina','published'=>false, 'tags'=>['composer','php']],
  ['id'=>4,'title'=>'Validation FormRequest','category'=>'laravel','views'=>210,'author'=>'Sara','published'=>true,  'tags'=>['laravel','validation']],
];


function slugify(string $title): string {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
    return trim($slug, '-');
}

// function slugify(string $title): string
// A function named slugify that takes a string title and returns a string.
// 
// $slug = strtolower($title);
// Make everything lowercase.
// Darija: klchi small letters.
// 
// $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);
// Replace any group of non letters/digits with -.
// Spaces, accents, symbols → become dashes.
// 
// return trim($slug, '-');
// Remove extra - at start or end.
// 
// Examples of slugs with your data:
// 
// "Intro Laravel" → intro-laravel
// 
// "PHP 8 en pratique" → php-8-en-pratique
// 
// "Validation FormRequest" → validation-formrequest


////////////////////////////////////////////////////////////////////////////////



$published = array_values(
  array_filter($articles, fn(array $a) => $a['published'] ?? false)
);


$light = array_map(
  fn(array $a) => [
    'id'    => $a['id'],
    'title' => $a['title'],
    'slug'  => slugify($a['title']),
    'views' => $a['views'],
  ],
  $published
);

// array_map transforms each article into a smaller shape (id, title, slug, views).
// We call slugify($a['title']) to build the URL-friendly slug.


////////////////////////////////////////////////////////////////////////////////



$top = $light;
usort($top, fn($a, $b) => $b['views'] <=> $a['views']);
$top3 = array_slice($top, 0, 3);



// $top = $light; work on a copy.
// 
// usort(..., fn($a,$b)=> $b['views'] <=> $a['views'])
// Sort descending by views (big to small).
// <=> = spaceship operator (compare).
// 
// array_slice($top, 0, 3) → take first 3.

$byAuthor = array_reduce(
  $published,
  function(array $acc, array $a): array {
      $author = $a['author'];
      $acc[$author] = ($acc[$author] ?? 0) + 1;
      return $acc;
  },
  []
);




$allTags = array_merge(...array_map(fn($a) => $a['tags'], $published));

$tagFreq = array_reduce(
  $allTags,
  function(array $acc, string $tag): array {
      $acc[$tag] = ($acc[$tag] ?? 0) + 1;
      return $acc;
  },
  []
);

echo "Top 3 (views):\n";
foreach ($top3 as $a) {
  echo "- {$a['title']} ({$a['views']} vues) — {$a['slug']}\n";
}

echo "\nPar auteur:\n";
foreach ($byAuthor as $author => $count) {
  echo "- $author: $count article(s)\n";
}

echo "\nTags:\n";
foreach ($tagFreq as $tag => $count) {
  echo "- $tag: $count\n";
}