# Wright

A static site generator written in PHP.

## PHP Dependencies

Wright requires PHP 5.5 or greater, and SQLite3 which should be enabled by default in PHP.

## Composer.json

Wright is not on Packagist yet. Add it to your composer.json with the following.

``` json
"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/erickmerchant/wright"
    }
],
"require": {
    "erickmerchant/wright": "*"
}
```

## Command-line

Make sure ./vender/bin/wright is executable. You can symlink it to ./wright. Then run `./wright` to get a list of available commands, their arguments and options. You'll run `./wright publish` to publish your site. When you do, it will appear in a new directory called site, ready to be deployed.

Please see [wright-blog](http://github.com/erickmerchant/wright-blog) for an example of how to set up a site.
