<p align="center">
    <img width="100px" src="https://www.chorkleines.com/logo.png" align="center" alt="Chor Kleines Logo"></img>
    <h1 align="center">Kleines Mypage</h1>
    <p align="center">A management system developed for <a href="https://chorkleines.com" target="_blank">Chor Kleines</a>.</p>
    <p align="center">
        <a href="https://github.com/nozomu-y/kleines-mypage/blob/master/LICENSE">
            <img src="https://img.shields.io/github/license/nozomu-y/kleines-mypage" alt="License"></img>
        </a>
        <a href="https://github.com/nozomu-y/kleines-mypage/releases">
            <img src="https://img.shields.io/github/v/release/nozomu-y/kleines-mypage" alt="Latest Stable Version"></img>
        </a>
        <img src="https://img.shields.io/github/repo-size/nozomu-y/kleines-mypage" alt="GitHub repo size"></img>
        <br>
        <a href="https://github.com/nozomu-y/kleines-mypage/issues">
            <img src="https://img.shields.io/github/issues/nozomu-y/kleines-mypage?color=4e73df" alt="GitHub issues"></img>
        </a>
        <a href="https://github.com/nozomu-y/kleines-mypage/pulls">
            <img src="https://img.shields.io/github/issues-pr/nozomu-y/kleines-mypage?color=4e73df" alt="GitHub pull requests"></img>
        </a>
        <img src="https://img.shields.io/github/commits-since/nozomu-y/kleines-mypage/latest/master" alt="GitHub commits since tagged version"></img>
        <br>
        <img src="https://img.shields.io/badge/php-%3E=7.3-777bb4?logo=php&logoColor=FFF&labelColor=777bb4" alt="PHP>=7.3"></img>
        <img src="https://img.shields.io/badge/MySQL-5.7-4479A1?logo=mysql&logoColor=FFF&labelColor=4479A1" alt="MySQL5.7"></img>
        <img src="https://img.shields.io/badge/Bootstrap-4.3.1-563D7C.svg?logo=bootstrap&labelColor=563D7C" alt="Bootstrap"></img>
        <img src="https://img.shields.io/badge/jQuery-3.4.1-0769AD.svg?logo=jquery&labelColor=0769AD" alt="jQuery"></img>
    </p>
</p>


## Requirements 
* PHP >= 7.3
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

