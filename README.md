### Usage ###
- ```$ composer require anatolygerasimov/code --dev```
- ```$ composer require psalm/plugin-laravel```
- add folder configuration for ```composer.json```
  ```json
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
      },
      "cs-fixer": {
        "paths": [
          "/app/Containers",
          "/config",
          "/database"
        ],
        "skip": [
          "/bootstrap",
          "/public",
          "/resources",
          "/node_modules",
          "/public",
          "/storage",
          "/vendor"        
        ]
      },
      "psalm": {
        "paths": [
          "/app/Containers"
        ],
        "skip": [
          "/vendor"
        ],
        "config": {
          "plugins": {
            "pluginClass": [
              {"class": "Psalm\\LaravelPlugin\\Plugin"}
            ]
          }
        }
      }
    }
  }
  ```
- ```composer.json```
  ```json
  "scripts": {
    "rector": [
      "./vendor/bin/rector process --config=./vendor/anatolygerasimov/code/configs/rector.php  --clear-cache"
    ],
    "rector-check": [
      "./vendor/bin/rector process --config=./vendor/anatolygerasimov/code/configs/rector.php  --clear-cache --dry-run"
    ],
    "php-cs-fixer": [
      "./vendor/bin/php-cs-fixer fix --config=./vendor/anatolygerasimov/code/configs/.php_cs.dist.php --allow-risky=yes --using-cache=no"
    ],
    "php-cs-fixer-check": [
      "./vendor/bin/php-cs-fixer fix --dry-run --config=./vendor/anatolygerasimov/code/configs/.php_cs.dist.php --diff -vv --allow-risky=yes --using-cache=no"
    ],
    "psalm": [
      "php src/psalm_config.php --target=./psalm.xml",
      "./vendor/bin/psalm --threads=4 --no-cache --config=./psalm.xml",
      "rm ./psalm.xml"
    ]
  }
  ```

### TODO: ###
- runner for this tools