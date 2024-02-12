<?php
session_start();
// print_r($_SESSION);
require_once '../database/database.php';
    
if (!isset($_SESSION["land"])) {
    header("Location: ..\data_page\landowners_login.php");
}

$userid = isset($_GET['userid']) ? $_GET['userid'] : null;

// if (!$userid) {
//     // Handle the case where no userid is provided in the URL
//     header("Location: landowners_dashboard_5.php");
// }

// Fetch user details based on the provided userid
// $sql = mysqli_query($conn, "SELECT ra.f_name, ra.s_name
//                             FROM tbl_renters_account ra
//                             WHERE ra.userid = $userid");

// if (mysqli_num_rows($sql) > 0) {
//     $row = mysqli_fetch_assoc($sql);
// } else {
//     // Handle the case where no user is found with the given userid
//     $row = array(); // Initialize $row as an empty array
// }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ResiHive - Contact</title>
    <link rel="icon" type="image/x-icon" href="..\data_image\favicon.png">
    <link rel="stylesheet" type="text/css" href="..\data_style\styles-land.css">

    <script src="https://kit.fontawesome.com/4d86b94a8a.js" crossorigin="anonymous"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
    <body class="chat-land_body">

        <?php include 'navbar_land.php'; ?>

        <section class="main">
            <div class="wrapper" id="container">
                <header>

                    <div class="title">
                        <h1>Chat</h1>
                    </div>
                    
                    <div class="content">

                        <?php
                        include_once "../database/database.php";

                        // Fetch userid from the URL parameters
                        $userid = isset($_GET['userid']) ? $_GET['userid'] : null;

                        if (!$userid) {
                            // Handle the case where no userid is provided in the URL
                            echo "No user found";
                            exit; // You can redirect or display an error message as needed
                        }

                        // Fetch data from rental_options and tbl_renters_account using a JOIN statement
                        $sql = mysqli_query($conn, "SELECT ro.renter_user_id, ra.f_name, ra.s_name, ra.profile_img
                                                    FROM rental_options ro
                                                    JOIN tbl_renters_account ra ON ro.renter_user_id = ra.userid 
                                                    WHERE ro.renter_user_id = $userid");

                        if (mysqli_num_rows($sql) > 0) {
                            $row = mysqli_fetch_assoc($sql);
                        } else {
                            // Handle the case where no rows are returned
                            $row = array(); // Initialize $row as an empty array
                        }
                        ?>
                            <img src="..\data_style\assets\profile\<?php echo $row['profile_img']; ?>" alt="Profile Image">
                            <div class="details">
                                <?php if (!empty($row)): ?>
                                    <span><a style="color:white"><?php echo $row['f_name']. " " . $row['s_name'] ?></a></span>
                                    <!-- <p><?php echo $row['msg_status']; ?></p> -->
                                    <div class="active_user">
                                        <div class="status-dot"><i class="fa-solid fa-circle"></i></div>
                                        <p style="color:white"> Active Now</p>
                                    </div>
                                <?php else: ?>
                                    <p style="color:white">No Landowner</p>
                                <?php endif; ?>
                            </div>

                    </div>
                </header>
                    <section class="users">
                    <div class="search">
                        <span class="text">Select a user to start chat</span>
                        <input type="text" placeholder="Enter name to search...">
                        <button><i class="#"></i></button>
                    </div>
                    <div class="users-list">
                        
                    </div>
                    </section>

                    <section class="chatBox">
                        <div class="content">
                            <div class="user-details">  
                            <?php
                            include_once "../database/database.php";

                            // Fetch userid from the URL parameters
                            $userid = isset($_GET['userid']) ? $_GET['userid'] : null;

                            if (!$userid) {
                                // Handle the case where no userid is provided in the URL
                                echo "No user found";
                                exit; // You can redirect or display an error message as needed
                            }

                            // Fetch data from rental_options and tbl_renters_account using a JOIN statement
                            $sql = mysqli_query($conn, "SELECT ro.renter_user_id, ra.f_name, ra.s_name, ra.profile_img
                                                        FROM rental_options ro
                                                        JOIN tbl_renters_account ra ON ro.renter_user_id = ra.userid 
                                                        WHERE ro.renter_user_id = $userid");

                            if (mysqli_num_rows($sql) > 0) {
                                $row = mysqli_fetch_assoc($sql);
                            } else {
                                // Handle the case where no rows are returned
                                $row = array(); // Initialize $row as an empty array
                            }
                            ?>

                            <?php if (!empty($row) && array_key_exists('f_name', $row)): ?>
                                <img src="..\data_style\assets\profile\<?php echo $row['profile_img']; ?>" alt="Profile Image">
                                <div class="details">
                                    <span><a style="color:black"><?php echo $row['f_name']. " " . $row['s_name'] ?></a></span>
                                    <div class="active_user">
                                        <div class="status-dot"><i class="fa-solid fa-circle"></i></div>
                                        <p style="color:black"> Active Now</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p>No user found</p>
                            <?php endif; ?>                  
                            </div>

                        </div>
                        
                        <div class="chat-cont" id="loadchat">
                            

                        </div>

                        <div class="typing-area">
                            <input type="text" name="sender_id" class= "sender_id" value="<?php echo $email; ?>"hidden>
                            <input type="text" name="chatmsg" id="chatmsg" class="input-field" placeholder="Type a message here..." autocomplete="off">
                            <button type="button" id="btnSend" title="Send Message"><i class="fab fa-telegram-plane"></i></button>
                        </div>

                        </div>

                        
                    </section>

            </div>
        </section>
        <script src="..\jscripts\chatBoxFeat.js"></script>

        <script>
            $(document).ready(function () {
                let userid = '<?= $_GET['userid'] ?>';

                // Flag to check if user manually scrolled up
                let manualScroll = false;

                // Function to send a message
                function sendMessage() {
                    let chatmsg = $('#chatmsg').val();
                    $.post({
                        url: "../ajax/INSERTCHAT_LANDOWNER.php",
                        data: { userid: userid, chatmsg: chatmsg }
                    }).done(function (data) {
                        if (data == "success") {
                            $('#chatmsg').val('');
                            scrollToBottom(); // Scroll down after sending a new message
                        }
                    });
                }

                // Keypress event for the Enter key in the input field
                $(document).on("keypress", function (e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        sendMessage();
                    }
                });

                // Click event for the send button
                $(document).on("click", "#btnSend", function () {
                    sendMessage();
                });

                // Scroll event to detect manual scrolling
                $('#loadchat').scroll(function () {
                    manualScroll = ($('#loadchat').scrollTop() + $('#loadchat').innerHeight() < $('#loadchat')[0].scrollHeight);
                });

                // Periodic function to load chat messages
                function loadChat() {
                    // Get the current scroll position
                    let currentScroll = $('#loadchat').scrollTop();

                    $.post({
                        url: "../ajax/LOADCHAT_LANDOWNER.php",
                        data: { userid: userid }
                    }).done(function (data) {
                        // Reverse the order of chat messages before inserting
                        $('#loadchat').html(data);

                        // If user has not manually scrolled up, scroll down
                        if (!manualScroll) {
                            scrollToBottom();
                        } else {
                            // If user manually scrolled up, maintain the scroll position
                            $('#loadchat').scrollTop(currentScroll);
                        }
                    });
                }

                // Initial load of chat messages
                loadChat();

                // Periodic function to load chat messages
                setInterval(function () {
                    loadChat();
                }, 1000);

                function scrollToBottom() {
                    $('#loadchat').scrollTop($('#loadchat')[0].scrollHeight);
                }
            });
        </script>



    </body>
</html>