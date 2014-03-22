# PHP Backup Script

Version 1.3

Copyright (C) 2014 by Philipp Rackevei

Licensed under GNU GPL v3 License (See LICENSE.md file)

[Available on Github](https://github.com/philipp-r/PHP-Backup-Script "PHP Backup Script on Github")


## Info

This is a PHP Script which can backup multiple MySQL Databases and Directories to an external FTP Server.

It creates a ZIP-archive of the Directory and of the .sql files and uploads them via FTP.

You can also send the backups to your email address using the [PHPMailer](https://github.com/PHPMailer/PHPMailer) class

## Usage

* Upload all files to your web space

* Fill out all required information in the backup-files.cfg.php and backup-mysql.cfg.php files

* Call `backup-files.php?KEY_GET1=randomStuff&KEY_GET2=otherStuff` or `backup-mysql.php?KEY_GET1=randomStuff&KEY_GET2=otherStuff` to create a backup

If you don't want to use the GET Keys to protect the backup files you can comment the lines 7-9 in `backup-files.php` or `backup-mysql.php`.
You should password-protect the backup files with a `.htaccess` file.
