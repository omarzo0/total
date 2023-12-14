<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = require __DIR__ . "/connect.php";

    $username = $_POST["username"];
    $password = $_POST["password"];
   
    // Prepare and execute the database query for students
    $query = "SELECT id, password, username, user_type FROM user_system WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    $query_login = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_login_h'";
    $result_login = mysqli_query($conn, $query_login);
    if ($result_login) {
        $row_login = mysqli_fetch_assoc($result_login);
        $currentValue = $row_login['value'];
    }
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row && password_verify($password, $row['password'])) {
            session_start();
            $_SESSION['username'] = $row['username'];
            $_SESSION['id'] = $row['id'];
            $user_type = $row ['user_type'];
            // Capture user agent
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        
            // Determine whether the user agent is from a website or a desktop application
            $server = (strpos($user_agent, 'Mozilla') !== false || strpos($user_agent, 'Chrome') !== false)
                ? 'موقع'
                : 'برنامج';
               
                if ($currentValue === "1")
                {
            // Prepare and execute the SQL statement to insert login history
            $sql = "INSERT INTO login_h (name, sys_type) VALUES (?, ?)";
            $stmt_log = mysqli_prepare($conn, $sql);
        
            if ($stmt_log) {
                mysqli_stmt_bind_param($stmt_log, "ss", $username, $server);
                mysqli_stmt_execute($stmt_log);
                mysqli_stmt_close($stmt_log);
            } else {
                echo "Error preparing statement: " . mysqli_error($conn);
            }
            if ($user_type == 'ادمن'){
            header('Location: adminhome.php');
            exit();}else {
                header("location: dailytt.php");
                exit();
            }
        }
        else {
            if ($user_type == 'ادمن'){
                header('Location: adminhome.php');
                exit();}
                else {
                    header("location: dailytt.php");
                    exit();
                }
        }
        }
        else {
            echo '<script>alert("اسم المستخدم او كلمة المرور غير صحيحة"); window.location.href = "index.php";</script>';
            exit;
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Oleo+Script&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style2.css">
    <link rel="stylesheet" href="css/start.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">    
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>محطه الندي</title>
</head>


<body>
    <form class="form4" method="post">
        <div style="margin-top: 20px;" class="logo-details">
            <span class="logo_name">
                <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">
            </span>
        </div>
        <h2>محطه توتال الندي</h2>
        <label for="name"></label>
        <input type="text" placeholder="أدخل اسم المستخدم" id="name" name="username" required>
        <br>
        <label for="password"></label>
        <input type="password" placeholder="أدخل كلمه المرور " id="password" name="password" required>
        <br>
        <button style="
                    margin-top: 20px;
                    margin-left: 10px;"  id="myform" type="submit" class="save">دخول</button>
      </form>
    
    
</body>

</html>