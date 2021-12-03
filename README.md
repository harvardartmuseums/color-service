# Color Service

A barebones service for extracting colors from an image.

This code is a modified version of [Kepler Gelotte's code](http://www.coolphptools.com/color_extract) which is a modified version of  [Csongor Zalatnai's code](http://www.phpclasses.org/browse/package/3370.html).

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

## Get extract

`GET /extract` will extract colors from an image

Query String Parameters

| Parameter | Value | Description |
| :--------- | :----- | :----- |
| image_url | https://YOUR_IMAGE_URL required | Any valid URL that resolves to an image |
| color_count | Any number > 0 (default=10) | The maximum number of colors to include in the output |
| delta | Any number from 1-255 (default=25) | The quantization delta; the smaller the number the more accurate the color |
| reduce_brightness | true OR false (default=true) | Reduce brightness variants of the same color |
| reduce_gradients | true OR false (default=true) | Reduce gradient variants |

### Examples

> curl -XGET https://localhost:8080/extract?image_url=https://ids.lib.harvard.edu/ids/view/8064315&color_count=25  
> Returns a maximum of 25 colors from the image located at https://ids.lib.harvard.edu/ids/view/8064315.

### Response (truncated)

```json
{
    "status": "ok",
    "info": {
        "image_url": "https://ids.lib.harvard.edu/ids/view/8064315",
        "color_count": 25,
        "delta": 25,
        "reduce_brightness": true,
        "reduce_gradients": true
    },
    "colors": [
        {
            "color": "#323219",
            "percent": 0.22785478547854784,
            "hue": "Brown",
            "css3": "#2f4f4f",
            "spectrum": "#3db657"
        },
        {
            "color": "#191919",
            "percent": 0.17570957095709572,
            "hue": "Grey",
            "css3": "#000000",
            "spectrum": "#1eb264"
        },
        {
            "color": "#646464",
            "percent": 0.12613861386138614,
            "hue": "Grey",
            "css3": "#696969",
            "spectrum": "#7866ad"
        }
    ]
}
```

## References

This project is based on code from the article [Getting Started with PHP on Heroku](https://devcenter.heroku.com/articles/getting-started-with-php).
