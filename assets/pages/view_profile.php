<?php
/**
 * View Profile Page - Player Profile Display
 * 
 * Security features:
 * - Input validation for Steam ID
 * - Prepared statements for all database queries
 * - Output encoding (XSS prevention)
 */

if($secure==1){

// Connect to database
$conn = dbConnect();

// Validate and sanitize steamid
$steamid = isset($_GET['id']) ? $_GET['id'] : '';
if (!validateSteamId($steamid)) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Invalid Steam ID format!</p></div>";
    $conn->close();
    return;
}

// Grab STEAM Profile ID for Steam Community link
$split = explode(":", $steamid);
if (count($split) !== 3) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Invalid Steam ID!</p></div>";
    $conn->close();
    return;
}
$steam_profile = ($split[2] * 2) + 0x0110000100000000 + $split[1];

$database_call = $db_prefix . "playertimes";

// Get list of maps this player has completed - using prepared statement
$stmt = $conn->prepare("SELECT * FROM $database_call WHERE steamid = ?");
if (!$stmt) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Database query failed.</p></div>";
    $conn->close();
    return;
}

$stmt->bind_param("s", $steamid);
$stmt->execute();
$result_save = $stmt->get_result();
$map_array = array();
$recordStat = 0;

if ($result_save->num_rows > 0) {
    while($row = $result_save->fetch_assoc()) {
        array_push($map_array, $row["mapname"]);
    }
}
$stmt->close();

// Lookup all maps and check if player is in top 3 rankings
$record_times = array();
foreach ($map_array as $value) {
    $stmt = $conn->prepare("SELECT * FROM $database_call WHERE mapname = ? ORDER BY runtimepro ASC LIMIT 3");
    $stmt->bind_param("s", $value);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $x = 1;
        while($row = $result->fetch_assoc()) {
            if($row['steamid'] == $steamid) {
                $recordStat++;
                $record_times[$row['mapname']] = "<span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='" . esc($lang_rank[$x]) . "'><i class='fa fa-trophy' aria-hidden='true'></i></span>";
            }
            $x++;
        }
    }
    $stmt->close();
}

// Get player times for map stats table
$stmt = $conn->prepare("SELECT * FROM $database_call WHERE steamid = ? ORDER BY mapname ASC");
$stmt->bind_param("s", $steamid);
$stmt->execute();
$result_save = $stmt->get_result();

$map_times = "";
while($row = $result_save->fetch_assoc()) {
    $recordBadge = isset($record_times[$row['mapname']]) ? $record_times[$row['mapname']] : '';
    $map_times .= "<tr><td><a href='?view=map&name=" . esc($row["mapname"]) . "'>" . esc($row["mapname"]) . "</a></td>";
    $map_times .= "<td>" . $recordBadge . "</td>";
    $map_times .= "<td><i class=\"fa fa-clock-o\" aria-hidden=\"true\"></i> " . processFloat($row["runtimepro"]) . "</td></tr>";
}
$stmt->close();

// Get player rank info - using prepared statement
$database_call = $db_prefix . "playerrank";
$stmt = $conn->prepare("SELECT * FROM $database_call WHERE steamid = ?");
$stmt->bind_param("s", $steamid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<h2><a href='http://steamcommunity.com/profiles/" . esc($steam_profile) . "' target=\"_blank\" rel=\"noopener noreferrer\">" . esc($row['name']) . "</a></h2>";
        echo "Last seen on " . esc($row['lastseen']) . "<br/><br/>";
        ?>
        <table class="table table-striped table-hover">
            <tbody>
                <?php
                echo "<tr><td><b>Points</b> : " . esc($row['points']) . "</td><td><b>Country</b> : " . esc($row['country']) . "</tr>";
                echo "<tr><td><b>Win Ratio</b> : " . esc($row['winratio']) . "</td><td><b>Points Ratio</b> : " . esc($row['pointsratio']) . "</tr>";
                echo "<tr><td><b>Finished Maps</b> : " . esc($row['finishedmaps']) . "</td><td><b>Records</b> : " . esc($recordStat) . "</tr>";
                ?>
            </tbody>
        </table>
        <?php
    }
} else {
    echo "<div class=\"alert alert-dismissible alert-warning\"><h4>Player Not Found</h4><p>No player found with that Steam ID.</p></div>";
}
$stmt->close();

?>

<h2>Map stats</h2>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Map name</th>
            <th>Rank</th>
            <th>Best time</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if (!empty($map_times)) {
            echo $map_times; 
        } else {
            echo "<tr><td colspan=\"3\">No completed maps found.</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php
$conn->close();

} // end secure check
?>
