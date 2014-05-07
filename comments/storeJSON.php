<?php

  /* Here are the constants that you, the database owner/manager, might want to customize:
   */

define('DB_FILE','database.json');   // the name of the database file
define('DB_MAX_SIZE',1e9);           // the maximum size of the database, in bytes
define('DB_MAX_VERSIONS',100);       // maximum number of saved versions

/*

This version does not lock the database.

Manage a database consisting of a single JSON object (typically an array
of object, e.g. of people signing up to attend an event).

The basic idea is that the user downloads a complete copy of the database,
makes whatever changes are desired completely within the browser, and then
uploads the modified database, replacing the older version.  Of course,
there are many technical details, which we'll get into now.

The database is in a JSON file called database.json.  (See
user-customizable parameter above.)

If you want read-only access to the file, just read it, bypassing this
script entirely.

Note that, to avoid malicious destruction of the database, this script
never deletes the database.  It renames the existing database to another
filename.  So, you can always recover a previous version of the database.

================================================================

What this file does is allow you to *save* new versions of the database.  For
this version of the implementation, I'll use the POST parameter named
'database', but in the long run I'll want to use file upload techniques,
so that there aren't any length limitations (other than what's imposed by
this script) (TODO: implement file upload.  implement length restriction)

================================================================

Another possible attack is to upload so many versions of the database that
the server runs out of space.  At that point, we would either have to (1)
delete older versions to allow continued upload, or (2) disallow new
uploads.  (TODO: make this a user-specified choice.)  For now, we will
disallow further updates.  Ideally, this script would determine the amount
of disk-space remaining (in the owner's quota or on the disk partition),
but that's too difficult.  Instead, we will set a limit on the number of
versions that can be saved.  (TODO: make this a parameter.)

================================================================

In summary, this file has just one necessary use case:

1. save the whole database, in which case the return value is again either
'success' or 'failure'.  Do this by sending in a store=true and
database=<JSON> request.  Again, any value will do; we just look for the
parameter.  If the saving is successful, the (default) response will be
"success\n"

It also has other use cases:

2. OK.  This checks to see if things are configured properly, such as
write permissions.  NYI

================================================================

# Written by Scott D. Anderson
# scott.anderson@acm.org
# April 2013

*/

// ================================================================
// You probably don't want to modify any of these

define('RESULT_FAILURE',"failure\n");   // code returned when request fails
define('RESULT_SUCCESS',"success\n");   // code returned when request succeeds
define('STORE_PARAMETER','store');    // REQUEST parameter 
define('DB_PARAMETER','database');    // REQUEST parameter 
define('DEBUG_PARAMETER','debug');    // REQUEST parameter 

if( !date_default_timezone_set ("America/New_York") ) {
    die("Failed to set default timezone; did you mispell something?");
}

$debug = true;

if( isset($_REQUEST[DEBUG_PARAMETER]) ) {
    $debug = true;
}

if( isset($_REQUEST[STORE_PARAMETER])) {
    process_store_request();
} else if( isset($_REQUEST['ok'])) {
    // this is just a test of whether the script is runnable
    // eventually, check configuration (writability of directory)
    success_exit();
} else if( isset($_REQUEST['download'])) {
    // Thanks to http://stackoverflow.com/questions/6321307/how-to-download-a-php-file-without-executing-it
    $filename = 'storeJSON.php';
    ob_end_clean();
    header("Content-type: application/octet-stream; ");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize($filename) . ";");
    header("Content-disposition: attachment; filename=" . $filename);
    readfile($filename);
    die();
} else {
    print_usage_note();
}

exit(0);

// ================================================================
// Main API functions

function process_store_request() {
    global $debug;
    store_database();
    success_exit();
}

// ================================================================
/* Debug output.  We need to be sure that when we put in debugging
statements, they get appended to the script output, rather than just
printed.  It's important to put them in the script output, because our
users will not necessarily have access to system logs.

I'll add output to our system logs, as well.

*/

$debug_messages = '';

function debug_output($message) {
    global $debug, $debug_messages;
    if( !$debug ) return;
    $debug_messages .= $message;
    if( ! error_log($message) ) {
        $debug_messages .= "Could not use system error_log.";
    }
}

// We might want to use register_shutdown_function, in case we don't call
// one of these functions.

function success_exit() {
    global $debug, $debug_messages;
    $response = RESULT_SUCCESS;
    if( $debug ) {
        $response .= $debug_messages;
    }
    print $response;
    exit(0);
}
        
function failure_exit($reason) {
    global $debug, $debug_messages;
    $response = RESULT_FAILURE . $reason;
    if( $debug ) {
        $response .= $debug_messages;
    }
    print $response;
    exit(1);
}

// TODO: this doesn't yet store backup versions.  It also doesn't check
// the size of the file or the number of versions.  Also, should
// syntax-check the new database

function store_database() { 
    if( ! isset($_REQUEST[DB_PARAMETER]) ) {
        return ("<p>No database supplied. " .
                "Use parameter '" . DB_PARAMETER . "' in the request\n");
    }
    return overwrite_file(DB_FILE,$_REQUEST[DB_PARAMETER]);
}

/* Overwrites the named file with the given data.  Returns true on success
   and false otherwise, printing the failure reason to stdout. */

function overwrite_file($file,$data) { 
    global $debug;
    if( file_exists($file) && !is_writable($file) ) {
        failure_exit("Failed to overwrite database file\n"
                     . "$file is not writable\n"
                     . "The website manager should (probably) make it (the file and the directory) \n"
                     . "world-writable using an FTP client.");
    }
    if( !$handle = fopen($file,'wb') ) {
        failure_exit("Failed to overwrite database file\n"
                     . "Couldn't open $file for writing\n"
                     . "The website manager should (probably) make it (the file and the directory) \n"
                     . "world-writable using an FTP client.");
    }
    if( strlen($data) == 0 ) {
        failure_exit("Refusing to overwrite the database with empty string\n");
    } else {
        debug_output("<p>Writing $data to $file\n");
    }
    if( fwrite($handle,$data) === FALSE ) {
        failure_exit("Couldn't overwrite $file for some reason, not sure why\n");
    }
    fflush($handle);
    fclose($handle);
    return true;
}

function print_usage_note() {
    print "<p>Incorrect usage. \n";
    print "<p>Sorry, this message needs updating, so no more information is available at this time.\n";
}
