<?php

// In this page, we open the connection to the Database
// In this page, we open the connection to the Database
// Our MySQL database (blueprintdb) for the Blueprint Application
// Function to connect to the DB

class conexiondb {

   public function connectToDB() {
        // These four parameters must be changed dependent on your MySQL settings
        $namedb = 'factorydb'; // MySQL database name
        //$link = mysql_connect ("localhost:3306", "username", "password");
        //$link = mysql_connect ($hostdb, $userdb, $passdb);
        $link = mysql_connect();
    
        if (!$link) {
            // we should have connected, but if any of the above parameters
            // are incorrect or we can't access the DB for some reason,
            // then we will stop execution here
            die('Could not connect: ' . mysql_error());
        }
    
        $db_selected = mysql_select_db($namedb);
        if (!$db_selected) {
            die('Can\'t use database : ' . mysql_error());
        }
        return $link;
    }
}


?>