<?php
include_once "../mysql/Connection.php";
$connection = new Connection();
$db = $connection->accessConnection();

// Get the 'owner_user_id' parameter from the query string
$owner_user_id = $_GET['owner_user_id'];

// Check if the 'owner_user_id' parameter is set
if (isset($owner_user_id)) {
    if ($db->connect_error) {
        echo "database";
    } else {
        $stmt = $db->prepare("SELECT owner_user_id FROM chat_users WHERE id = ?");
        $stmt->bind_param("s", $owner_user_id);
        $stmt->execute();
        $stmt->bind_result($ownerUserId);
        
        // Fetch the result
        $stmt->fetch();
        
        // Output the result
        echo $ownerUserId;

        // Close the statement and database connection
        $stmt->close();
        $db->close();
    }
} else {
    echo "owner_user_id parameter is not set";
}
?>
