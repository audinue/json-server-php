<?php

require __DIR__ . '/../src/respond.php';
require __DIR__ . '/test_data.php';

$tests = [
  [
    'input' => [
      'method' => 'GET',
      'base' => '/api',
      'path' => '/'
    ],
    'expected' => [
      $data,
      [
        '/api/posts',
        '/api/comments',
        '/api/profile',
      ]
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/posts'
    ],
    'expected' => [
      $data,
      $posts
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/posts/1'
    ],
    'expected' => [
      $data,
      $posts[0]
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/posts/1',
      'query' => ['with' => 'comments']
    ],
    'expected' => [
      $data,
      [
        ...$posts[0],
        'comments' => [
          $comments[0],
          $comments[1],
        ]
      ]
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/comments/1',
      'query' => ['with' => 'post']
    ],
    'expected' => [
      $data,
      [
        ...$comments[0],
        'post' => $posts[0]
      ]
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/posts',
      'query' => ['search' => 'e']
    ],
    'expected' => [
      $data,
      [
        $posts[0],
        $posts[2],
      ]
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/posts',
      'query' => ['sort' => 'title', 'order' => 'desc']
    ],
    'expected' => [
      $data,
      [
        $posts[2],
        $posts[1],
        $posts[0],
      ]
    ]
  ],
  [
    'input' => [
      'method' => 'GET',
      'path' => '/posts',
      'query' => ['size' => 1, 'page' => 2]
    ],
    'expected' => [
      $data,
      [
        'rows' => [
          $posts[1],
        ],
        'row' => 3,
        'page' => 3
      ]
    ]
  ],
  [
    'input' => [
      'method' => 'POST',
      'path' => '/posts',
      'body' => [
        'id' => 4,
        'title' => 'Durian',
      ],
    ],
    'expected' => [
      [
        ...$data,
        'posts' => [
          ...$posts,
          [
            'id' => 4,
            'title' => 'Durian',
          ]
        ]
      ],
      4
    ]
  ],
  [
    'input' => [
      'method' => 'PUT',
      'path' => '/posts/1',
      'body' => [
        'id' => 1,
        'title' => 'Apple X',
      ],
    ],
    'expected' => [
      [
        ...$data,
        'posts' => [
          [
            'id' => 1,
            'title' => 'Apple X',
          ],
          $posts[1],
          $posts[2],
        ]
      ],
      null
    ]
  ],
  [
    'input' => [
      'method' => 'PUT',
      'path' => '/posts',
      'body' => [
        [
          'id' => 1,
          'title' => 'Apple',
        ]
      ],
    ],
    'expected' => [
      [
        ...$data,
        'posts' => [
          [
            'id' => 1,
            'title' => 'Apple',
          ]
        ]
      ],
      null
    ]
  ],
  [
    'input' => [
      'method' => 'PATCH',
      'path' => '/posts/1',
      'body' => [
        'title' => 'Apple Y',
      ],
    ],
    'expected' => [
      [
        ...$data,
        'posts' => [
          [
            'id' => 1,
            'title' => 'Apple Y',
          ],
          $posts[1],
          $posts[2],
        ]
      ],
      null
    ]
  ],
  [
    'input' => [
      'method' => 'DELETE',
      'path' => '/posts/1',
    ],
    'expected' => [
      [
        ...$data,
        'posts' => array_slice($posts, 1)
      ],
      null
    ]
  ],
  [
    'input' => [
      'method' => 'DELETE',
      'path' => '/posts/1',
      'query' => [
        'with' => 'comments'
      ],
    ],
    'expected' => [
      [
        ...$data,
        'posts' => array_slice($posts, 1),
        'comments' => array_slice($comments, 2)
      ],
      null
    ]
  ],
];

$success = true;

foreach ($tests as $test) {
  $input = $test['input'];
  $expected = $test['expected'];
  $actual = respond(
    $input['data'] ?? $data,
    $input['method'],
    $input['base'] ?? '/api',
    $input['path'],
    $input['query'] ?? [],
    $input['body'] ?? null,
  );
  if ($actual != $expected) {
    echo "FAILED\n";
    var_dump([
      'input' => $input,
      'expected' => $expected,
      'actual' => $actual
    ]);
    $success = false;
    break;
  }
}

if ($success) {
  echo "AWESOME\n";
}
