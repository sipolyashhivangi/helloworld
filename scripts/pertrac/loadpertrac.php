<?php

    include '../config/data_output_config.php';
    //connect to the database
    $connect = mysql_connect("localhost","leapdbadmin",'k$REW;l');
    mysql_select_db("leapscoremeta",$connect); //select the table


    /*
     * Create a backup file of the pertrac table using a select all query and
     * put it into the backup folder.  Recreate the pertrac table if the
     * backup is successful.
    */

    $table_name = "pertrac";
    date_default_timezone_set('UTC');
    $today = date("Y-m-d-H-i-s");
    $backup_file  = $pertrac_data_directory."pertrac".$today.".sql";

    $retval = mysql_query( "SELECT * INTO OUTFILE '$backup_file' FROM $table_name;" );
    if($retval )
    {
        echo "Backed up data successfully\n\r\n\r";

        $retval = mysql_query( "DROP TABLE IF EXISTS $table_name;" );

        mysql_query( "CREATE TABLE IF NOT EXISTS `pertrac` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fundname` varchar(100) DEFAULT NULL,
            `ticker` varchar(20) DEFAULT NULL,
            `itemtype` varchar(50) DEFAULT NULL,
            `category` varchar(50) NOT NULL DEFAULT 'NORMAL',
            `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'active item or not',
            `std_deviation` double DEFAULT NULL COMMENT 'Standard Deviation',
            PRIMARY KEY (`id`),
            KEY `ticker` (`ticker`)
          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;" );

         echo "Created new pertrac table\n\r\n\r";
    }
    else {
        die('Could not take data backup: ' . mysql_error());
    }



    /*
     * Create a list of fundnames and a list of ticker names from the ETF
     * reference file.  The filenames must be consistent
     * and placed in a data folder in order for the substrings
     * for the category and risk factor data to work.
    */
    $etf_handle = fopen("reference/ETF List.csv","r");
    $etf_data = fgetcsv($etf_handle,1000,",");
    $etf_fund_list = array();
    $etf_ticker_list = array();
    do {
        if ($etf_data[0]!= "Fund Name" && trim($etf_data[0])!= "") {
            $etf_fund_list[] = strtoupper(trim($etf_data[0]));
            $etf_ticker_list[] = strtoupper(trim($etf_data[1]));
        }
    } while ($etf_data = fgetcsv($etf_handle,1000,","));



    /*
     * Create a list of fundnames and a list of ticker names from the Non-
     * Correlated reference file.  The filenames must be consistent
     * and placed in a data folder in order for the substrings
     * for the category and risk factor data to work.
    */
    $noncor_handle = fopen("reference/Non-Correlated.csv","r");
    $noncor_data = fgetcsv($noncor_handle,1000,",");
    $noncor_fund_list = array();
    $noncor_ticker_list = array();
    do {
        if ($noncor_data[0]!= "Type" && trim($noncor_data[0])!= "") {
            $noncor_fund_list[] = strtoupper(trim($noncor_data[1]));
            $noncor_ticker_list[] = strtoupper(trim($noncor_data[2]));
        }
    } while ($noncor_data = fgetcsv($noncor_handle,1000,","));


    /*
     * Loop through the asset files in the data directory in order to
     * populate the new pertrac table.  The riskfactor is coded
     * in the filename.  If the ticker is in the ETF list, then
     * the itemtype is "ETF", if the ticker is 5 characters then it is "MF",
     * otherwise the itemtype is a "STOCK". If the ticker or fundname
     * is in the Non-Correlated list, then the category is "NONCOR", otherwise
     * the category is "NORMAL".
    */
    foreach(glob("data/*.csv") as $filename) {
        $file = $filename;
        $handle = fopen($file,"r");
        $data = fgetcsv($handle,1000,",");

        echo "filename: ".$filename."\n\r";
        $count = 0;
        //loop through the csv file ;and insert into database
        do {
            if ($data[0]!= "Type" && trim($data[0])!= "") {            
                $fundname = strtoupper(trim(($data[0])));
                $ticker = strtoupper(trim(($data[1])));
                $type = "";
                $category = "";

                if ( $ticker != "" && in_array($ticker, $etf_ticker_list) ) {
                        $type = "ETF";
                }
                else if ( $ticker == "" && in_array($fundname, $etf_fund_list) ) {
                        $type = "ETF";
                }
                else if ( strlen($ticker) == 5 ) {
                        $type = "MF";
                }
                else {
                    $type = "STOCK";
                }

                if ( $ticker != "" && in_array($ticker, $noncor_ticker_list) ) {
                        $category = "NONCOR";
                }
                else if ( $ticker == "" && in_array($fundname, $noncor_fund_list) ) {
                        $category = "NONCOR";
                }
                else {
                    $category = "NORMAL";
                }

                mysql_query("insert into pertrac (fundname,ticker,
                    itemtype,category,std_deviation)
                    values('".addslashes(trim($data[0]))."','".$ticker."',
                    '".$type."','".$category."','". trim($data[2]) . "')");
            }
            $count++;
        } while ($data = fgetcsv($handle,1000,","));

        echo "count: ".$count."\n\r\n\r\n\r";
    }



?>