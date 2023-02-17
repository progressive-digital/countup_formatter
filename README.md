# countup_formatter
Provides a field formatter that integrates the countUp.js library.

## How to include it in your Drupal project
Add the repository to your composer.json like this:
```json
"repositories": [
  {
      "type": "composer",
      "url": "https://packages.drupal.org/8"
  },
  {
    "type": "git",
    "url": "https://github.com/progressive-digital/countup_formatter.git"
  },
  {
    "type": "package",
    "package": {
      "name": "inorganik/countup-js",
      "version": "2.4.2",
      "type": "drupal-library",
      "dist": {
        "url": "https://github.com/inorganik/countUp.js/archive/refs/tags/v2.4.2.zip",
        "type": "zip"
      }
    }
  }
]
```

And then require this module and the needed library:
```bash
composer require 'progressive-digital/countup_formatter:^1.0' 'inorganik/countup-js'
```
