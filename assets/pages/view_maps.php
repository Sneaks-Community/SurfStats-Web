<?php
/**
 * View Maps Page - Map Leaderboard List
 * 
 * Security features:
 * - Prepared statements for all database queries
 * - Output encoding (XSS prevention)
 */

if($secure==1){

// Connect to database
$conn = dbConnect();

$database_call = $db_prefix . "playertimes";
$database_call_2 = $db_prefix . "maptier";

// Get all maps with world record times
// This complex query finds the best time for each map
$sql = "SELECT tbl.* FROM $database_call tbl 
        INNER JOIN (SELECT mapname, MIN(runtimepro) MinPoint FROM $database_call GROUP BY mapname) tbl1 
        ON tbl1.mapname = tbl.mapname WHERE tbl1.Minpoint = tbl.runtimepro";

$result = $conn->query($sql);

if (!$result) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Database query failed.</p></div>";
    $conn->close();
    return;
}

?>
<table class="table table-striped table-hover sortable">
    <thead>
        <tr>
            <th>Map Name</th>
            <th>Map Tier</th>
            <th>WR Time</th>
            <th>WR Holder</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $current_map = $row["mapname"];
                
                // Get map tier using prepared statement
                $stmt = $conn->prepare("SELECT tier FROM $database_call_2 WHERE mapname = ? LIMIT 1");
                $stmt->bind_param("s", $current_map);
                $stmt->execute();
                $tier_result = $stmt->get_result();
                $value = $tier_result->fetch_object();
                $this_map_tier = $value ? $value->tier : "Unknown";
                $stmt->close();
                
                echo "<tr>";
                echo "<td><a href='?view=map&name=" . esc($row["mapname"]) . "'>" . esc($row["mapname"]) . "</a></td>";
                echo "<td>" . esc($this_map_tier) . "</td>";
                echo "<td>" . processFloat($row["runtimepro"]) . "</td>";
                echo "<td><a href='?view=profile&id=" . esc($row["steamid"]) . "'>" . esc($row["name"]) . "</a></td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan=\"4\">No maps found.</td></tr>";
        }
        ?>
    </tbody>
</table>
<?php
$conn->close();

} // end secure check
?>
