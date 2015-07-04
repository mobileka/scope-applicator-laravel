[![Build Status](https://travis-ci.org/mobileka/scope-applicator-laravel.svg)](https://travis-ci.org/mobileka/scope-applicator-laravel)
[![Code Climate](https://codeclimate.com/github/mobileka/scope-applicator-laravel.svg)](https://codeclimate.com/github/mobileka/scope-applicator-laravel)
[![Coverage Status](https://coveralls.io/repos/mobileka/scope-applicator-laravel/badge.svg?branch=master)](https://coveralls.io/r/mobileka/scope-applicator-laravel?branch=master)

*If you're looking for a version which works with Laravel 5.1.x, click [here](https://github.com/mobileka/scope-applicator-laravel).*

[ScopeApplicator](https://github.com/mobileka/scope-applicator) brings an elegant way of sorting and filtering data to your Laravel projects.

- [Overview](#overview)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage (with Models)](#usage-with-models)
- [A better usage scenario (with Repositories)](#a-better-usage-scenario-with-repositories)
- [Contributing](#contributing)
- [License](#license)

## Overview

ScopeApplicator is an easy and logical way to achieve something like this:

`/posts` – returns a list of all posts

`/posts?recent` – returns only recent posts

`/posts?author_id=5` – returns posts belonging to an author with an `id=5`

`/posts?author_id=5&order_by_title=desc&status=active` – returns only active posts belonging to an author with an `id=5` and sorts them by a title in a descending order

## Requirements

— PHP 5.4 or higher

Tested with:

* Laravel 5.0.x
* Laravel 4.x.x
* Laravel 3

*If you're looking for a version which works with Laravel 5.1.x, click [here](https://github.com/mobileka/scope-applicator-laravel).*

## Installation

`composer require mobileka/scope-applicator-laravel 1.0.*`

## Usage (with Models)

> Make sure you are familiar with Laravel's [query scopes](http://laravel.com/docs/eloquent#query-scopes) before you dive in

Let's learn by example. First of all, we'll implement an `author_id` filter for `posts` table.

> Please note that this is going to be a basic example and it's not the most optimal way of doing things ;)

These are steps required to achieve this:

1. Create a basic `PostController` which outputs a list of posts when you hit `/posts` route
2. Create a `userId` scope in the `Post` model (and it has to extend the `Mobileka\ScopeApplicator\Laravel\Model` class)
3. Tell ScopeApplicator that this scope is available and give it an alias
4. Visit `/posts?author_id=1` and enjoy the result

Ok, let's cover these step by step.

— The `PostController`:

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }
}
```

— The `Post` model:

```php
<?php namespace App\Models;

use Mobileka\ScopeApplicator\Laravel\Model;

class Post extends Model
{
    public function scopeUserId($builder, $param = 0)
    {
        if (!$param) {
            return $builder;
        }
        
        return $builder->where('user_id', '=', $param);
    }
}
```

> Note that it extends `Mobileka\ScopeApplicator\Laravel\Model`

— Now we have to replace `Post::all()` in our controller with `Post::handleScopes()` and tell this method which scopes are available for filtering:

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
    // an array of available scopes
    public $scopes = [
        'userId'
    ];

    public function index()
    {
        return Post::handleScopes($this->scopes)->get();
    }
}
```

Take a note that `'userId'` matches the name of the scope we've created in the `Post` model (`scopeUserId`).

At this moment you can add some dummy data to your `posts` table and make sure that you can filter it by hitting the following route:
`/posts?userId=your_number`

But, as we wanted `author_id` instead of `userId`, let's create an alias for this scope:

```php
<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Post;

class PostController extends Controller
{
    // an array of available scopes
    public $scopes = [
        'userId' => [
            // Here it is!
            'alias' => 'author_id'
        ]
    ];

    public function index()
    {
        return Post::handleScopes($this->scopes)->get();
    }
}
```
— That's it! Now you can visit `/posts?author_id=x` and check the result.

`alias` is only one of the many available scope configuration options. These are described in ScopeApplicator's [documentation](https://github.com/mobileka/scope-applicator#configuration-options).

## A better usage scenario (with Repositories)

ScopeApplicator can also be used with [Repositories](http://blog.armen.im/laravel-and-repository-pattern). It was actually designed to be used this way.

To achieve this, your repository has to extend the `Mobileka\ScopeApplicator\Laravel\Repository` class.

The ScopeApplicator is already attached to this class, so you'll have a new `applyScopes()` method available in repositories extending it.

Let's see an example `BaseRepository` *before* we extend the aforementioned class:

```php
<?php namespace Acme\Repositories;

class BaseRepository
{
    protected $dataProvider;
    
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }
    
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
    
    public function all()
    {
        return $this->getDataProvider()->all();
    }
}
```

`DataProvider` is typically an instance of a `Model`.

And now what it looks like with ScopeApplicator:

```php
<?php namespace Acme\Repositories;

use Mobileka\ScopeApplicator\Laravel\Repository;

class BaseRepository extends Repository
{
    protected $dataProvider;
    
    public function __construct($dataProvider)
    {
        $this->dataProvider = $dataProvider;
    }
    
    public function getDataProvider()
    {
        return $this->dataProvider;
    }
    
    public function all($scopes = [])
    {
        // This part has to be noticed!
        return $this->applyScopes($this->getDataProvider(), $scopes)->get();
    }
}
```

Pay closer attention to `all` method. Now it accepts an array of scopes (the same array we were passing to `Model::handleScopes()`).

Instead of directly calling `all` on our DataProvider, we now use `applyScopes()` method which accepts a `DataProvider` instance as a first argument and a scope configuration array as a second.

## Contributing

If you have noticed a bug or have suggestions, you can always create an issue or a pull request (use PSR-2). We will discuss the problem or a suggestion and plan the implementation together.

## License

ScopeApplicator is an open-source software and licensed under the [MIT License](https://github.com/mobileka/scope-applicator-laravel/blob/master/license).
