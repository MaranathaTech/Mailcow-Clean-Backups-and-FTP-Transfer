# Mailcow-Clean-Backups-and-FTP-Transfer
Script that cleans a Mailcow server's backup folder of old, space consuming backups and transfers them to the FTP server of your choosing. The script is set up to keep 7 days worth of backups on your FTP server and keep your Mailcow server clean of large backup files.


## Getting Started

First, you will need to use the built-in mailcow backup script to generate your compressed database files. 

```bash
MAILCOW_BACKUP_LOCATION=/opt/backup /opt/mailcow-dockerized/helper-scripts/backup_and_restore.sh backup all
```
You will need to set this command to run as a cron task on a daily basis.




Next, upload this script to your server and modify the following variables at the top of the script:

FTP SERVER SETTINGS:
```php
$ftp_server = "ftp.yourserver.com";
$ftp_username = "user";
$ftp_userpass = "password";
$ftpDir = "backups/mail-cow"; //(This is the directory where the backup folder/files should be created/uploaded)
 ``` 
MAILCOW SERVER SETTINGS:  
```php
$backupsDir = "/opt/backup/";
 ```   
 
 
 
Finally, set the following command to run as a cron task on a daily basis to execute this script daily:
```bash
php /the-dir-you-uploaded-the-script-to/clean-mailcow-backups-and-transfer.php
```


## Additional Info on Mailcow Backup Generation
https://mailcow.github.io/mailcow-dockerized-docs/b_n_r_backup/


## Additional Info on Setting Up Cron Tasks
https://help.ubuntu.com/community/CronHowto


## License

This project is licensed under the MIT License


