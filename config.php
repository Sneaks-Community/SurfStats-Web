<?php

if($secure==1){

######## GENERAL INFORMATION ########

$stat_name = "Sneak's Community - Surf Stats";		# Stats page name
$group_name = 'Website'; # Name of your steam group (Optional)
$group_url = 'https://snksrv.com'; # URL to your Steam group or Website
$local_timezone = 'America/Chicago'; #Set your local timezone here E.G. EST
$conf_language = 'eng'; # Only supports English right now
$conf_record_stats = '2'; # How do you want profile stats to be displayed? 0 = No records, 1 = all in one, 2 = Record stats are displayed different from map stats

######### DATABASE INFORMATION ########

$db_type = 'mysql';
$db_server = 'localhost';
$db_name = 'name'; # Database name
$db_user = 'user'; # Database username
$db_passwd = 'password'; # Database password
$db_prefix = 'ck_'; # The prefix you chose for your CKSurf Install, default is ck_

}else{
	header("location:https://snksrv.com");
}

?>