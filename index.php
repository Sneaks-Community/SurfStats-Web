<?php $secure = 1; include("config.php"); include("assets/lang/$conf_language.php"); $page_topic = htmlspecialchars($_GET['view'], ENT_QUOTES); ?>
<?php
function processFloat($decimal)
{
	$secs = floor($decimal);
	$mins = (($secs / 60) % 60);
	$hours = floor($secs / 3600);
	$minutes = floor((int)$decimal / 60);
	$seconds = $decimal % 60;

	$whole = floor($decimal);
	$milli = $decimal - $whole;
	$milliclean = round($milli, 4);
	$millienforcer = sprintf('%0.4f', $milliclean);
	$millifinal = substr($millienforcer, 2);
		
	if ($hours > 0) {
		return str_pad($hours, 2, "0", STR_PAD_LEFT) . ":" . str_pad($mins, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . "." . str_pad($millifinal, 2, "0", STR_PAD_LEFT);
	}
	else {
		return str_pad($minutes, 2, "0", STR_PAD_LEFT) . ":" . str_pad($seconds, 2, "0", STR_PAD_LEFT) . "." . str_pad($millifinal, 2, "0", STR_PAD_LEFT);
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title><?php echo $stat_name; ?></title>
	<link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="assets/css/font-awesome.min.css" />
	<script src="//code.jquery.com/jquery-3.4.1.min.js" type="text/javascript"></script>
	<script src="assets/js/bootstrap.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="assets/css/stats.css" />
	<meta name="description" content="Surf Stats Page">
	<link rel="author" href="humans.txt"/>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta property="og:type" content="website">
	<meta property="og:url" content="https://snksrv.com/surfstats">
	<meta property="og:title" content="SNK.SRV Surf Stats">
	<meta property="og:description" content="Surf Stats Page">
	<meta name="theme-color" content="#3091FF">
	<script src="assets/js/sorttable.min.js"></script>
</head>
<body>
<script src=https://snksrv.com/js/jquery.backstretch.min.js></script>
<script>
jQuery(document).ready(function($) {
 		// Image Array Variable
		var images = [
		'assets/images/aether2.jpg',
		'assets/images/surf_edge.jpg',
		'assets/images/surf_extremex.jpg',
		'assets/images/surf_lt_unicorn_official.jpg',
		'assets/images/surf_summer.jpg',
		];
 		// Function to Shuffle through the Array Randomly
				var slideshow = images.sort(function() { return 0.5 - Math.random() });
 		// Displays a Random Image to begin the slideshow and randomizes the slideshow.
	$("body").backstretch(slideshow,
		{
			duration:7000,
			fade:1100,
		});
});
</script>
<div class="container">

<h1><?php echo $stat_name; ?></h1>
<nav class="navbar navbar-inverse" id="navTest">
	<div class="container-fluid">
		<ul class="nav navbar-nav">
			<li><a href="?view=home"><i class="fa fa-home" aria-hidden="true"></i> Home</a></li>
			<li><a href="?view=maps"><i class="fa fa-map-marker" aria-hidden="true"></i> Maps</a></li>
			<li><a href="?view=players"><i class="fa fa-users" aria-hidden="true"></i> Players</a></li>
		</ul>
		<form class="navbar-form navbar-left" action="?view=search" method="post" id="surfSearchBar">
			<div class="form-group">
				<input id="searchInput" name="search" class="form-control" placeholder="Search Players" type="text">
				<button type="submit" class="btn btn-default" id="searchSubmit">Submit</button>
			</div>
		</form>
		<ul class="nav navbar-nav navbar-right">
			<li><a href="<?php echo $group_url; ?>"><?php echo $group_name; ?></a></li>
		</ul>
	</div>
  <center>
	<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
		<!-- Surf Stats 2 -->
		<ins class="adsbygoogle"
			style="display:inline-block;width:728px;height:90px"
			data-ad-client="ca-pub-2422230716896877"
			data-ad-slot="9083626575"></ins>
		<script>
		(adsbygoogle = window.adsbygoogle || []).push({});
		</script>
	</center>
</nav>
<script>
	// grab screen size, and make adjustments
	var viewPortSize = document.documentElement.clientWidth;
	if(viewPortSize <768) {
		var element = document.getElementById('surfSearchBar');
		element.classList.remove('navbar-form');
	} else if (viewPortSize >=768) {
		var element = document.getElementById('surfSearchBar');
		element.classList.add('navbar-form');
	}
	// grab new screen size on resize. 
	window.onresize = onScreenResize;
	function onScreenResize() {
		var viewPortSize = document.documentElement.clientWidth;
		if(viewPortSize <768) {
			var element = document.getElementById('surfSearchBar');
			element.classList.remove('navbar-form');
		} else if (viewPortSize >=768) {
			var element = document.getElementById('surfSearchBar');
			element.classList.add('navbar-form');
		}
	}
</script>

<?php

switch($page_topic){
	
	case "map":
		include("assets/pages/view_map.php");
	break;
	case "maps":
		include("assets/pages/view_maps.php");
	break;
	case "players":
		include("assets/pages/playerlist.php");
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
	default:
	
	include("assets/pages/default.php");
}

?>

<footer>
<p><center><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- Surf Stats Ad -->
<ins class="adsbygoogle"
     style="display:inline-block;width:728px;height:90px"
     data-ad-client="ca-pub-2422230716896877"
     data-ad-slot="1979162176"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></center></p>
</footer>

</div>
</body>
</html>

<script>
$(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip(); 
});
</script>
