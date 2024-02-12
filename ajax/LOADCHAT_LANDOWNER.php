<?php
session_start();
include_once "../mysql/Connection.php";
$connection = new Connection();
$db = $connection->accessConnection();
$land = $_SESSION['land'];
$rent_id = $_POST['userid'];
$email = $land['email'];
$output = "";

if ($db->connect_error) {
    echo "database";
} else {
    $land_id = getLandownerID($email);
    $stmt = $db->prepare("SELECT * FROM chats WHERE (sender_id = ? AND receiver_id = ?) OR (receiver_id = ? AND sender_id = ?) ORDER BY id");
    $stmt->bind_param("ssss", $land_id, $rent_id, $land_id, $rent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $time = date("h:i A", strtotime($row['time'])); // Assuming 'time' is the new column name
            $message = '<div class="details">
                            <p class="message-text">' . $row['chat'] . '</p>
                            <span class="timestamp-bubble">' . $time . '</span>
                        </div>';

            if ($row['receiver_id'] === $land_id) {
                $output .= '<div class="chat incoming">' . $message . '</div>';
            } else {
                $output .= '<div class="chat outgoing">' . $message . '</div>';
            }
        }
    } else {
        // No Messages
        $output = '<p class="no-messages">No Messages</p>';
    }

    // Output the result
    echo $output;
}

function getLandownerID($email)
{
    $connection = new Connection();
    $db = $connection->accessConnection();

    if ($db->connect_error) {
        echo "database";
    } else {
        $stmt = $db->prepare("SELECT userid FROM tbl_landowner_account WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($id);
        $stmt->fetch();
        $stmt->close();

        return $id;
    }
}


?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all elements with the class 'details'
    var detailsElements = document.querySelectorAll('.details');

    // Add a mouseover event listener to each 'details' element
    detailsElements.forEach(function(detailsElement) {
        detailsElement.addEventListener('mouseover', function() {
            // Find the child element with the class 'timestamp-bubble' within the 'details' element
            var timestampBubble = this.querySelector('.timestamp-bubble');
            
            // Show the timestamp bubble
            timestampBubble.style.display = 'block';
        });

        // Add a mouseout event listener to each 'details' element
        detailsElement.addEventListener('mouseout', function() {
            // Find the child element with the class 'timestamp-bubble' within the 'details' element
            var timestampBubble = this.querySelector('.timestamp-bubble');
            
            // Hide the timestamp bubble
            timestampBubble.style.display = 'none';
        });
    });
});

</script>
