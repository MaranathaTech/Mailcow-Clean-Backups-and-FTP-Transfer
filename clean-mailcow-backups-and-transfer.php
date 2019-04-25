<?php

//setup a cron task to run this task daily to transfer your mail cow backups to another server via FTP
    
    
//your ftp site settings
$ftp_server = "ftp.yourserver.com";
$ftp_username = "user";
$ftp_userpass = "password";
$ftpDir = "backups/mail-cow"; //directory where the backup file should be uploaded
    
    
    
//the directory on your mailcow server that the script scans for .tar.gz files
$backupsDir = "/opt/backup/";
    
//clean up backup files
$files = glob($backupsDir.'*', GLOB_ONLYDIR);

$deletions = $files;

foreach($deletions as $to_delete) {
    $name = str_replace($backupsDir,"",$to_delete);
    echo "\nName: ".$name."Dir: ".$to_delete."\n";
    exec('tar -cvf '.$to_delete.'.tar.gz '.$to_delete.'');
    $files = glob($to_delete."/*");
    
        foreach($files as $file){
            unlink($file);
        }

    $deleted = rmdir($to_delete);
}



// connect and login to FTP server
$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
$login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);
ftp_pasv($ftp_conn, true) or die("Unable switch to passive mode");

    
// try to change the directory
if (ftp_chdir($ftp_conn, $ftpDir)) {
    echo "Current directory is now: " . ftp_pwd($ftp_conn) . "\n";
} else { 
    die( "Couldn't change directory\n");
}


//save to a dated folder
$original_directory = ftp_pwd($ftp_conn);
$dateDir = date("Y-m-d");

// try to create the directory $dateDir
if (ftp_mkdir($ftp_conn, $dateDir)) {
 echo "successfully created $dateDir\n";
} else {
 echo "There was a problem while creating $dateDir\n";
}


// try to change the directory to $dateDir
if (ftp_chdir($ftp_conn, $dateDir)) {
    echo "Current directory is now: " . ftp_pwd($ftp_conn) . "\n";
} else { 
    die( "Couldn't change directory\n");
}


//loop through sql directory and post file to FTP
foreach (glob($backupsDir."*.tar.gz") as $file) {
   
    $filename = str_replace($backupsDir,"",$file);
    echo "Filename: $filename \n";
    echo "$filename size " . filesize($file) . "\n";

    // upload file
    if (ftp_put($ftp_conn, $filename, $file, FTP_ASCII))
    {
        echo "Successfully uploaded $file.";
        unlink($file);
    }
    else
    {
        echo "Error uploading $file.";
    }

}


// try to change the directory to main backups dir
if (ftp_chdir($ftp_conn, $original_directory)) {
    echo "Current directory is now: " . ftp_pwd($ftp_conn) . "\n";
} else { 
    die( "Couldn't change directory\n");
}



//clean old backups off the FTP Server
    
// set the directory for 7 days ago
$oldDir = date('Y-m-d', strtotime('-7 days'));
// try to change the directory to that old backup dir
if (ftp_chdir($ftp_conn, $oldDir)) {
    echo "Current directory is now: " . ftp_pwd($ftp_conn) . "\n";
} else { 
    die( "Couldn't change directory\n");
}

//delete all files within that directory
$files = ftp_nlist($ftp_conn, ".");
foreach ($files as $file)
{
    ftp_delete($ftp_conn, $file);
}   

// try to change back to the directory to main backups dir
if (ftp_chdir($ftp_conn, $original_directory)) {
    echo "Current directory is now: " . ftp_pwd($ftp_conn) . "\n";
} else { 
    die( "Couldn't change directory\n");
}

//delete the old backup dir
if (ftp_rmdir($ftp_conn, $oldDir)) {
    echo "Successfully deleted $oldDir\n";
} else {
    echo "There was a problem while deleting $oldDir\n";
}



// close connection to the FTP server
ftp_close($ftp_conn);
    
?>
