    <?php
    session_start();
    require_once '../mysql/conn.php';

    // Check if the user is already logged in
    if (empty($_SESSION["tenant"])) {
        header("Location: ../data_page/renters_login.php");
        exit();
    }

    // Get the user ID (userid)
    $userid = $_SESSION["tenant"]["userid"];

    // Get the ID and house ID from the URL
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $houseId = isset($_GET['house_id']) ? $_GET['house_id'] : null;

    // Fetch data from chat_users and house_rentals based on the house ID
    $sqlFetchData = "
        SELECT cu.*, hr.*
        FROM chat_users cu
        LEFT JOIN house_rentals hr ON cu.house_id = hr.house_id
        WHERE cu.id = ?
        LIMIT 1
    ";

    $stmtFetchData = $conn->prepare($sqlFetchData);

    if ($stmtFetchData === false) {
        die('Error in SQL query: ' . $conn->error);
    }

    $stmtFetchData->bind_param('s', $id);
    $stmtFetchData->execute();
    $resultFetchData = $stmtFetchData->get_result();

    if ($resultFetchData === false) {
        die('Error in SQL result: ' . $stmtFetchData->error);
    }

    $defaultHouseImage = "../data_image/favicon.png";
$defaultHouseName = "No Available Inquiries.";
$defaultHouseType = "";

