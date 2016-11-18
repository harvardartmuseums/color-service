# Color Service

A barebones service for extracting colors from an image.

## Requirements

* PHP w/GD, cURL
* Composer

## Deploying to Heroku

Install the [Heroku Toolbelt](https://toolbelt.heroku.com/).

```sh
$ git clone https://github.com/harvardartmuseums/color-service
$ cd color-service
$ heroku create
$ git push heroku master
$ heroku open
```

## Run locally

```sh
$ php -S localhost:8080 -t web web/index.php
```

## Usage

```sh
curl -XGET http://localhost:8080/extract?image_url=http://ids.lib.harvard.edu/ids/view/18732547
```

## References

This project is based on code from the article [Getting Started with PHP on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php).
