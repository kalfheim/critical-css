# critical-css [![Build Status](https://travis-ci.org/kalfheim/critical-css.svg)](https://travis-ci.org/kalfheim/critical-css)

A Laravel package for generating and using inline critical-path CSS.

![CriticalCss](https://i.imgur.com/ZIGgtAz.gif)

## Why?

> For best performance, you may want to consider inlining the critical CSS directly into the HTML document. This eliminates additional roundtrips in the critical path and if done correctly can be used to deliver a “one roundtrip” critical path length where only the HTML is a blocking resource.

More information:

- https://github.com/addyosmani/critical/blob/master/README.md#why
- https://developers.google.com/web/fundamentals/performance/

**Table of Contents**

- [Installation](#installation)
    - [1) Install the Critical npm package](#1-install-the-critical-npm-package)
    - [2) Require the package](#2-require-the-package)
    - [3) Configure Laravel](#3-configure-laravel)
- [Usage](#usage)
    - [Generating critical-path CSS](#generating-critical-path-css)
    - [Using critical-path CSS with Blade templates](#using-critical-path-css-with-blade-templates)
- [A demo](#a-demo)
    - [PageSpeed Insights results](#pagespeed-insights-results)
- [A note on Laravel 5.0 compatibility](#a-note-on-laravel-50-compatibility)

## Installation

### 1) Install the Critical npm package

This package is used to extract critical-path CSS from an HTML document.

From your project's base path, run:

    $ npm install critical --save

Alternatively, install it globally:

    $ npm install -g critical

### 2) Require the package

Next, you'll need to require the package using Composer:

From your project's base path, run:

    $ composer require krisawzm/critical-css

### 3) Configure Laravel

#### Service Provider

Add the following to the `providers` key in `config/app.php`:

``` php
'providers' => [
    Alfheim\CriticalCss\CriticalCssServiceProvider::class,
];
```

#### Console

To get access to the `criticalcss:clear` and `criticalcss:make` commands, add the following to the `$commands` property in `app/Console/Kernel.php`:

``` php
protected $commands = [
    \Alfheim\CriticalCss\Console\CriticalCssMake::class,
    \Alfheim\CriticalCss\Console\CriticalCssClear::class,
];
```

#### Config

Generate a template for the `config/criticalcss.php` file by running:

    $ php artisan vendor:publish

> **Note:** Descriptions for the config options are only present in the config file, **not** in this readme. Click [here](https://github.com/kalfheim/critical-css/blob/master/src/config/criticalcss.php) to open the config file on GitHub.

## Usage

Before getting started, I highly recommend reading through the [`config/criticalcss.php`](src/config/criticalcss.php) file. That will give you a good idea of how this all works.

### Generating critical-path CSS

Providing everything is set up and configured properly, all you need to do in order to generate a fresh set of critical-path CSS files, is running the following command:

    $ php artisan criticalcss:make

This will generate a unique file for each of the URIs (routes) provided.

See [this commit](https://github.com/kalfheim/critical-css-demo/commit/8288ba8971fc7381ef933affdde3b3d71c5475e3) for a diff of the implementation.

### Using critical-path CSS with Blade templates

The service provider provides a new Blade directive named `@criticalCss`.

Simply call that directive, passing a route as the only argument, like so:

``` html
<html>
<head>
  ...
  @criticalCss('some/route')
</head>
</html>
```

If no argument is passed, the current route will be used, however, I highly recommend always passing a specific route.

And of course, make sure to asynchronously load the full CSS for the page using something like loadCSS (https://github.com/filamentgroup/loadCSS).

Full example (using Elixir to generate the URL for the CSS file, which or course is optional):

``` html
<html>
<head>
  ...
  @criticalCss('some/route')
  <script>
    !function(a){"use strict";var b=function(b,c,d){var g,e=a.document,f=e.createElement("link");if(c)g=c;else{var h=(e.body||e.getElementsByTagName("head")[0]).childNodes;g=h[h.length-1]}var i=e.styleSheets;f.rel="stylesheet",f.href=b,f.media="only x",g.parentNode.insertBefore(f,c?g:g.nextSibling);var j=function(a){for(var b=f.href,c=i.length;c--;)if(i[c].href===b)return a();setTimeout(function(){j(a)})};return f.onloadcssdefined=j,j(function(){f.media=d||"all"}),f};"undefined"!=typeof module?module.exports=b:a.loadCSS=b}("undefined"!=typeof global?global:this);
    loadCSS('{{ elixir('css/app.css') }}');
  </script>
</head>
</html>
```

For multiple views, you may wrap `@criticalCss` in a `@section`, then `@yield` the section in a master view.

## A demo

I made a simple demo using [this](http://startbootstrap.com/template-overviews/clean-blog/) Bootstrap theme. It's a fairly simple theme, and it does not have any major performance issues, but yet, implementing inline critical-path CSS **did** improve performance.

Demo repo: https://github.com/kalfheim/critical-css-demo

See [this commit](https://github.com/kalfheim/critical-css-demo/commit/8288ba8971fc7381ef933affdde3b3d71c5475e3) for a diff of the implementation.

### [PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights/) results

              | Mobile        | Desktop
------------- | ------------- | -------------
Before | <img src="https://i.imgur.com/86VyVgB.png"> | <img src="https://i.imgur.com/rS9j8Iq.png">
**After** | <img src="https://i.imgur.com/iSMjzCs.png"> | <img src="https://i.imgur.com/d86k0vj.png">

## A note on Laravel 5.0 compatibility

On Laravel 5.0, you must set `'blade_directive' => false` in the config. This is **not** recommended, but because [Custom Directives](http://laravel.com/docs/5.1/blade#extending-blade) were introduced in 5.1, it has to be done.

This will require adding the following to the `aliases` key in **config/app.php**:

``` php
'aliases' => [
    'Critical' => Alfheim\CriticalCss\Facades\Critical::class,
];
```

In your Blade views, you'll now be able to do the following instead of `@criticalCss('some/route')`:

``` php
{!! Critical::css('some/route') !!}
```
