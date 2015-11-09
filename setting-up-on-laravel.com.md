## How I used inline critical-path to improve performance on Laravel.com.

> I encourage you to follow along—either using Laravel.com or your own project! In most cases you'll see a fairly drastic improvement in performance.

### Table of Contents

1. [Building your own copy of Laravel.com](#1-building-your-own-copy-of-laravelcom)
1. [Installing Critical from npm](#2-installing-critical-from-npm)
1. [Installing the critical-css package](#3-installing-the-critical-css-package)

---

### 1. Building your own copy of Laravel.com

The Laravel.com website is of course built with Laravel, and it's open source. Grab a copy [on GitHub](https://github.com/laravel/laravel.com).

1. `git clone https://github.com/laravel/laravel.com` (or your own fork)
1. `cd laravel.com`
1. `composer install`
1. `npm install`

Create a `.env` file and fill in database info:

    DB_HOST=localhost
    DB_USERNAME=homestead
    DB_PASSWORD=secret
    DB_DATABASE=laravel

Migrate the database by running `php artisan migrate`.

Lastly, set up the docs with this one-liner:

    git clone --depth=1 -b 4.2 git@github.com:laravel/docs.git resources/docs/4.2 && git clone --depth=1 -b 5.0 git@github.com:laravel/docs.git resources/docs/5.0 && git clone --depth=1 -b 5.1 git@github.com:laravel/docs.git resources/docs/5.1 && git clone --depth=1 -b master git@github.com:laravel/docs.git resources/docs/master && rm -fr resources/docs/*/.git

> Note: I'm using [Homestead](http://laravel.com/docs/5.1/homestead), which means I'm running `composer install` and the migrate command on the VM, but you get the point. You may of course use whatever dev setup you'd like.

Now you should have your own copy of the Laravel.com website up and running - with working documentation.

### 2. Installing Critical from npm

Let's get started by installing the Critical npm package ([GitHub](https://github.com/addyosmani/critical), [npm](https://www.npmjs.com/package/critical)) by [Addy Osmani](https://twitter.com/addyosmani). You'll need [npm](https://www.npmjs.com/) to install it.

You've got two options:

**1)** Installing as a project dependency:

    $ npm install critical --save

**2)** Installing globally:

    $ npm install -g critical

### 3. Installing the critical-css package

I'll be using [Composer](https://getcomposer.org/) to install the package.

Again, two options:

**1.1)** Simply run this command:

    composer require krisawzm/critical-css

**1.2)** Or, add this to the `require` section of your `composer.json` file and run `composer install` or `composer update`:

    "krisawzm/critical-css": "^1.0"

**2)** Register the service provider by adding the following to the `providers` key in **config/app.php**:

``` php
'providers' => [
    Krisawzm\CriticalCss\CriticalCssServiceProvider::class,
];
```

**3)** Publish vendor assets by running:

    php artisan vendor:publish

**4)** Set up the two Artisan commands by adding the following to the `$commands` property in **app/Console/Kernel.php**:

``` php
protected $commands = [
    \Krisawzm\CriticalCss\Console\CriticalCssMake::class,
    \Krisawzm\CriticalCss\Console\CriticalCssClear::class,
];
```

### 3. Configuring the critical-css package

We'll start by setting up the routes configuration. The generator needs HTML to work with, and this is where it gets the HTML from.

In **config/criticalcss.php**, take a look at the `routes` item. You'll see that it's already documented, so I won't repeat myself. (Read the config!)

For Laravel.com, I ended up with something like this:

``` php
'routes' => [
    '/',
    'docs' => 'docs/5.1',
],
```

I won't bother adding more than one route for the docs, since all the documentation pages are very similar in both design and layout.

I also set the following for the `css` property:

``` php
'css' => ['assets/css/laravel.css'],
```

### 4. Adding critical-path CSS to the Blade views

When inlining critical-path CSS, it's important to make sure the full CSS is loaded asynchronously. For that purpose, we'll be using filamentgroup's [loadCSS](https://github.com/filamentgroup/loadCSS).

`app.blade.php` is Laravel.com's main view, which `docs.blade.php` and `marketing.blade.php` (the index) extends.

In **resources/views/app.blade.php**, add the following before the `</head>` tag.

``` html
<head>
    ... other stuff ...

    @yield('critical-css')

    <script>
    !function(a){"use strict";var b=function(b,c,d){var g,e=a.document,f=e.createElement("link");if(c)g=c;else{var h=(e.body||e.getElementsByTagName("head")[0]).childNodes;g=h[h.length-1]}var i=e.styleSheets;f.rel="stylesheet",f.href=b,f.media="only x",g.parentNode.insertBefore(f,c?g:g.nextSibling);var j=function(a){for(var b=f.href,c=i.length;c--;)if(i[c].href===b)return a();setTimeout(function(){j(a)})};return f.onloadcssdefined=j,j(function(){f.media=d||"all"}),f};"undefined"!=typeof module?module.exports=b:a.loadCSS=b}("undefined"!=typeof global?global:this);

    loadCSS('{{ elixir('assets/css/laravel.css') }}');
    </script>
</head>
```

And, of course, remove `<link rel="stylesheet" href="{{ elixir('assets/css/laravel.css') }}">`.

Add the following in  **resources/views/docs.blade.php**, below `@extends('app')`:

``` php
@section('critical-css')
    @criticalCss('docs')
@endsection
```

Same story for  **resources/views/marketing.blade.php**, but with the `/` route:

``` php
@section('critical-css')
    @criticalCss('/')
@endsection
```

**Wrap up** by running the command which generates the critical-path CSS:

    php artisan criticalcss:make

---

### A note on Laravel 5.0 compatibility

Because Laravel.com is still running Laravel 5.0, I had to set `'blade_directive' => false,`. This is **not** recommended, but because [Custom Directives](http://laravel.com/docs/5.1/blade#extending-blade) were introduced in 5.1, it had to be done.

This requires the use of adding the following to the `aliases` key in **config/app.php**:

``` php
'aliases' => [
    'Critical' => Krisawzm\CriticalCss\Facades\Critical::class,
];
```

In your Blade views, you'll now be able to do the following instead of `@criticalCss('some/route')`:

``` php
{!! Critical::css('some/route') !!}
```
