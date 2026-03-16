<?php
/**
 * Search Page - Player Search Functionality
 * 
 * Security features:
 * - CSRF token validation
 * - Prepared statements for all database queries
 * - Input validation
 * - Output encoding (XSS prevention)
 */

if($secure==1){

// Validate CSRF token
if (!isset($_POST['csrf_token']) || !validateCsrf($_POST['csrf_token'])) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Invalid request. Please refresh and try again.</p></div>";
    return;
}

// Validate search input exists
if(!isset($_POST['search']) || empty(trim($_POST['search']))) {
    echo "<div class=\"alert alert-dismissible alert-warning\"><h4>Error</h4><p>You need to enter in a search term!</p></div>";
    return;
}

$search = trim($_POST['search']);

// Validate minimum length
if(strlen($search) < 3) {
    echo "<div class=\"alert alert-dismissible alert-warning\"><h4>Error</h4><p>Please use 3 characters or more to search!</p></div>";
    return;
}

// Sanitize search input (remove potentially dangerous characters)
$search = sanitizeSearchInput($search);

// Connect to database
$conn = dbConnect();

$database_call = $db_prefix . "playerrank";

// Use prepared statement with LIKE for security
$searchParam = "%" . $search . "%";
$stmt = $conn->prepare("SELECT * FROM $database_call WHERE name LIKE ? ORDER BY points DESC LIMIT 100");
if (!$stmt) {
    echo "<div class=\"alert alert-dismissible alert-danger\"><h4>Error</h4><p>Database query failed.</p></div>";
    $conn->close();
    return;
}

$stmt->bind_param("s", $searchParam);
$stmt->execute();
$result = $stmt->get_result();

?>

<h2>Player Search</h2>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Player Name</th>
            <th>Country</th>
            <th>Points</th>
            <th>Maps Completed</th>
            <th>Last Played</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td><a href='?view=profile&id=" . esc($row["steamid"]) . "'>" . esc($row["name"]) . "</a></td>";
                echo "<td>" . esc($row["country"]) . "</td>";
                echo "<td>" . esc($row["points"]) . "</td>";
                echo "<td>" . esc($row['finishedmaps']) . "</td>";
                echo "<td>" . esc($row['lastseen']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td>No results found</td><td></td><td></td><td></td><td></td></tr>";
        }
        ?>
    </tbody>
</table>

<?php
$stmt->close();
$conn->close();

} // end secure check
?>
