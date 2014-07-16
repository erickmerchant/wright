# Wright

A static site generator written in PHP.

This README is a work in progress, but should serve as the primary documentation for Wright.

## Dependencies

Wright requires PHP 5.5 or greater, and SQLite3 which should be enabled by default in PHP.

Wright allows relationships between data, so when publishing by first writing everything into SQLite it can stay performant.

## Getting Started

You should check out [wright-skeleton](http://github.com/erickmerchant/wright-skeleton) for an example of how to set up a site.

At the bare minimum you'll need the right directory structure, a sitemap settings file, and a template to get started.

## The CLI

Make sure ./vendor/bin/wright is executable. You can symlink it to ./wright. Then run `./wright` to get a list of available commands, their arguments and options. Run `./wright [name-of-command] --help` to get help with any command.

Anywhere a directory (also referred to as a collection) or path is called for always include the full path from the current directory. In other words use `data/posts/` instead of `posts/`. This allows you to use tab completion.

### Commands

`./wright publish`

Publishes your site. It looks at settings/sitemap.yml and creates pages based on it, putting them in site/.

`./wright make`

Used to add a new file to data/.

`./wright move`

Used to move a file from one place in data to another place. Think of moving a draft from data/drafts/ to data/posts/.

## Directories

### base/

The contents of the base directory are copied over to site/ without modification when publishing.

### data/

The data directory is where your content lives. You use `./wright make` to add files here and then edit them in your editor of choice.

### settings/

Settings for defaults and your sitemap. (see Settings below)

### templates/

Your templates. Wright uses Twig. Check out [its docs](http://twig.sensiolabs.org/documentation) to learn more.

### includes/

The includes directory contains PHP files that set extensions, converters, hooks, and middleware. The only thing required is one converter.

## Settings

### sitemap.yml

The sitemap settings file tells Wright what pages to create when it publishes your site. It consists of a mapping. Every item is a route with some settings.

This is a portion of the sitemap from the [wright-skeleton](https://github.com/erickmerchant/wright-skeleton)

```yaml
/posts/:
    data: posts
    template: posts
/posts/:slug/:
    data: posts/*
    template: post
```

The first route is straight forward. It tells Wright to grab the data at data/posts.md and feed it to the template called posts. The second route is a bit more complex. `posts/*` tells Wright to use that route for every post, each entry in the posts directory. For each post `:slug` will get expanded or replaced with the slug from that post.
