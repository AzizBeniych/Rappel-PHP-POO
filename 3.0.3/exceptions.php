<?php
declare(strict_types=1);

/** Exception personnalisée pour les erreurs de seed / I/O. */
class SeedException extends RuntimeException {}

/** Valide un article minimal (titre + slug). */
function validateArticle(array $a): void {
  if (!isset($a['title']) || !is_string($a['title']) || $a['title'] === '') {
    throw new DomainException("Article invalide: 'title' requis.");
  }
  if (!isset($a['slug']) || !is_string($a['slug']) || $a['slug'] === '') {
    throw new DomainException("Article invalide: 'slug' requis.");
  }
}

/** Charge et décode un JSON en tableau associatif avec gestion d’erreurs. */
function loadJson(string $path): array {
  $raw = @file_get_contents($path);
  if ($raw === false) {
    // I/O error → SeedException (au lieu de RuntimeException)
    throw new SeedException("Fichier introuvable ou illisible: $path");
  }

  try {
    /** @var array $data */
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
  } catch (JsonException $je) {
    // JSON invalide → SeedException (au lieu de RuntimeException)
    throw new SeedException("JSON invalide: $path", previous: $je);
  }

  if (!is_array($data)) {
    // Cas logique: racine non-tableau
    throw new UnexpectedValueException("Le JSON doit contenir un tableau racine.");
  }
  return $data;
}

/** Point d’entrée CLI : attraper TOUT et retourner un code de sortie propre. */
function main(array $argv): int {
  $path = $argv[1] ?? 'storage/seeds/articles.input.json';

  $articles = loadJson($path);              // peut lever SeedException (I/O/JSON) ou UnexpectedValueException
  foreach ($articles as $i => $a) {
    validateArticle($a);                    // peut lever DomainException
  }

  echo "[OK] $path: " . count($articles) . " article(s) valides." . PHP_EOL;
  return 0;
}

try {
  exit(main($argv));
} catch (Throwable $e) {
  // Message clair vers STDERR
  $msg = "[ERR] " . $e->getMessage() . PHP_EOL;
  fwrite(STDERR, $msg);

  // Journalisation fichier (log) — créer le dossier storage/logs/ si besoin
  error_log($msg, 3, 'storage/logs/seed.log');

  // Optionnel : contexte dev (cause interne)
  if ($e->getPrevious()) {
    $cause = "Cause: " . get_class($e->getPrevious()) . " — " . $e->getPrevious()->getMessage() . PHP_EOL;
    fwrite(STDERR, $cause);
    error_log($cause, 3, 'storage/logs/seed.log');
  }

  exit(1);
}