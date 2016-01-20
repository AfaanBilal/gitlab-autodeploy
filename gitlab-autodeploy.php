<?php
/*
 *  GitLab Deploy
 *  https://afaan.ml/gitlab-autodeploy
 *
 *  Automatically deploy your web app repositories on "git push" or any other hook.  
 *
 *  Author: Afaan Bilal
 *  Author URL: https://google.com/+AfaanBilal
 *  
 *  - No Shell Access Required
 *  - Best for Shared Hosting Platforms
 *  - Both public, internal and private repositories.   
 * 
 *  (c) 2016 Afaan Bilal
 *
 */

// GitLab Repo ID
define('REPO_ID', 0);

// GitLab Private Token
define('PRIVATE_TOKEN', '[PRIVATE_TOKEN]');

// Deploy Directory (RELATIVE TO THIS FILE)
define('DEPLOY_DIR', '[DEPLOY_DIR]');

// LOG ? (FALSE or 'filename')
define('LOGFILE', 'gitlab-autodeploy.log');

// TimeZone (for LOG)
date_default_timezone_set("Asia/Kolkata");

function writeLog($data)
{
    if (LOGFILE == FALSE)
        return;
    
    if (!file_exists(LOGFILE))
    {
        $logFile = fopen(LOGFILE, "a+");
        fwrite($logFile, "--------------------------------------------------------\n");
        fwrite($logFile, "|   PHP GitLab AUTO-DEPLOY                             |\n");
        fwrite($logFile, "|   https://afaan.ml/gitlab-autodeploy                 |\n");
        fwrite($logFile, "|   (c) Afaan Bilal ( https://google.com/+AfaanBilal ) |\n");
        fwrite($logFile, "--------------------------------------------------------\n");
        fwrite($logFile, "\n\n");
        fclose($logFile);
    }
    
    $fh = fopen(LOGFILE, "a+");
    fwrite($fh, "\nTimestamp: ".date("d-m-Y h:i:s a"));
    fwrite($fh, "\n\n {$data}");
    fwrite($fh, "\n\n");
    fclose($fh);
}

function ExtractZip($zipFile, $extractTo)
{    
    $zip = new ZipArchive;
    
    if ($zip->open($zipFile) === TRUE) 
    {
        $zip->extractTo($extractTo);
        $zip->close();
        return TRUE;
    }
    else
        return FALSE;
}

function recursiveRemoveDirectory($directory)
{
    foreach(glob("{$directory}/{,.}*", GLOB_BRACE) as $file)
    {
        if(is_dir($file))
            recursiveRemoveDirectory($file);
        else
            unlink($file);
    }
    
    rmdir($directory);
}

function copyr($source, $dest) 
{ 
    // Simple copy for a file 
    if (is_file($source)) 
    {
        //chmod($dest, 0777);
        return copy($source, $dest); 
    } 

    // Make destination directory 
    if (!is_dir($dest))  
        mkdir($dest);

    chmod($dest, 0777);

    // Loop through the folder 
    $dir = dir($source); 
    while (FALSE !== $entry = $dir->read()) 
    { 
        // Skip pointers 
        if ($entry == '.' || $entry == '..')
            continue;

        // Deep copy directories 
        if ($dest !== "$source/$entry")
            copyr("$source/$entry", "$dest/$entry");
    }

    // Clean up 
    $dir->close(); 
    return TRUE; 
}

function recursiveMoveDirectory($src, $dest)
{
    if (copyr($src, $dest))
    {
        recursiveRemoveDirectory($src);
        return TRUE;
    }
    
    return FALSE;
}

echo "Deploying...<br>";

$ProjectData = json_decode( file_get_contents("https://gitlab.com/api/v3/projects/".REPO_ID."?private_token=".PRIVATE_TOKEN), true );
if ( in_array("message", array_keys($ProjectData)) )
{
    writeLog("Error: ".$ProjectData['message']);
    exit;
}

$RepositoryWebURL = explode('/', $ProjectData['web_url']);
$RepositoryName   = $RepositoryWebURL[ count( $RepositoryWebURL ) - 1 ];

$zipRepo  = "https://gitlab.com/api/v3/projects/".REPO_ID."/repository/archive.zip?private_token=".PRIVATE_TOKEN; 
$zipLocal = "{$RepositoryName}.zip";
  
if (!file_exists( DEPLOY_DIR ))
{
    mkdir( DEPLOY_DIR , 0777 );
}
else 
{
    chmod( DEPLOY_DIR, 0777 );
    recursiveRemoveDirectory( DEPLOY_DIR );
    mkdir( DEPLOY_DIR , 0777 );
}

// Download the latest zip
copy($zipRepo, $zipLocal);

$tempDir = uniqid("temp");
$logStr = "";

// Deploy
if ( ExtractZip($zipLocal, $tempDir) )
{
    if ( recursiveMoveDirectory( glob("{$tempDir}/{$RepositoryName}*")[0], DEPLOY_DIR ) )
        $logStr = "Deployed {$RepositoryName} to ".DEPLOY_DIR;
    else
        $logStr = "Error: Failed on MOVE";
}
else
    $logStr = "Error: Failed on ExtractZip";

    
writeLog($logStr);
echo $logStr;

// Clean up
unlink ($zipLocal);
chmod($tempDir, 0777);
recursiveRemoveDirectory($tempDir);

?>
