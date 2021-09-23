### Usage ###
- ```$ composer require anatolygerasimov/code --dev```
- add folder configuration for ```composer.json```
  ```
  "extra": {
    "code": {
      "rector": {
        "paths": [
          "/app/Containers/AppSection"
        ],
        "skip": [
          "/app/Ship/Migrations/*",
          "/app/Ship/Core/*",
          "/app/Containers/*/Data/Migrations/*",
          "/app/Containers/*/Data/Criterias/*",
          "/app/Containers/*/Routes/*"
        ]
      }
    }
  }
  ```
- ```$ ./vendor/bin/rector process --config=./vendor/anatolygerasimov/code/configs/rector.php  --clear-cache```
- ```$ ./vendor/bin/rector process --config=./vendor/anatolygerasimov/code/configs/rector.php  --clear-cache --dry-run```

### TODO: ###
- ```"friendsofphp/php-cs-fixer": "^2.18"```
- ```"vimeo/psalm": "^4.6"```
- runner for this tools