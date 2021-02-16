# Kleines Mypage

![License](https://img.shields.io/github/license/nozomu-y/kleines-mypage)
![Latest Stable Version](https://img.shields.io/github/v/release/nozomu-y/kleines-mypage)

Kleines Mypage is a management system developed for Chor Kleines.


## Requirements 
* PHP 7
* MySQL 5.7

## Installation

### Libraries

- [googleapis / google-api-php-client](https://github.com/googleapis/google-api-php-client)
- [PHPOffice / PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
- [donatj / PhpUserAgent](https://github.com/donatj/PhpUserAgent)

To install all dependencies, execute the following command in the root directory.

```sh
composer install
```

### Google API

Enable the Gmail API from the following link. (Select Web Server)  
https://developers.google.com/gmail/api/quickstart/php

Download `credentials.json` and place it in the `googleapi/` directory.  
In the `googleapi` directory, execute the following command.

```sh
php quickstart.php
```

### Edit config file

Edit `Core/config_template.php` and save it as `Core/config.php`.

### Initialize database

Execute the following command in the root directory.

```sh
php initialize.php
```

