<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1)
{
    if (isset($_POST['save_form'])) {
        $name = $_POST['name2'];
        $query = "SELECT name FROM statement";
$result = mysqli_query($conn, $query);
// Check if the query was executed successfully
if ($result) { 
    while ($row = mysqli_fetch_assoc($result)) {
    if ($row['name'] == $name){                            
    echo '<script>alert("هذا الاسم متواجد بالفعل"); window.location.href = "addtremove.php";</script>';
    exit;
    }
}
}
    
        $sql = "INSERT INTO statement (name) VALUES (?)";
    
        $stmt = mysqli_stmt_init($conn);
    
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            // Handle the database error here
            die("Error preparing statement: " . mysqli_error($conn));
        }
    
        mysqli_stmt_bind_param($stmt, "s", $name);
    
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
    
            // Unset the POST variable after successful insertion
            unset($name);
            header('Location: addtremove.php');
            exit;
        } else {
            // Handle the database error here
            echo '<script>alert("حدث خطأ أثناء ادخال البيانات"); window.location.href = "addtremove.php";</script>';
            exit;
        }
}
    

if (isset($_POST['delete'])) {
    // Get the selected oil name from the dropdown list
    $selected_oil = $_POST['oil_type'];

    $conn = require __DIR__ . "/connect.php";

    // Create a delete query based on the selected oil name
    $delete_query = "DELETE FROM statement WHERE name = ?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $delete_query)) {
        // Handle the database error here
        die("Error preparing statement: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $selected_oil);

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Deletion successful, you can redirect or show a success message here
        // For example, redirect to the same page to refresh the dropdown list
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    } else {
        // Deletion failed, show an error message or handle the error accordingly
        echo '<script>alert("حدث خطأ أثناء حذف الاسم"); window.location.href = "addtremove.php";</script>';
        exit;
    }
}
}
mysqli_close($conn);
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
    <link rel="stylesheet" href="css/style3.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css"> 
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>حركه الخزينه</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="nav1">
            <h1>حركه الخزينه</h1>
           
        </div>
        </div>
  <div class="form-container">
    
  
      <form style="margin-left: 400px;" class="form1" method="post" >
        <h2 >اضافه</h2>
        <label for="name2"></label>
        <input type="text" placeholder="ادخل البيان" id="name2" name="name2" required>
        <br>
        <button style="margin-bottom: -50px; margin-top: 30px; margin-left: 37px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
      </form>
    </div>
  
    
<form style="margin-left: 400px;" class="form3" method="post">
    <h2 style="margin-top: 100px;">تعديل</h2>
    <label for="email3"></label>
    <select style="margin-left: 50px; margin-top: 20px" name="oil_type" id="oil_type">
        <?php
        $conn = require __DIR__ . "/connect.php";
        // Prepare and execute the database query
        $query = "SELECT name FROM statement";
        $results = mysqli_query($conn, $query);

        if ($results && mysqli_num_rows($results) > 0) {
            // Loop through the rows to generate options
            while ($row = mysqli_fetch_assoc($results)) {
                $name = $row['name'];
                echo "<option value=\"$name\">$name</option>";
            }
        } else {
            echo "<option value=\"\">لا يوجد بيانات</option>";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </select>
    <br>
    <br>
    <button style="margin-bottom: 30px; margin-top: 5px; margin-left: 35px;" id="myform" type="submit" class="save" name="delete">حذف</button>
</form>
  
  
  </div>

       
        
    </div>
    <!-- Vertical navbar section  -->
    <section id="navbar" class="nav-bar">
        <div class="menu-toggle">
            <div class="hamburger">
                <span></span>
            </div>
        </div>
        <div class="sidebar close">
            <div class="logo-details">
                <span class="logo_name">
                    <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">
                </span>
            </div>
        <ul class="nav-links">
            <li>
                <a href="adminhome.php" id="btn" >
                    <i class='bx bxs-home'></i>
                    <span class="link_name">نظره عامه</span>
                </a>
            </li>
            <li>
                <a href="dailytt.php">
                    <i class='bx bxs-book-add' ></i>
                    <span class="link_name">يوميه الورديه</span>
                </a>
            </li>
            <li>
                <a href="oil.php">
                    <i class='bx bx-folder-plus'></i>
                    <span class="link_name">الزيوت</span>
                </a>
            </li>
            <li>
                <a href="expensis.php">
                    <i class='bx bx-folder-plus'></i>
                    <span class="link_name">المصاريف</span>
                </a>
            </li>
            <li>
                <a href="bons.php">
                    <i class='bx bxs-file-plus'></i>
                    <span class="link_name">البونات</span>
                </a>
            </li>
            <li>
                <a href="client.php">
                    <i class='bx bx-comment-error'></i>
                    <span class="link_name">عملاء اجله</span>
                </a>
            </li>
            <li>
                <a href="daitre.php">
                    <i class='bx bx-comment-error'></i>
                    <span class="link_name">يوميه الخزينه </span>
                </a>
            </li>
            <li>
                <a href="tamwenpass.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">دفتر التموين</span>
                </a>
            </li>
            <li>
                <a href="tremove.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name"> حركه الخزينه</span>
                </a>
            </li>
            <li>
                <a href="dailyqed.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name"> قيد يومي</span>
                </a>
            </li>
            <li>
                <a href="employee.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">الموظفين </span>
                </a>
            </li>
            <li>
                <a href="systemmove.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">حركه النظام </span>
                </a>
            </li>
            <li>
            <a href="logout.php">
                    <i class='bx bx-cog'></i>
                    <span class="link_name">تسجيل الخروح</span>
                </a>
            </li>
        </ul>
    </div>
</section>
<script src="script.js"></script>
</body>
</html>