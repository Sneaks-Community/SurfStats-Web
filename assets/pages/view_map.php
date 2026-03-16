<?php
/**
 * View Map Page - Individual Map Leaderboard
 * 
 * Security features:
 * - Input validation for map name
 * - Prepared statements for all database queries
 * - Output encoding (XSS prevention)
 */

if($secure==1){

// Connect to database
$conn = dbConnect();

// Validate mapname
$mapname = isset($_GET["name"]) ? $_GET["name"] : '';
if (!validateMapName($mapname)) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Invalid map name!</p></div>";
    $conn->close();
    return;
}

// Get run count - using prepared statement
$database_call = $db_prefix . "playertimes";
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM $database_call WHERE mapname = ?");
$stmt->bind_param("s", $mapname);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$run_cnt = $row['cnt'];
$stmt->close();

// Get map tier and author - using prepared statement
$database_call_tier = $db_prefix . "maptier";
$stmt = $conn->prepare("SELECT tier, mapper FROM $database_call_tier WHERE mapname = ? LIMIT 1");
$stmt->bind_param("s", $mapname);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_object();
    $map_tier = $row->tier;
    $map_author = $row->mapper;
} else {
    $map_tier = "Unknown";
    $map_author = "Unknown";
}
$stmt->close();

// Get bonuses count - using prepared statement
$database_call_zones = $db_prefix . "zones";
$stmt = $conn->prepare("SELECT MAX(zonegroup) AS bonuses FROM $database_call_zones WHERE mapname = ?");
$stmt->bind_param("s", $mapname);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_object();
$bonus_num = $row ? ($row->bonuses ?? 0) : 0;
$stmt->close();

// Get bonus completions - using prepared statement
$database_call_bonus = $db_prefix . "bonus";
$stmt = $conn->prepare("SELECT COUNT(*) AS bonuses FROM $database_call_bonus WHERE mapname = ?");
$stmt->bind_param("s", $mapname);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_object();
$bonus_comp = $row ? ($row->bonuses ?? 0) : 0;
$stmt->close();

// Get average time - using prepared statement
$database_call = $db_prefix . "playertimes";
$stmt = $conn->prepare("SELECT AVG(runtimepro) AS average FROM $database_call WHERE mapname = ?");
$stmt->bind_param("s", $mapname);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_object();
$avg_times = $row ? ($row->average ?? 0) : 0;
$stmt->close();

// Get total rows for pagination
$stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM $database_call WHERE mapname = ?");
$stmt->bind_param("s", $mapname);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$row_cnt = ceil($row['cnt'] / 50);
$stmt->close();

// Handle pagination
$page = isset($_GET["p"]) ? $_GET["p"] : '0';
if (!validatePageNumber($page)) {
    $page = 0;
}
$page_start = (intval($page) >= 1) ? (intval($page) - 1) * 50 : 0;
if ($page_start < 0) $page_start = 0;

// Get leaderboard - using prepared statement with LIMIT
$stmt = $conn->prepare("SELECT * FROM $database_call WHERE mapname = ? ORDER BY runtimepro ASC LIMIT ?, 50");
$stmt->bind_param("si", $mapname, $page_start);
$stmt->execute();
$result = $stmt->get_result();

?>

<style>
.subheader img {
    float: right;
    border-radius: 10px;
}
.subheader a:link { color: lightgrey; }
.subheader a:visited { color: lightgrey; }
</style>

<div class="subheader">
    <img src="<?php echo "../bans/images/maps/" . esc($mapname) . ".jpg"; ?>" alt="<?php echo esc($mapname); ?>">
    <h2><?php echo esc($mapname); ?> <a href="<?php echo "https://fastdl.snksrv.com/maps/" . esc($mapname) . ".bsp.bz2"; ?>" target=\"_blank\" rel=\"noopener noreferrer\"><i class='fa fa-download' aria-hidden='true'></i></a></h2>
    <h5>Map Author: <?php echo esc($map_author); ?></h5>
    <b>Completions: <?php echo esc($run_cnt); ?></b><br/>
    <b>Average Time: <?php echo processFloat($avg_times); ?></b><br/>
    <b>Map Tier: <?php echo esc($map_tier); ?></b><br/>
    <b>Bonuses: <?php echo esc($bonus_num); ?> (<?php echo esc($bonus_comp); ?> Completions)</b>
