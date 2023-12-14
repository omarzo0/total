<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = require __DIR__ . "/connect.php";

    // Validate and sanitize form inputs
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Prepare and execute the SQL statement
    $sql = "INSERT INTO admin (Name, password) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $name, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            // Redirect to the login page
            header('Location: index.php');
            exit;
        } else {
            echo "Error inserting data: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>



<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>booking</title>
   
    <link rel="stylesheet" type="text/css" href="css/pickmeup.css">
    <link rel="stylesheet" type="text/css" href="css/handle-counter.min.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    
    <link rel="shortcut icon" type="x-icon" href="images/logo.png">
    
    
</head>
    
<body>
    <header class="tm-header">
        <img class="tm-logo" src="images/download.png" alt="Galala University logo">
        <nav class="tm-nav" >
    </header>
    <section class="tm-main">
        <div class="main-heading">
            <h2>Student Information</h2>
        </div>   
     </section>
     <section class="tm-booking">
        <form id="booking-form" method="post" enctype="multipart/form-data" >
            <div class="tm-form">
                <label for="student_name">Name</label> 
                <input type="text" id="student_name" name="name" placeholder="Full Name" >

                <label for="student_password">Password</label>
                <br>
                            <input type="password"  id="student_password" name="password" >
                            
                <input type="submit" value="Proceed">
            </div>
        </form>
    </section>
    <footer class="tm-footer">
        <div class="tm-us">
        <p class="bold"></p>
        <p>All rights reserved &copy; 2023</p>    
        </div>
       
        <div class="tm-address">
        <p> Galala Plateau , Attaka , Suez<br>
            hotline:15888 <br>
            Email:info@gu.edu.eg</p>    
        </div>
        <br/>
    <div class="tm-media">
        <a href="#"> <img src="images/fb.png"></a>
        <a href="#"> <img src="images/G.png"> </a>
        <a href="#"> <img src="images/twittr.png"> </a>
        <a href="#"> <img src="images/insta.png"> </a>
        </div>        
    </footer>
</body>
</html>