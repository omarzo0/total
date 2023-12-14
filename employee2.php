<?php
require_once('loggedin.php');

// Check if the user is logged in or has admin privileges
if (is_logged_in() || $_SESSION['id'] == 1) {
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = require __DIR__ . "/connect.php";

    if(isset($_POST['save_form'])){
        // Validate and sanitize form inputs
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
    
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Check if username already exists
        $check_sql = "SELECT * FROM user_system WHERE username = ?";
        $check_stmt = mysqli_stmt_init($conn);
    
        if (!mysqli_stmt_prepare($check_stmt, $check_sql)) {
            die(mysqli_error($conn));
        }
    
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
    
        $result = mysqli_stmt_get_result($check_stmt);
    
        if (mysqli_num_rows($result) > 0) {
            echo '<script>alert("يوجد مثل اسم مستخدم مثل هذا اختار اسم مستخدم اخر"); window.location.href = "employee2.php";</script>';
        } else {
            // Proceed with insertion
            $sql = "INSERT INTO user_system (username, user_type, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
    
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sss", $username, $user_type, $hashedPassword);
    
                if (mysqli_stmt_execute($stmt)) {
                    // Redirect to the login page
                    header('Location: employee2.php');
                    exit;
                } else {
                    echo "Error inserting data: " . mysqli_stmt_error($stmt);
                }
    
                mysqli_stmt_close($stmt);
            } else {
                echo "Error preparing statement: " . mysqli_error($conn);
            }
        }
    
        mysqli_stmt_close($check_stmt);
    }
    

    // View employee by name
    if (isset($_POST['form_search'])) {
        $search_name = isset($_POST['username']) ? $_POST['username'] : '';
    
        $sql = "SELECT id, username, user_type, password FROM user_system WHERE username LIKE '$search_name%'";
        $results = mysqli_query($conn, $sql);
    
        if (!$results) {
            // Handle SQL query error
            echo "Error executing SQL query: " . mysqli_error($conn);
        } else {
            // Check if there are any rows in the result set
            if (mysqli_num_rows($results) > 0) {
                $row = mysqli_fetch_assoc($results);
                $username = $row['username'];
                $user_type = $row['user_type'];
                $password = 'لا يمكن اظهار كلمة المرور';

            } else {
                $username = "لا يوجد بيانات";
                $user_type = "لا يوجد بيانات";
                $password = "لا يوجد بيانات";
            }
        }
        
    }

    if (isset($_POST['edit']))
    {
        // Get the button's value (trumba_type and trumba_number)
        $buttonValue = $_POST['edit'];
        // Split the value into an array
        $values = explode('|', $buttonValue);
        // Get the individual trumba_type and trumba_number
        $username = mysqli_real_escape_string($conn, $values[0]);
        $user_type = mysqli_real_escape_string($conn, $values[1]);
        $id = mysqli_real_escape_string($conn, $values[2]);
    
        // Use the trumba_type and trumba_number in the SQL query

        $query = "SELECT id , username, user_type, password FROM user_system WHERE username = '$username' AND user_type = '$user_type' AND id = '$id'";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_type'] = $row['user_type'];
            $_SESSION['password'] =  $row['password'];
            $_SESSION['emp_id'] = $row['id'];
            header('Location: employee2_changes.php');
            exit;
        }
        header('Location: employee2_changes.php');
        exit();
    }

    if (isset($_POST['delete']))
    {
        // Get the button's value (trumba_type and trumba_number)
        $buttonValue = $_POST['delete'];
        // Split the value into an array
        $values = explode('|', $buttonValue);
        // Get the individual trumba_type and trumba_number
        $username = mysqli_real_escape_string($conn, $values[0]);
        $user_type = mysqli_real_escape_string($conn, $values[1]);
        $id = mysqli_real_escape_string($conn, $values[2]);
    
        // Use prepared statement to avoid SQL injection
    $sql = "DELETE FROM user_system WHERE (username = ? AND user_type = ? AND id = ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "sss", $username, $user_type, $id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Deletion successful
            echo '<script>alert("تم حذف البيانات بنجاح"); window.location.href = "employee2.php";</script>';
        } else {
            echo '<script>alert("لم يتم العثور على بيانات للحذف"); window.location.href = "employee2.php";</script>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("حدث خطأ أثناء حذف البيانات"); window.location.href = "employee2.php";</script>';
    }
    }
    
    mysqli_close($conn);
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
    <link rel="stylesheet" href="css/style3.css">
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css"> 
    <link rel="shortcut icon" type="x-icon" href="">

    <title>المستخدمين</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="nav1">
            <h1>المستخدمين</h1>
            <a href="employee.php"><button style="margin-left: 100px;" class="save">الموظفين</button></a>
            <a href="employee2.php"><button class="save">المستخدمين</button></a>
        </div>
    </div>
  <div class="form-container">
    
    <form class="form2" method="post" >
        <h2>بحث</h2>
        <input type="text" placeholder="أدخل اسم المستخدم" id="name1" name="username" required>
        <br>
  
        <button style="
                    margin-top: 20px;
                    margin-left: 35px;"  id="myform" type="submit" class="save" name="form_search" >بحث</button>
      </form>
  
      <form style="margin-right: 100px;" class="form1"  method="post">
        <h2>اضافه</h2>
        <label for="name3"></label>
        <input type="text" placeholder="اسم المستخدم" id="name3" name="username" required>
    
        <label for="email3"></label>
        <input type="text" placeholder="كلمه السر" id="email3" name="password" required>
  
        <label for="email3"></label>
        <select  style="margin-left: 20px;
        width:230px" name="user_type" >
          <option value="ادمن">أدمن</option>
          <option value="محاسب">محاسب</option>
          <option value="عامل">عامل</option>
        </select>      <br>
    
        <button style="margin-bottom: 10px;
                    margin-top: 30px;
                    margin-left: 35px;"  id="myform" type="submit" class="save" name="save_form" >حفظ</button>
      </form>
    </div>
  
    <form  class="form3" method="post" >
      <h2 style="margin-top: 20px;">تعديل</h2>
      <label for="name3"></label>
      <input type="text" placeholder="اسم المستخدم" id="name3" name="name3" readonly value="<?php echo isset($username) ? $username : ''; ?>">
  
      <label for="email3"></label>
      <input type="text" placeholder="كلمه السر" id="email3" name="email3" readonly value="<?php echo isset($password) ? $password : ''; ?>">

      <label for="email3"></label>
      <select name="user_type" style="margin-left: 20px; width: 230px" >
    <?php if (empty($user_type)) : ?>
        <option value="" selected>نوع الوظيفة</option>
    <?php else : ?>
        <option value="">نوع الوظيفة</option>
        <option value="ادمن" <?php if ($user_type === "ادمن") echo "selected"; ?>>أدمن</option>
        <option value="محاسب" <?php if ($user_type === "محاسب") echo "selected"; ?>>محاسب</option>
        <option value="عامل" <?php if ($user_type === "عامل") echo "selected"; ?>>عامل</option>
    <?php endif; ?>
</select>

    <br>

    <?php 
if (empty($user_type)) {
    echo '<button style="margin-bottom: 30px;
    margin-top: 5px;
    margin-left: 35px;" id="myform" type="submit" class="save">لا توجد بيانات</button>';

} else {
                  $nameValue = $row['username'] . '|' . $row['user_type'] . '|' . $row['id'];
                  echo '<button style="margin-bottom: 10px;
                                      margin-top: 30px;
                                      margin-left: 35px;" id="myform" type="submit" class="save" name="edit" value="' . $nameValue . '">تعديل</button>';
                  echo '<br>';
                  echo '<button style="margin-bottom: 30px;
                                margin-top: 5px;
                                margin-left: 35px;" id="myform" type="submit" class="save" name="delete" value="' . $nameValue . '">حذف</button>';
}
?>


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