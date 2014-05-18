# Wright

A static site generator written in PHP.

## PHP Dependencies

Wright requires PHP 5.5 or greater, and SQLite3 which should be enabled by default in PHP.

Wright allows relationships between data, so when publishing by first writing everything into SQLite it can stay performant.

## CLI Commands

Make sure ./vender/bin/wright is executable. You can symlink it to ./wright. Then run `./wright` to get a list of available commands, their arguments and options. You'll run `./wright publish` to publish your site. When you do, it will appear in a new directory called site, ready to be launched.

## Getting Started

You should check out [wright-skeleton](http://github.com/erickmerchant/wright-skeleton) for an example of how to set up a site.

At the bare minimum you'll need the right directory structure to get started. Make the following directories in what ever directory you've installed Wright into.

### base/

The contents of the base directory are copied over to site/ without modification when publishing.

### data/

The data directory is where your content lives. You use `./wright make` to add files here and then edit them in your editor of choice.

### settings/

Settings for defaults, old permalinks, and your sitemap.

### templates/

Your templates. Wright uses Twig. Check out their [docs](http://twig.sensiolabs.org/documentation) to learn more.

### includes/ (optional)

The includes directory contains PHP files that set optional extensions, converters, generators, and middleware.
