<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');
// Function to update the values in the table and insert into System_Move table
function updateData($conn, $username, $password, $user_type, $id) {
        // Use prepared statement to avoid SQL injection
         // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE user_system SET username = ?, user_type = ?, password = ? WHERE id = '$id'";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $username, $user_type, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "employee2_changes.php";</script>';
}

}

if (is_logged_in() || $_SESSION['id'] == 1) {
    // Fetch the data again after insertion
   
    $username =  $_SESSION['username'] ;
    $user_type =  $_SESSION['user_type'];
    $id=  $_SESSION['emp_id'];
    

     // Fetch the data again after insertion
     $query = "SELECT username, user_type, id, password FROM user_system WHERE id = '$id' ";
     $result = mysqli_query($conn, $query);

     if ($result) {
        $row = mysqli_fetch_assoc($result);
     $user_type = $row['user_type'] ;
     $username  = $row['username'];
     $id  = $row['id'];
    
     }
     
     if (isset($_POST['edit'])) {
        // Get the updated values from the form
        $username = $_POST['username'];
        $user_type = $_POST['user_type'];
        $password = $_POST['password'];
       
        // Update the data using the updateData() function
        updateData($conn, $username,$password, $user_type, $id);
    }

    mysqli_close($conn);
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
    <link rel="stylesheet" href="css/style3.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css"> 
    <link rel="shortcut icon" type="x-icon" href="">

    <title>الموظفين</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="nav1">
            <h1>الموظفين</h1>
            <a href="employee.php"><button style="margin-left: 100px;" class="save">الموظفين</button></a>
            <a href="employee2.php"><button class="save">المستخدمين</button></a>
        </div>
    
  <form class="form1" method="post">
  <label for="name3">اسم المستخدم</label>
        <input type="text" id="name3" name="username" required value="<?php echo isset($username) ? $username : ''; ?>">

        <br>
        <label for="name3"> نوع الوظيفة</label>
    <input type="text" id="national_id" name="user_type" required value="<?php echo isset($user_type) ? $user_type : ''; ?>">
   
        
    <br>
    <label for="name3">كلمية السر</label>
    <input type="text"  id="mobile_num" name="password" value="<?php echo isset($password) ? $password : ''; ?>">

    <br>

        <button style="margin-bottom: -50px;
                    margin-top: 30px;
                    margin-left: 37px;" name="edit" id="myform1" type="submit" class="save" >حفظ</button>
    </form>
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