// Check if there are chat users
    if ($resultFetchData->num_rows > 0) {
        $userData = $resultFetchData->fetch_assoc();

        // Use $userData as needed
        $houseImage = $userData['house_image'];
        $houseName = $userData['house_name'];
        $houseType = $userData['house_type'];
    } else {
        // Use default values if there are no chat users
        $houseImage = $defaultHouseImage;
        $houseName = $defaultHouseName;
        $houseType = $defaultHouseType;

        // Get the id of the first user-details
        $sqlFirstUserId = "
            SELECT id
            FROM chat_users
            LIMIT 1
        ";

        $resultFirstUserId = $conn->query($sqlFirstUserId);

        if ($resultFirstUserId->num_rows > 0) {
            $firstUserData = $resultFirstUserId->fetch_assoc();
            $firstUserId = $firstUserData['id'];

            // Redirect to the first user's id
            header("Location: ../data_page/renters_dashboard_2.php?id=$firstUserId");
            exit();
        }
    }
    ?>


    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ResiHive - Contact</title>
        <link rel="icon" type="image/x-icon" href="..\data_image\favicon.png">
        <link rel="stylesheet" type="text/css" href="..\data_style\styles-renters.css">
        <script src="https://kit.fontawesome.com/4d86b94a8a.js" crossorigin="anonymous"></script>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    </head>
        <body class="chat-body">

            <?php include 'navbar.php'; ?>

            <section class="main">
                <div class="wrapper" id="container">
                    <header>
                        <div class="title">
                            <h1>Chat</h1>
                        </div>
                        <div class="content">
                            <?php include 'chat_items.php'; ?>
                        </div>
                    </header>
                        <!-- <section class="users">
                            <div class="search">
                                <span class="text">Select a user to start chat</span>
                                <input type="text" placeholder="Enter name to search...">
                                <button><i class="#"></i></button>
                            </div>
                            <div class="users-list">
                                
                            </div>
                        </section> -->

                        <section class="chatBox">
                            <div class="user-content">
                            <?php
                                echo '
                                    <div class="user-detail">
                                        <img src="' . $houseImage . '" alt="' . $houseName . '">
                                        <div class="details">
                                            <span style="color:black">' . $houseName . ' </span>
                                            <p>' . $houseType . '</p>
                                        </div>
                                    </div>
                                ';
                            ?>
                            </div>
                            
                            <div class="chat-cont"  id="loadchat">
                        
                            </div>

                            <div class="typing-area">
                                <input type="text" name="chatmsg" id="chatmsg" class="input-field" placeholder="Type a message here..." autocomplete="off">
                                <button type="button" id="btnSend" title="Send Message"><i class="fab fa-telegram-plane"></i></button>
                            </div>  

        
                        </section>

                </div>
            </section>

    



            <footer>
            <div class="watermark">
                <p>by &copy;ResiHive 2023</p>
            </div>
            </footer>

            <!-- <script src="/jscripts/users.js"></script> -->

            <!-- <script src="../jscripts/chat.js"></script> -->

            <!-- <script src="../jscripts/chat copy.js"></script> -->

            <script src="/jscripts/dropdownfeat.js"></script>
                    
            <script src="/jscripts/chatBoxFeat.js"></script>
                    
            <script>
        $(document).ready(function () {
            // Function to extract parameter from URL
            function getParameterByName(name, url) {
                if (!url) url = window.location.href;
                name = name.replace(/[\[\]]/g, "\\$&");
                var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                    results = regex.exec(url);
                if (!results) return null;
                if (!results[2]) return '';
                return decodeURIComponent(results[2].replace(/\+/g, " "));
            }

            // Get the id from the URL
            let land_id = getParameterByName('id');

            // Check if the land_id is available
            if (land_id) {
                // Function to send a message
                function sendMessage() {
                    let chatmsg = $('#chatmsg').val();

                    $.post({
                        url: "../ajax/INSERTCHAT_RENTER.php",
                        data: { land_id: land_id, chatmsg: chatmsg, owner_user_id: getOwnerUserId() },
                    }).done(function (data) {
                        if (data == "success") {
                            $('#chatmsg').val('');
                            scrollToBottom();
                        }
                        console.log(data);
                    });
                }

                // Function to get owner_user_id from the server
                function getOwnerUserId() {
                    let ownerUserId;

                    // Make an AJAX request to fetch owner_user_id
                    $.ajax({
                        url: "../data_page/chat_items.php", // Replace with your server-side script
                        type: "GET",
                        data: { id: land_id }, // Use land_id instead of undefined owner_user_id
                        async: false,
                        success: function (data) {
                            ownerUserId = data;
                        }
                    });

                    return ownerUserId;
                }

                // Click event for the send button
                $('#btnSend').click(function () {
                    sendMessage();
                });

                // Keypress event for the Enter key in the input field
                $('#chatmsg').keypress(function (e) {
                    if (e.which == 13) {
                        e.preventDefault();
                        sendMessage();
                    }
                });

                // Scroll event to detect manual scrolling
                $('#loadchat').scroll(function () {
                    manualScroll = ($('#loadchat').scrollTop() + $('#loadchat').innerHeight() < $('#loadchat')[0].scrollHeight);
                });

                // Periodic function to load chat messages
                function loadChat() {
                    $.post({
                        url: "../ajax/LOADCHAT_RENTER.php",
                        data: { land_id: land_id }
                    }).done(function (data) {
                        // Get the current scroll position
                        let currentScroll = $('#loadchat').scrollTop();

                        // Reverse the order of chat messages before inserting
                        $('#loadchat').html(data);

                        // If user has not manually scrolled up, scroll down
                        if (!manualScroll) {
                            scrollToBottom();
                        } else {
                            // If user manually scrolled up, maintain the scroll position
                            $('#loadchat').scrollTop(currentScroll);
                        }

                        console.log(data);
                    });
                }

                // Initial load of chat messages
                loadChat();

                // Periodic function to load chat messages
                setInterval(function () {
                    loadChat();
                }, 13);

                function scrollToBottom() {
                    $('#loadchat').scrollTop($('#loadchat')[0].scrollHeight);
                }
            }
        });
    </script>




    <!-- <script>
        // Automatically open the first user-details and set its id in the URL
        $(document).ready(function () {
            // Get the id of the first user-details
            var firstUserId = $(".btn-user:first").data("id");

            // Check if house details are not yet defined
            if (typeof $houseImage === 'undefined' || typeof $houseName === 'undefined' || typeof $houseType === 'undefined') {
                // Call the showChatDetails function with the first user's id
                showChatDetails(firstUserId);
            }
        });
    </script> -->

    <script>
        function showChatDetails(id) {
        // Access the hidden input fields for the selected user
        var userIdInput = document.querySelector(`.user-details #user-id-${id}`);
        var houseIdInput = document.querySelector(`.user-details #house-id-${id}`);
        var ownerUserIdInput = document.querySelector(`.user-details #owner-user-id-${id}`);
        var renterUserIdInput = document.querySelector(`.user-details #renter-user-id-${id}`);

        // Access the values from the hidden input fields
        var userId = userIdInput.value;
        var houseId = houseIdInput.value;
        var ownerUserId = ownerUserIdInput.value;
        var renterUserId = renterUserIdInput.value;

        // Display an alert with the values
        // alert("User ID: " + userId + "\nHouse ID: " + houseId + "\nOwner User ID: " + ownerUserId + "\nRenter User ID: " + renterUserId);

        // Update the URL to include the parameters
        var url = `../data_page/renters_dashboard_2.php?id=${encodeURIComponent(id)}`;

        // Redirect to the updated URL
        window.location.href = url;
    }

    </script>



    <?php

    ?>
        </body>
    </html>