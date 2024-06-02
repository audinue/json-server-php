# json-server-php

Inspired by [json-server](https://github.com/typicode/json-server).

```
GET /
GET /posts
GET /posts/1
POST /posts
PUT /posts/1
PUT /posts
PATCH /posts/1
PATCH /posts
DELETE /posts/1
DELETE /posts

GET /posts?search=a
GET /posts?sort=title&order=desc
GET /posts?size=4&page=2
GET /posts?with=comments
GET /comments?with=post

GET /posts/1?with=comments

DELETE /posts/1?with=comments
```