<?php

$posts = [
  [
    'id' => 1,
    'title' => 'Apple',
  ],
  [
    'id' => 2,
    'title' => 'Banana',
  ],
  [
    'id' => 3,
    'title' => 'Cherry',
  ],
];

$comments = [
  [
    'id' => 1,
    'text' => 'Comment A',
    'postId' => 1
  ],
  [
    'id' => 2,
    'text' => 'Comment B',
    'postId' => 1
  ],
  [
    'id' => 2,
    'text' => 'Comment C',
    'postId' => 2
  ],
];

$profile = [
  'name' => 'typicode',
  'email' => 'typicode@gmail.com',
];

$data = [
  'posts' => $posts,
  'comments' => $comments,
  'profile' => $profile,
];
