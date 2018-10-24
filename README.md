# Kirby3 byline

This is a plugin for kirby3 that provides field methods to make generating an (author) byline.

- It can cope with a field that either contains a single author or a structured (yaml) list of one or more authors.
- When generating the byline, it adds commas and a penultimate "and".
- It can add links to an authors website, twitter handle, or instagram.
- Provides a default attribution (`Staff Writer`) if the author field is empty.

## Installation

## configuration

#### `config.php`

In your site's `site/config/config.php` the following entries prefixed with `omz13.byline.` can be used:

- `author` - optional - string - default `'Staff Writer'` - the name to be used when the author is unknown.

## fieldMethods

### byline

This plugin provides a field method called `byline` that should be passed a field that contains the author or authors; the field passed can be either a simple field or a structured field and it will automaticlly cope with either.

When there are multiple authors it will return a comma-separated-and list of the authors' names.

For a single author, it will return the author's name.

If the author's details are missing, it will return `"Staff Writer"` (or whatever has been specified by the configuration option `omz13.byline.author`).

This field method is intened for use in a blueprint, for example:

```yaml
info: "{{ page.author.bylineLinked }} - {{ page.date('Y-m-d') }}"
```

### bylineLinked

This field method is similar to the `byline` field method except that each authors' name is 'wrapped' in an html link to their (in order of preference) website, twitter, or instagram (as determined by `website`, `twitter`, or `instagram` fields in a user's blueprint).

The `bylineLinked` field method is intended for use in a template or snippet:

```php
<?= $page->author()->byline() ?>
```

For example, for the Kirby [starterkit](https://github.com/k-next/starterkit), you would change in `snippets/article` the lines:

```php
<?php if ($author = $article->author()->toUser()): ?>
<p class="article-author">by <?= $author->name() ?></p>
<?php endif ?>
```

to:

```php
<p class="article-author">by <?= $article->author()->byline() ?></p>
```

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/omz13/kirby3-feeds/issues/new).

## License

### Buy Me A Coffee

Until kirby3 has been officially released, you are free to use and evaluate this plugin on test or production servers AT YOUR OWN RISK. There is no warranty. Support is at my discretion. Did I mention that I like coffee? This started as a simple plugin and morphed into something quite complex to cope with a combination of trying to get something sensibe out of kirby while also trying to generate something acceptable within the constraints of the different syndication formats. To show your support for this project you are welcome to [buy me a coffee](https://buymeacoff.ee/omz13).
