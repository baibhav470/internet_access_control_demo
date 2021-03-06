<?php
/**
 *  Test 9.5:  MySQL analyse query:  Gets the 5 most visited domains by month
 *  First the 10 highest numeric values for hit will be get, and then all the users for this values will be retourned.
 *  
 *  This queries are done over the InternetAccessLogs database directly, which contains example data for 90 millions of records for Non FTP access logs and 4,5 millions of record for FTP access logs
 *  This script makes part of a list of scripts to compare MySQL 5.0.26 with MyISAM tables versus MongoDB 2.2.0rc0.
 *
 *  @author Jos� Manuel Ciges Regueiro <jmanuel@ciges.net>, Web page @link http://www.ciges.net
 *  @license GNU GPLv3 @link http://www.gnu.org/copyleft/gpl.html
 *  @version 20120831
 */
set_include_path(get_include_path() . PATH_SEPARATOR . "classes");
require_once("MySQLRandomElements.class.php");

$mre = new MySQLRandomElements("mysqldb", "mysqldb", "localhost", "InternetAccessLog");
// Search the distinct months stored 
$query = "select distinct(month(datetime)) as month from NonFTP_Access_log order by month";
$results_months = $mre->getResults($query);
        
while($row_month = $results_months->fetch_assoc())  {
    $n = $row_month['month'];
    printf("MES: %d\n", $n);
    // First, we get the minimum value of the 5 highest visits per domain for each month
    $query = "select * from (select distinct(count(*)) as visits from NonFTP_Access_log where month(datetime)=".$n." group by domain order by visits desc limit 5) as topten_visits_by_domain order by visits limit 1";
    $min_value = $mre->getOne($query);
    
    // Now, we obtain all the domains of this month with at lest that value
    $query = "select * from (select month(datetime) as month, domain, count(*) as visits from NonFTP_Access_log group by month, domain having month = ".$n.") as visits_by_domain where visits >= ".$min_value;
    $results = $mre->getResults($query);
    while($row = $results->fetch_assoc())   {
        print_r($row);
        }
    }
 
?>