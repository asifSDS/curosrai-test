#!/usr/bin/env php
<?php
/**
 * This script will download and install https://github.com/pantheon-systems/wp-native-php-sessions
 * on your Pantheon Wordpress site.
 * 
 * Works on Macintosh / Linux.
 * 
 * Usage:
 *
 *   terminus auth login
 *   php add-php-sessions-plugin.php my-site-name [env]
 *
 */
$self=array_shift($argv);
$sftp_port="2222";
$site="";
$env="dev";
$pantheon_filename="v0.5.0";
$pantheon_sessions_version="0.5.0";
while (count($argv) > 0) {
  $arg = array_shift($argv);
  if ($arg[0] == '-') {
    switch ($arg) {
      case "--port":
        $sftp_port = array_shift($argv);
        break;
      case "--pantheon-sessions-version":
        $pantheon_sessions_version = array_shift($argv);
        break;
    }
  }
  else {
    if ($site == "") {
      $site = $arg;
    }
    else {
      // Generally, you should only use 'dev' or a multi-dev branch here.
      $env = $arg;
    }
  }
}
function tempdir($dir=FALSE, $prefix='') {
    $tempfile=tempnam($dir ? $dir : sys_get_temp_dir(), $prefix);
    if (file_exists($tempfile)) { unlink($tempfile); }
    mkdir($tempfile);
    if (is_dir($tempfile)) { return $tempfile; }
}
if (empty($site)) {
  print "Usage: $self SITE [ENV]";
  exit(1);
}
// Get the site UUID
$uuid = exec("terminus site info --site=$site --field=id");
// Write the wp-native-php-sessions code to the site with
// the sftp command line tool, and commit it with terminus.
$sftp_host="dev.$uuid@appserver.dev.$uuid.drush.in";
// Make sure the site is awake
exec("terminus site wake --site=$site --env=dev");
$mu_plugins_path = 'wp-content/mu-plugins';
$work_dir = tempdir();
chdir($work_dir);
//
// We are going to write the pantheon sessions loader into wp-content/mu-plugins
//
$pantheon_sessions_loader = "<?php
require_once(__DIR__ . '/wp-native-php-sessions/pantheon-sessions.php');";
file_put_contents('00-pantheon-sessions.php', $pantheon_sessions_loader);
//
// Download the pantheon-sessions plugin; we are going to write it to wp-content/mu-plugins as well
//
exec("curl -OL https://github.com/pantheon-systems/wp-native-php-sessions/archive/$pantheon_filename.zip");
rename("$pantheon_filename.zip", "wp-native-php-sessions.zip");
exec('unzip wp-native-php-sessions.zip');
rename("wp-native-php-sessions-$pantheon_sessions_version", 'wp-native-php-sessions');
//
// Make a batch file for the sftp tool
//
$sftp_batch_contents = "cd code/wp-content/mu-plugins
put 00-pantheon-sessions.php
put -r wp-native-php-sessions";
$sftp_batch_file="pantheon-sessions-upload.batch";
file_put_contents($sftp_batch_file, $sftp_batch_contents);
// We need to be in sftp mode to write the file
passthru("terminus site set-connection-mode --site=\"$site\" --env=\"$env\" --mode=sftp");
// Use the sftp cli tool to upload 00-pantheon-sessions.php and wp-native-php-sessions
passthru("sftp -o Port=\"$sftp_port\" -b \"$sftp_batch_file\" \"$sftp_host\"");
// Commit it to the database.
passthru("terminus site code commit --site=\"$site\" --env=\"$env\" --message=\"Add wp-native-php-sessions/pantheon-sessions to the must-use plugins directory.\" --yes");
