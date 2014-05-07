/* The following symbols are the API for this software, our database
 * backend for CS 110:

 DBSCRIPT  -- the script that loads the database

 DBFILE -- the file that contains the database

 getDatabase(successCallback) -- loads the database

 saveDatabase(data,successCallback,failureCallback) -- saves the data

 There's more detail below.

*/

/* This constant is the location of the database.  In principle, these JS
 * functions could work with more than one database. */

var DBSCRIPT = 'storeJSON.php';
var DBFILE = 'database.json';

/* Function to load the database for viewing; this function does not lock
   it, so it should always succeed; therefore, it takes just one callback
   function.  That callback function is invoked with the data from the
   database, so that's where you should save it in a global, if desired,
   and format the entries for viewing. */

function getDatabase(successCallback) {
    getDatabaseGeneral(DBSCRIPT,successCallback);
}

/* Function to save the database. If the save succeeds, the first callback
 * is invoked, otherwise the second is.  There
 */

function saveDatabase(data,successCallback,failureCallback) {
    saveDatabaseGeneral(DBSCRIPT,data,successCallback,failureCallback);
}

// ================================================================
/* Internal functions follow.  No need for students to read past here
 * unless they're intellectually curious. */

function ignore (x) {}

// Firefox doesn't mind passing around a built-in like console.log, but
// Chrome objects, so defining an ordinary function to assign gets around
// that problem.

function console_log (x) { console.log(x); }

var sda_debug = console_log;        // or ignore

function getDatabaseGeneral(dbfile,successCallback) {
    var url = DBFILE + '?' + Date.now();  // url is unique and therefore prevents caching
    jQuery.getJSON(url,successCallback)
        .fail(function () {
                alert('Failed to load database. '
                      + 'see error message at top of page');
                $('<p>The database can fail to load if the'
                  + ' page is not readable or '
                  + ' if the contents are not in proper JSON format.'
                  + ' Click this link to <a href="'
                  + DBFILE
                  + '">visit the database</a> and check.')
                    .prependTo('body')
                    .css({'font-size':'large','color':'red'});
            });
}

/* Function to save the database (this gives up the lock).  Takes one
 * callback function, which takes a boolean argument which is true if the
 * save succeeded and false otherwise.  You might use it like this:

 saveDatabaseGeneral(data,
     function (success) {
         if(result) {
             $('#savespan').html('saved').addClass('successfullSave');
         } else {
             alert("Database not saved successfully!  "+
                   "Please reload and try again.");
         }
     }); // end save database callback

*/

function saveDatabaseGeneral(dbfile,data,successCallback,failureCallback) {
    if(!data) {
        // alert, or throw an error?
        alert("No database supplied (or it's empty).  Please check your code");
        return;
    }
    datastring = JSON.stringify(data);
    if(datastring.length == 0 ) {
        alert("Converting the database to a JSON yields an empty string"+
              "Please check the value of the database you are trying to store");
        return;
    }
    data2 = JSON.parse(datastring);
    if( !JSON.equal(data,data2) ) {
        alert("Couldn't understand your database:  "+
              "stringify and re-parse doesn't yield the same thing"+
              "reparsed value is "+deduplicate);
        return;
    }
    jQuery.post(dbfile,
                {'store':true,
                        'database':datastring},
                function (response) {
                    sda_debug("save response is \n"+response);
                    if( response.substring(0,7) == "success" ) {
                        if( typeof(successCallback) == "function" ) {
                            successCallback(response);
                        } else {
                            alert("Successfully saved database, but no success callback function");
                        }
                    } else {
                        sda_debug("saving failed!");
                        if( typeof(failureCallback) == "function" ) {
                            failureCallback(response);
                        } else {
                            alert("Failed to save database, and no failure callback function\n"
                                  +"response was: "+response);
                        }
                    }
                },
                'text')
        .fail(function () { alert('Database saving failed badly.'); 
                failureCallBack("unknown failure; look in Firebug console");});
}    

// Supporting functions.  This first one should be put in another file
// with a ton of test cases.  Nevertheless, it seems to work.

if(!JSON.equal) {
    JSON.equal =
        function equal(x,y) {
            var whatis = Object.prototype.toString;
            var objectlen =
                function (obj) {
                    var p, len = 0;
                    for( p in obj ) len++;
                    return len;
                };

            xtype = whatis.call(x);
            ytype = whatis.call(y);
            if( xtype != ytype ) return false;
            // for the non-compositional types:
            if( xtype == "[object String]" ||
                xtype == "[object Number]" ||
                xtype == "[object Boolean]" )
                return x == y;
            // for the compositional types:
            if( xtype == "[object Array]" ) {
                if( x.length != y.length ) return false;
                var i = 0, len = x.length;
                for( i = 0; i < len; i++ ) {
                    result = JSON.equal(x[i],y[i])
                    if(!result) return false;
                }
                return true;
            }
            if( xtype == "[object Object]" ) {
                var xlen = objectlen(x);
                var ylen = objectlen(y);
                if( xlen != ylen ) return false;
                // Recursively compare.  y should have same properties as
                // x and all the same values.
                var p;
                for( p in x ) {
                    // We could let the recursion handle this, since the
                    // whatis function will return "[object Undefined]"
                    // for a missing property, but I'd rather check
                    // directly for a missing property.
                    if( typeof y[p] === "undefined" ) return false;
                    if( ! equal(x[p],y[p]) ) return false;
                }
                return true;
            }
            // Shouldn't get to here, I think
            alert("couldn't compare elements "+x+" and "+y);
        }
}

// ================================================================

