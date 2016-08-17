<?php
if($secure==1){

######## GENERAL INFORMATION ########

$stat_name = 'CK Stats';		# Stats page name
$group_name = ''; # Name of your steam group (Optional)
$group_url = ''; # URL to your Steam group or Website
$local_timezone = 'EST'; # Set your local timezone here
$conf_language = 'eng'; # Only supports English right now
$conf_record_stats = '1'; # How do you want profile stats to be displayed? 0 = No records, 1 = all in one, 2 = Record stats are displayed different from map stats

######### DATABASE INFORMATION ########

 $db_type = 'mysql';
 $db_server = 'localhost';
-$db_name = ''; # Database name
-$db_user = ''; # Database username
-$db_passwd = ''; # Database password
-$db_prefix = 'ck_'; # The prefix you chose for your CKSurf Install, default is ck_

}else{
	header("location:http://google.com");
}
?>