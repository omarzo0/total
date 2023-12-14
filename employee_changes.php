<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');
// Function to update the values in the table and insert into System_Move table
function updateData($conn, $name, $national_id, $mobile_num, $id, $job , $salary , $hired_time) {
        // Use prepared statement to avoid SQL injection
        $sql = "UPDATE employee SET name = ?, national_id = ?, mobile_num = ? , job = ? , salary = ? , hired_time = ? WHERE id = '$id' AND national_id = '$national_id' ";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $name, $national_id, $mobile_num , $job, $salary , $hired_time);

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "employee_changes.php";</script>';
}

}

if (is_logged_in() || $_SESSION['id'] == 1) {
    // Fetch the data again after insertion
   
    $name =  $_SESSION['name'] ;
    $national_id =  $_SESSION['national_id'];
    $mobile_num=  $_SESSION['mobile_num'];
    $job =  $_SESSION['job'];
    $salary=  $_SESSION['salary'];
    $hired_time =  $_SESSION['hired_time'];
    $id = $_SESSION['emp'];
    

     // Fetch the data again after insertion
     $query = "SELECT id, hired_time, name, national_id, mobile_num, job, salary FROM employee WHERE name = '$name' AND national_id = '$national_id' AND id = '$id'";
     $result = mysqli_query($conn, $query);

     if ($result) {
        $row = mysqli_fetch_assoc($result);
        $name = $row['name'];
        $national_id = $row['national_id'];
        $mobile_num = $row['mobile_num'];
        $job = $row['job'];
        $salary = $row['salary'];
        $hired_time = $row['hired_time'];
     }
     
     if (isset($_POST['edit'])) {
        // Get the updated values from the form  
        $name = $_POST['name'];
        $national_id = $_POST['national_id'];
        $mobile_num = $_POST['mobile_num'];
        $job = $_POST['job'];
        $salary = $_POST['salary'];
        $hired_time = $_POST['hired_time'];
       
        // Update the data using the updateData() function
        updateData($conn, $name, $national_id, $mobile_num, $id, $job , $salary , $hired_time);
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
    <br><br><br><br><br><br><br><br>
    <form class="form3" method="post">
    <h2 style="margin-top: -150px;">بيانات الموظف</h2>
    <label for="name3">الاسم</label>
    <input type="text" placeholder="الاسم" id="name3" name="name" required value="<?php echo isset($name) ? $name : ''; ?>">
    <br>

    <label for="national_id">الرقم القومي</label>
    <input type="text" placeholder="الرقم القومي" id="national_id" name="national_id" required value="<?php echo isset($national_id) ? $national_id : ''; ?>">
    <br>

    <label for="mobile_num">رقم الهاتف</label>
    <input type="text" placeholder="رقم الهاتف" id="mobile_num" name="mobile_num" required value="<?php echo isset($mobile_num) ? $mobile_num : ''; ?>">
    <br>

    <label for="job">الوظيفة</label>
    <input type="text" placeholder="الوظيفة" id="job" name="job" required value="<?php echo isset($job) ? $job : ''; ?>">
    <br>

    <label for="salary">المرتب</label>
    <input type="text" placeholder="المرتب" id="salary" name="salary" required value="<?php echo isset($salary) ? $salary : ''; ?>">
    <br>

    <label for="hired_time">معاد التعيين</label>
    <input type="text" placeholder="معاد التعيين" id="hired_time" name="hired_time" required value="<?php echo isset($hired_time) ? $hired_time : ''; ?>">
    <br>

    <button style="margin-bottom: 30px; margin-top: 30px; margin-left: 35px;" id="myform" type="submit" class="save" name="edit">حفظ</button>
    <br>
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