<?php $secure = 1; include("config.php"); include("assets/lang/$conf_language.php"); $page_topic = htmlspecialchars($_GET['view'], ENT_QUOTES); ?>
<?php
function processFloat($seconds) {
		$micro = sprintf("%06d",($seconds - floor($seconds)) * 1000000);
		$d = new DateTime( date('Y-m-d H:i:s.'.$micro,$seconds) );

		return $d->format("i:s.u");
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>CK Stats</title>
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css" />
	<script src="//code.jquery.com/jquery-3.1.0.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/stats.css" />
	<meta name="description" content="Ck Stats Page">
	<link rel="author" href="humans.txt"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="container">

	<h1><?php echo $stat_name; ?></h1>

	<nav class="navbar navbar-default">
	  <div class="container-fluid">


		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		  <ul class="nav navbar-nav">
			<li><a href="?view=home"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
			<li><a href="?view=maps"><i class="fa fa-map-marker" aria-hidden="true"></i> Maps</a></li>
		  </ul>
		  <form class="navbar-form navbar-left" action="?view=search" method="post">
			<div class="form-group">
				<input name="search" class="form-control" placeholder="Search Players" type="text">
			</div>
			<button type="submit" class="btn btn-default">Submit</button>
		  </form>
		  <ul class="nav navbar-nav navbar-right">
			<li><a href="<?php echo $group_url; ?>"><?php echo $group_name; ?></a></li>
		  </ul>
		</div>
	  </div>
	</nav>

	<?php

	switch($page_topic){
		
		case "map":
			include("assets/pages/view_map.php");
		break;
		case "maps":
			include("assets/pages/view_maps.php");
		break;
		case "records": //--coming soon!
			include("assets/pages/view_records.php");
		break;
		case "profile":
			switch($conf_record_stats){
				case"0":
					include("assets/pages/view_profile_0.php");
				break;
				case"1":
					include("assets/pages/view_profile.php");
				break;
				case"2":
					include("assets/pages/view_profile_2.php");
				break;
				default:
					include("assets/pages/view_profile.php");
			}
		break;
		case "recent":
			include("assets/pages/view_recent.php");
		break;
		case "search":
			include("assets/pages/search.php");
		break;
		/*case "tiers":
			include("assets/pages/view_tiers.php");
		break;*/ //Coming soon
		default:
		
		include("assets/pages/default.php");
	}

	?>

	<footer>
	Assembled by <a href="https://gitlab.com/Rowedahelicon/CkSurfStatsPage">Rowedahelicon</a> <span style="font-size:8pt;">(V.1.3.)</span>, CS:GO CkSurf built by <a href="https://forums.alliedmods.net/showthread.php?t=264498">Jonitaikaponi</a><?php if($use_marco_cksurf == '1'){ echo" & <a href='https://github.com/marcowmadeira/ckSurf'>Marcowmadeira</a>"; } ?> <a href="humans.txt"><img src="assets/images/humanstxt-transparent-color.png" alt="Humans.txt image" /></a>
	</footer>

</div>
</body>
</html>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>