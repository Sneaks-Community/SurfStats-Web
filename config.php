<?php

if($secure==1){

######## GENERAL INFORMATION ########

$stat_name = '';		# Stats page name
$group_name = ''; # Name of your steam group (Optional)
$group_url = ''; # URL to your Steam group or Website
$local_timezone = 'EST'; #Set your local timezone here E.G. EST

######### DATABASE INFORMATION ########

$db_type = 'mysql';
$db_server = 'localhost';
$db_name = ''; # Database name
$db_user = ''; # Database username
$db_passwd = ''; # Database password
$db_prefix = 'ck_'; # The prefix you chose for your CKSurf Install, default is ck_

}else{
	header("location:http://google.com");
}

?>