</div>

<h2>Record Times</h2>

<center>
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php
        // Get current page from URL, default to 1
        $current_page = isset($_GET["p"]) ? intval($_GET["p"]) : 1;
        if ($current_page < 1) $current_page = 1;
        if ($current_page > $row_cnt) $current_page = $row_cnt;
        
        // Calculate visible page range
        $max_visible = 8; // Maximum number of page links to show
        $show_pages = array();
        
        if ($row_cnt <= $max_visible + 1) {
            // If total pages is small, show all
            for ($x = 1; $x <= $row_cnt; $x++) {
                $show_pages[] = $x;
            }
        } else {
            // Always show first page
            $show_pages[] = 1;
            
            // Calculate range around current page
            $start = max(2, $current_page - 2);
            $end = min($row_cnt - 1, $current_page + 2);
            
            // If near the start, show pages 2 through 8 (or max_visible)
            if ($current_page <= 4) {
                $start = 2;
                $end = $max_visible;
            }
            
            // If near the end, show more pages towards the end
            if ($current_page >= $row_cnt - 3) {
                $start = max(2, $row_cnt - $max_visible + 1);
                $end = $row_cnt - 1;
            }
            
            // Add ellipsis if there's a gap after first page
            if ($start > 2) {
                $show_pages[] = '...';
            }
            
            // Add the page range
            for ($x = $start; $x <= $end; $x++) {
                $show_pages[] = $x;
            }
            
            // Add ellipsis if there's a gap before last page
            if ($end < $row_cnt - 1) {
                $show_pages[] = '...';
            }
            
            // Always show last page (if more than 1 page total)
            if ($row_cnt > 1) {
                $show_pages[] = $row_cnt;
            }
        }
        
        // Previous button
        $prev_page = ($current_page > 1) ? $current_page - 1 : 1;
        echo '<li><a href="?view=map&name=' . esc($mapname) . '&p=' . $prev_page . '" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
        
        // Display page numbers
        foreach ($show_pages as $x) {
            if ($x === '...') {
                echo '<li class="disabled"><a href="#">...</a></li>';
            } else {
                $active = ($x == $current_page) ? ' class="active"' : '';
                echo '<li' . $active . '><a href="?view=map&name=' . esc($mapname) . '&p=' . $x . '">' . $x . '</a></li>';
            }
        }
        
        // Next button
        $next_page = ($current_page < $row_cnt) ? $current_page + 1 : $row_cnt;
        echo '<li><a href="?view=map&name=' . esc($mapname) . '&p=' . $next_page . '" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
        ?>
    </ul>
</nav>
</center>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Player</th>
            <th></th>
            <th>Best Time</th>
            <th>Date</th>
            <th>Start Speed</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            $x = 1;
            while($row = $result->fetch_assoc()) {
                $rankBadge = "";
                if ($x <= 3) {
                    $rankBadge = "<span class='rank_$x' data-toggle='tooltip' data-placement='bottom' title='' data-original-title='" . esc($lang_rank[$x] ?? '') . "'><i class='fa fa-trophy' aria-hidden='true'></i></span>";
                }
                echo "<tr>";
                echo "<td><a href='?view=profile&id=" . esc($row["steamid"]) . "'>" . esc($row["name"]) . "</a></td>";
                echo "<td>" . $rankBadge . "</td>";
                echo "<td><i class=\"fa fa-clock\" aria-hidden=\"true\"></i> " . processFloat($row["runtimepro"]) . "</td>";
                echo "<td>" . esc($row["date"]) . "</td>";
                echo "<td>" . esc($row["startspeed"]) . " u/s</td>";
                echo "</tr>";
                $x++;
            }
        } else {
            echo "<tr><td colspan=\"5\">No records found for this map.</td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();

} // end secure check
?>
