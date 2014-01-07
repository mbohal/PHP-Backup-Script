# PHP Backup Script

Version 1.1 (Jan 7, 2014)

Copyright (C) 2013 by Philipp Rackevei

Licensed under WTFPL License (See LICENSE.md file)

[Available on Github](https://github.com/philipp-r/PHP-Backup-Script "PHP Backup Script on Github")

[Website](http://8qq.de/phil/php-backup/ "PHP Backup Script Website")

## Info

This is a PHP Script which can backup multiple MySQL Databases and Directories to an external FTP Server.
It creates a ZIP-archive of the Directory and of the .sql files and uploads them via FTP.

## Usage

* Upload all files to your web space

* Fill out all required information in the backup-files.cfg.php or backup-mysql.cfg.php files

* Call `backup-files.php?KEY_GET1=randomStuff&KEY_GET2=otherStuff` or `backup-mysql.php?KEY_GET1=randomStuff&KEY_GET2=otherStuff` to create a backup

If you don't want to use the GET Keys to protect the backup files you can comment the lines 5-7 in `backup-files.php` or `backup-mysql.php`.
You should password-protect the backup files with a `.htaccess` file.
