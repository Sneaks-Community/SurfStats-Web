<?php
if($secure==1){
	
$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$mapname = mysqli_real_escape_string($conn, htmlspecialchars($_GET["name"], ENT_QUOTES));

$database_call = $db_prefix."playertimes";
if ($result = $conn->query("SELECT * FROM $database_call WHERE mapname='$mapname'")) { $run_cnt = $result->num_rows; $result->close(); }

$database_call = $db_prefix."maptier";
if ($result = $conn->query("SELECT * FROM $database_call WHERE mapname='$mapname' LIMIT 1")) { $row = $result->fetch_object(); $map_tier = $row->tier;  $result->close(); }

$database_call = $db_prefix."playertimes";
$result = $conn->query("SELECT * FROM $database_call WHERE mapname = '$mapname'");
$row_cnt = ceil($result->num_rows / 30);

$page_start = ($_GET["p"] ? mysqli_real_escape_string($conn, htmlspecialchars($_GET["p"], ENT_QUOTES)) : '0');

if($page_start >=1){ $page_start = $page_start - 1; }
if($page_start <0){ $page_start = 0; }
$page_start = $page_start * 30;

$sql = "SELECT * FROM $database_call WHERE mapname = '$mapname' ORDER BY runtimepro ASC LIMIT $page_start,30";
$result = $conn->query($sql);

?>

<h2><?php echo $mapname; ?></h2>
<b>Number of completed runs: <?php echo $run_cnt; ?></b><br/>
<b>Map tier: <?php echo $map_tier; ?></b>

<h2>Record times</h2>

<?php
if($use_marco_cksurf == '1'){
	echo"<div class=\"btn-group\" role=\"group\" aria-label=\"...\">
		<button type=\"button\" class=\"btn btn-primary\">Records</button>
		<a class=\"btn btn-default\" href=\"?view=records&name=$mapname&p=1\">Stage Records</a>
	</div>";
}
?>

<nav aria-label="Page navigation">
  <ul class="pagination">
    <li>
      <a href="<?php echo "?view=map&name=$mapname&p=1"; ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
	<?php
	$x = 1;
	while($x<=$row_cnt){
		echo "<li><a href=\"?view=map&name=$mapname&p=$x\">$x</a></li>";
		$x++;
	}
	?>
    <li>
      <a href="<?php echo "?view=map&name=$mapname&p=$row_cnt"; ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

<table class="table table-striped table-hover ">
	<thead>
		<tr>
			<th>Player name</th>
			<th></th>
			<th>Best time</th>
		</tr>
	</thead>
	<tbody>
		<?php

		if ($result->num_rows > 0) {
			// output data of each row
			$x=1;
			while($row = $result->fetch_assoc()) {
				if($x<=3) {
					echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td><span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='".$lang_rank[$x]."'><i class='fa fa-trophy' aria-hidden='true'></i></span></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td></tr>";
				}else{
					echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td></td><td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> ".processFloat($row["runtimepro"])."</td></tr>";
				}
			$x++;
			}
		} 

		?>
	</tbody>
</table>

<nav aria-label="Page navigation">
  <ul class="pagination">
    <li>
      <a href="<?php echo "?view=map&name=$mapname&p=1"; ?>" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
	<?php
	$x = 1;
	while($x<=$row_cnt){
		echo "<li><a href=\"?view=map&name=$mapname&p=$x\">$x</a></li>";
		$x++;
	}
	?>
    <li>
      <a href="<?php echo "?view=map&name=$mapname&p=$row_cnt"; ?>" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>

<?php $conn->close(); } ?>