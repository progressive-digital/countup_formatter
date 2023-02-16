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
  }
]
```

And then require this module:
```bash
composer require 'progressive-digital/countup_formatter:^1.0'
```
