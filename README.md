# critical-css [![Build Status](https://travis-ci.org/krisawzm/critical-css.svg)](https://travis-ci.org/krisawzm/critical-css)

A Laravel package for generating and using inline critical-path CSS.

**The goal of this package** is to make it easy for Laravel developers (and PHP developers in general) to inline critical-path CSS in their projects.

## Why?

> For best performance, you may want to consider inlining the critical CSS directly into the HTML document. This eliminates additional roundtrips in the critical path and if done correctly can be used to deliver a “one roundtrip” critical path length where only the HTML is a blocking resource.

More info at https://github.com/addyosmani/critical/blob/master/README.md#why

## Installation

### 1) Install the Critical tool

First, you'll need to install the Critical tool from npm. This tool is used to extract critical-path CSS from an HTML document.

From your project's base path, run:

    $ npm install --save-dev critical

Alternatively, install it globally:

    $ npm install -g critical

### 2) Require the package

Next, you'll need to require the package using Composer:

From your project's base path, run:

    $ composer require krisawzm/critical-css --save

### 3) Configure Laravel

#### Service Provider

Add the following to the `providers` key in `config/app.php`:

    Krisawzm\CriticalCss\CriticalCssServiceProvider::class,

#### Console

To get access to the `criticalcss:make` command, add the following to the `$commands` property in `app/Console/Kernel.php`:

    \Krisawzm\CriticalCss\Console\CriticalCssMake::class,

(Optional) You may also add the following line if you need access to the `criticalcss:clear` command:

    \Krisawzm\CriticalCss\Console\CriticalCssClear::class,

#### Config

To get started, you'll need to publish all vendor assets by running:

    $ php artisan vendor:publish

This will create a `config/criticalcss.php` file in your app that you can modify.

Descriptions for the config options are only present in the config file, **not** in this readme.

## Usage

Before getting started, I highly recommend reading through the [`config/criticalcss.php`](src/config/criticalcss.php) file. That will give you a good idea of how this all works.

### Generating critical-path CSS

Providing everything is set up and configured properly, all you need to do in order to generate a fresh set of critical-path CSS files, is running the following command:

    $ php artisan criticalcss:make

This will generate a unique file for each of the URIs (routes) provided.

![CriticalCss](https://i.imgur.com/ZIGgtAz.gif)

### Using critical-path CSS (with Blade templates)

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

And of course, make sure to asynchronously load the full CSS for the page using something like loadCss (https://github.com/filamentgroup/loadCSS).

Full example (using Elixir to generate the URL for the CSS file, which or course is optional):

``` html
<html>
<head>
  ...
  @criticalCss('some/route')
  <script>
    !function(a){"use strict";var b=function(b,c,d){var g,e=a.document,f=e.createElement("link");if(c)g=c;else{var h=(e.body||e.getElementsByTagName("head")[0]).childNodes;g=h[h.length-1]}var i=e.styleSheets;f.rel="stylesheet",f.href=b,f.media="only x",g.parentNode.insertBefore(f,c?g:g.nextSibling);var j=function(a){for(var b=f.href,c=i.length;c--;)if(i[c].href===b)return a();setTimeout(function(){j(a)})};return f.onloadcssdefined=j,j(function(){f.media=d||"all"}),f};"undefined"!=typeof module?module.exports=b:a.loadCSS=b}("undefined"!=typeof global?global:this);
    loadCss(elixir('css/app.css'));
  </script>
</head>
</html>
```

## Todo

- When the next release of Critical is tagged (currently 0.6.0), enable support for inlining images.
