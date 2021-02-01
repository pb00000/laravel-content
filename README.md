# [WIP] Laravel Content
[https://twitter.com/pascalbaljet/status/1348562877230280709](https://twitter.com/pascalbaljet/status/1348562877230280709)
## Don't use in production yet!

### This package will be a companion to [Form Components Pro](https://formcomponents.pro).

[![Latest Version on Packagist](https://img.shields.io/packagist/v/protonemedia/laravel-content.svg?style=flat-square)](https://packagist.org/packages/protonemedia/laravel-content)
![run-tests](https://github.com/protonemedia/laravel-content/workflows/run-tests/badge.svg)
[![Quality Score](https://img.shields.io/scrutinizer/g/protonemedia/laravel-content.svg?style=flat-square)](https://scrutinizer-ci.com/g/protonemedia/laravel-content)
[![Total Downloads](https://img.shields.io/packagist/dt/protonemedia/laravel-content.svg?style=flat-square)](https://packagist.org/packages/protonemedia/laravel-content)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/protonemedia/laravel-content)

## Requirements

* PHP 7.4+
* Laravel 6.0 and higher
123

## Features

Defining pages:

```php
use ProtoneMedia\LaravelContent\Fields\Html;
use ProtoneMedia\LaravelContent\Fields\Image;
use ProtoneMedia\LaravelContent\Fields\Text;
use ProtoneMedia\LaravelContent\Pages\Page;

class ContactPage extends Page
{
    public function fields(): array
    {
        return [
            'title'    => new Text,
            'header'   => new Image,
            'contents' => new Html,
        ];
    }
}

$field = new Html;
$field = Html::make();

$field = Html::make()
    ->addToRules([new NoJavascriptRule, new NoExternalHostsRule])  // optional
    ->beforeSaving(new HtmlPurifier), // optional
```

Create/update from Request:

```php
$newPageModel = ContactPage::fromRequest()->validate()->saveToModel(new PageModel);

// from request:
$contactPage = ContactPage::fromRequest()
$contactPage = ContactPage::fromRequest($request);

// validation:
$contactPage->validate();
$contactPage->getRules();
$contactPage->getValidator();

// save to database:
$contactPage->saveToModel($yourModel);
$contactPage->saveToModel($yourModel, 'field');

$contactPage->toJson();
```

Fields as Eloquent Casts:

```php
use ProtoneMedia\LaravelContent\Fields\Image;
use ProtoneMedia\LaravelContent\Fields\Markdown;

class BlogPost extends Model
{
    protected $casts = [
        'top_image' => Image::class,
        'contents' => Markdown::class,
    ];
}
```

Standalone fields:

```php
$field = Markdown::fromInput(request('contents'))
    ->withMiddleware(ReplaceOldHostnames::class);

BlogPostModel::first()->update([
    'contents' => $field->getValue()
]);
```

Media fields can have a custom backend:

```php
Image::setDefaultRepositoryResolver(function() {
    return new MediaLibraryRepository;
});
```

Fields can be used in Blade views:

```blade
{{ $page->image }}
```

Which is equal to:

```php
$page->image->toHtml();
```

## Blogpost

...

## Support

We proudly support the community by developing Laravel packages and giving them away for free. Keeping track of issues and pull requests takes time, but we're happy to help! If this package saves you time or if you're relying on it professionally, please consider [supporting the maintenance and development](https://github.com/sponsors/pascalbaljet).

## Installation

You can install the package via composer:

```bash
composer require protonemedia/laravel-content
```

## Usage

...

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information about what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Other Laravel packages

* [`Laravel Analytics Event Tracking`](https://github.com/protonemedia/laravel-analytics-event-tracking): Laravel package to easily send events to Google Analytics.
* [`Laravel Blade On Demand`](https://github.com/protonemedia/laravel-blade-on-demand): Laravel package to compile Blade templates in memory.
* [`Laravel Eloquent Scope as Select`](https://github.com/protonemedia/laravel-eloquent-scope-as-select): Stop duplicating your Eloquent query scopes and constraints in PHP. This package lets you re-use your query scopes and constraints by adding them as a subquery.
* [`Laravel Cross Eloquent Search`](https://github.com/protonemedia/laravel-cross-eloquent-search): Laravel package to search through multiple Eloquent models.
* [`Laravel Eloquent Where Not`](https://github.com/protonemedia/laravel-eloquent-where-not): This Laravel package allows you to flip/invert an Eloquent scope, or really any query constraint.
* [`Laravel FFMpeg`](https://github.com/protonemedia/laravel-ffmpeg): This package provides an integration with FFmpeg for Laravel. The storage of the files is handled by Laravel's Filesystem.
* [`Laravel Form Components`](https://github.com/protonemedia/laravel-form-components): Blade components to rapidly build forms with Tailwind CSS Custom Forms and Bootstrap 4. Supports validation, model binding, default values, translations, includes default vendor styling and fully customizable!
* [`Laravel Mixins`](https://github.com/protonemedia/laravel-mixins): A collection of Laravel goodies.
* [`Laravel Paddle`](https://github.com/protonemedia/laravel-paddle): Paddle.com API integration for Laravel with support for webhooks/events.
* [`Laravel Verify New Email`](https://github.com/protonemedia/laravel-verify-new-email): This package adds support for verifying new email addresses: when a user updates its email address, it won't replace the old one until the new one is verified.
* [`Laravel WebDAV`](https://github.com/protonemedia/laravel-webdav): WebDAV driver for Laravel's Filesystem.

### Security

If you discover any security related issues, please email pascal@protone.media instead of using the issue tracker.

## Credits

- [Pascal Baljet](https://github.com/protonemedia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Treeware

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/pascalbaljetmedia/laravel-content) to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.
