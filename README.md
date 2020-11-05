# kleines-mypage

Management system developed for Chor Kleines.

## Installation

### Libraries

- [googleapis / google-api-php-client](https://github.com/googleapis/google-api-php-client)
- [PHPOffice / PHPExcel](https://github.com/PHPOffice/PHPExcel)
- [donatj / PhpUserAgent](https://github.com/donatj/PhpUserAgent)

To install all dependencies, execute the following command in your terminal.

```
composer install
```

### Google API

Please enable the Gmail API from the following link. (Select Web Server)  
https://developers.google.com/gmail/api/quickstart/php

Place the downloaded `credentials.json` in the `googleapi` folder.

In the `googleapi` folder, execute the following command.

```
php quickstart.php
```

### Edit config file

Edit `config_template.php` in the `Core` folder and rename it to `config.php`.

### Initialize database

Execute the following command in the root folder.

```
php initialize.php
```

This will create the tables in the MySQL database.
