<?php
$article = [
  'title'     => 'Intro Laravel',
  'excerpt'   => null,
  'views'     => 120,
  'published' => true,
];

$article['author'] = 'Amina';                 // ajout
$hasViews          = array_key_exists('views', $article); // true même si null

// Show the whole array
print_r($article);

// Show the check result
var_dump($hasViews);