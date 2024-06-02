<?php

require_once __DIR__ . '/pluralize.php';

function respond(
  array $data,
  string $method,
  string $base,
  string $path,
  array $query,
  ?array $body
): ?array {
  switch ($method) {
    case 'GET':
      if (preg_match('@^/(.+?)(?:/(.+))?$@', $path, $matches)) {
        $name = $matches[1];
        if (isset($matches[2])) {
          foreach ($data[$name] as $row) {
            if ($row['id'] == $matches[2]) {
              return [$data, apply($data, $name, $row, $query)];
            }
          }
        } else {
          $result = $data[$name];
          if (isset($query['search'])) {
            $next = [];
            foreach ($result as $row) {
              foreach ($row as $cell) {
                if (stripos($cell, $query['search']) !== false) {
                  $next[] = $row;
                  continue 2;
                }
              }
            }
            $result = $next;
          }
          if (isset($query['sort'])) {
            $sort = $query['sort'];
            usort(
              $result,
              fn ($a, $b) => ($query['order'] == 'desc' ? -1 : 1) * strnatcasecmp($a[$sort], $b[$sort])
            );
          }
          if (isset($query['size'])) {
            $size = intval($query['size']);
            $row = count($result);
            $page = ceil($row / $size);
            $rows = array_slice(
              $result,
              (($query['page'] ?? 1) - 1) * $size,
              $size
            );
            $result = [
              'rows' => array_map(
                fn ($row) => apply($data, $name, $row, $query),
                $rows
              ),
              'row' => $row,
              'page' => $page,
            ];
          }
          return [$data, $result];
        }
      } else {
        return [
          $data,
          array_map(
            fn ($name) => "$base/$name",
            array_keys($data)
          )
        ];
      }
      break;
    case 'POST':
      if (preg_match('@^/(.+?)$@', $path, $matches)) {
        $name = $matches[1];
        $id = $body['id'] ?? uniqid(); // untestable
        $data[$name][] = [
          'id' => $id,
          ...$body
        ];
        return [$data, $id];
      }
      break;
    case 'PUT':
      if (preg_match('@^/(.+?)(?:/(.+))?$@', $path, $matches)) {
        $name = $matches[1];
        if (isset($matches[2])) {
          foreach ($data[$name] as &$row) {
            if ($row['id'] == $matches[2]) {
              $row = $body;
              break;
            }
          }
          return [$data, null];
        } else {
          $data[$name] = $body;
          return [$data, null];
        }
      }
      break;
    case 'PATCH':
      if (preg_match('@^/(.+?)(?:/(.+))?$@', $path, $matches)) {
        $name = $matches[1];
        if (isset($matches[2])) {
          foreach ($data[$name] as &$row) {
            if ($row['id'] == $matches[2]) {
              foreach ($body as $key => $value) {
                $row[$key] = $value;
              }
              break;
            }
          }
          return [$data, null];
        } else {
          foreach ($body as $key => $value) {
            $data[$name][$key] = $value;
          }
          return [$data, null];
        }
      }
      break;
    case 'DELETE':
      if (preg_match('@^/(.+?)(?:/(.+))?$@', $path, $matches)) {
        $name = $matches[1];
        if (isset($matches[2])) {
          $id = $matches[2];
          foreach ($data[$name] as $index => $row) {
            if ($row['id'] == $id) {
              array_splice($data[$name], $index, 1);
              if (isset($query['with'])) {
                $key = (new Pluralize)->singularize($name) . 'Id';
                foreach (explode(',', $query['with']) as $child) {
                  $next = [];
                  foreach ($data[$child] as $row) {
                    if ($row[$key] != $id) {
                      $next[] = $row;
                    }
                  }
                  $data[$child] = $next;
                }
              }
              break;
            }
          }
          return [$data, null];
        } else {
          unset($data[$name]);
          return [$data, null];
        }
      }
      break;
    default:
      return [$data, null];
  }
}

function apply(
  array $data,
  string $name,
  array $row,
  array $query
) {
  if (isset($query['with'])) {
    $pluralize = new Pluralize;
    foreach (explode(',', $query['with']) as $ref) {
      if (isset($data[$ref])) {
        $key = $pluralize->singularize($name) . 'Id';
        $children = [];
        foreach ($data[$ref] as $child) {
          if ($child[$key] == $row['id']) {
            $children[] = $child;
          }
        }
        $row[$ref] = $children;
      } else {
        $pname = $pluralize->pluralize($ref);
        $pid = $row[$ref . 'Id'];
        foreach ($data[$pname] as $parent) {
          if ($parent['id'] == $pid) {
            $row[$ref] = $parent;
            break;
          }
        }
      }
    }
  }
  return $row;
}
