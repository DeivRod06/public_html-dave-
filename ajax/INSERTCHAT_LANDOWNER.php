<?php
session_start();
include_once "../mysql/Connection.php";
$connection = new Connection();
$db = $connection->accessConnection();

$land = $_SESSION['land'];
$user_id = $_POST['userid'];
$chatmsg = $_POST['chatmsg'];
$email = $land['email'];

// Check if $chatmsg is not empty before proceeding
if (empty($chatmsg)) {
    echo "Empty message";
} else {
    // Validate $user_id to prevent SQL injection
    if (!is_numeric($user_id)) {
        echo "Invalid user ID";
    } else {
        if ($db->connect_error) {
            echo "database";
        } else {
            $land_id = getLandownerID($email);
            $stmt  = $db->prepare("INSERT INTO chats(chat, rent_id, land_id, sender_id, receiver_id) VALUES(?,?,?,?,?)");
            $stmt->bind_param("sssss", $chatmsg, $user_id, $land_id, $land_id, $user_id);
            $stmt->execute();
            $stmt->close();
            echo "success";
        }
    }
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
