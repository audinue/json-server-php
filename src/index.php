<?php

require_once __DIR__ . '/respond.php';

$file   = 'data.json';
$data   = json_decode(file_get_contents($file), true);
$method = $_SERVER['REQUEST_METHOD'];
$path   = $_SERVER['PATH_INFO'] ?? '/';
$base   = substr($_SERVER['REQUEST_URI'], 0, -strlen($path));
$query  = $_GET;
$body   = json_decode(file_get_contents('php://input'), true);

[$next, $result] = respond($data, $method, $base, $path, $query, $body);

if ($next != $data) {
  file_put_contents($file, json($next));
}

header('Content-Type: application/json');

echo json($result);

function json($value)
{
  return preg_replace_callback(
    '/^ +/m',
    fn ($matches) => str_repeat(' ', strlen($matches[0]) / 2),
    json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
  );
}
