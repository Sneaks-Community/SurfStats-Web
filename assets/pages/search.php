<?php
if($secure==1){

	if(!$_POST){ echo"<div class=\"alert alert-dismissible alert-warning\"><h4>Uh-Oh!</h4><p>You need to enter in a search term!</p></div>"; }else{
	if(!$_POST["search"]){ echo"<div class=\"alert alert-dismissible alert-warning\"><h4>Uh-Oh!</h4><p>You need to enter in a search term!</p></div>"; }else{

	$conn = new mysqli($db_server, $db_user, $db_passwd, $db_name);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}

	$database_call = $db_prefix."playerrank";
	$search_result = mysqli_real_escape_string($conn, htmlspecialchars($_POST["search"], ENT_QUOTES));

	$searchTerms = explode(' ', $search_result);
	$searchTermUnits = array();
	foreach ($searchTerms as $term) {
		$term = trim($term);
		if (!empty($term)) {
			$searchTermUnits[] = "name LIKE '%$term%'";
		}
	}

	$result = $conn->query("SELECT * FROM $database_call WHERE ".implode(' AND ', $searchTermUnits));

	?>

	<h2>Player Search</h2>

	<table class="table table-striped table-hover ">
		<thead>
			<tr>
				<th>Player name</th>
				<th>Country</th>
				<th>Points</th>
				<th>Maps Played</th>
				<th>Last Played</th>
			</tr>
		</thead>
		<tbody>
			<?php
			if ($result->num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					echo "<tr><td><a href='?view=profile&id=".$row["steamid"]."'>".$row["name"]."</a></td><td>".$row["country"]."</td><td>".$row["points"]."</td><td>".$row["finishedmaps"]."<td>".$row["lastseen"]."</td></tr>";
				}
			}else{
				echo"<tr><td>No results found</td><td></td><td></td><td></td><td></td></tr>";
			}
			?>
		</tbody>
	</table>

<?php	$conn->close();	} } }  ?>