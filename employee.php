<?php
require_once('loggedin.php');

// Check if the user is logged in or has admin privileges
if (is_logged_in() || $_SESSION['id'] == 1) {
    $conn = require __DIR__ . "/connect.php";

    // Add employee
if (isset($_POST['form_add'])) {
    $name = $_POST['name'];
    $national_id = $_POST['national_id'];
    $number = $_POST['number'];
    $job = $_POST['job'];
    $salary = $_POST['salary'];

    // Check if national_id already exists
    $check_sql = "SELECT * FROM employee WHERE national_id = ?";
    $check_stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($check_stmt, $check_sql)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($check_stmt, "s", $national_id);
    mysqli_stmt_execute($check_stmt);

    $result = mysqli_stmt_get_result($check_stmt);

    if (mysqli_num_rows($result) > 0) {
        echo '<script>alert(" يوجد هذا الرقم القومى فى قاعدة البيانات"); window.location.href = "employee.php";</script>';
    } else {
        // Proceed with insertion
        $insert_sql = "INSERT INTO employee (name, national_id, mobile_num, job, salary)
                VALUES (?, ?, ?, ?, ?)";

        $insert_stmt = mysqli_stmt_init($conn);

        if (!mysqli_stmt_prepare($insert_stmt, $insert_sql)) {
            die(mysqli_error($conn));
        }

        mysqli_stmt_bind_param($insert_stmt, "sssss",
            $name,
            $national_id,
            $number,
            $job,
            $salary
        );
        if (mysqli_stmt_execute($insert_stmt)) {
            mysqli_stmt_close($insert_stmt);

            // Unset the POST variables after successful insertion
            unset($_POST['name']);
            unset($_POST['national_id']);
            unset($_POST['number']);
            unset($_POST['job']);
            unset($_POST['salary']);

            header('Location: employee.php');
            exit;
        } else {
            echo "Error inserting data: " . mysqli_stmt_error($insert_stmt);
        }
    }

    mysqli_stmt_close($check_stmt);
}

    // View employee by name
    if (isset($_POST['form_search'])) {
        $search_name = isset($_POST['name1']) ? $_POST['name1'] : '';
    
        $sql = "SELECT * FROM employee WHERE name LIKE '$search_name%'";
        $result = mysqli_query($conn, $sql);
    
        if (!$result) {
            // Handle SQL query error
            echo "Error executing SQL query: " . mysqli_error($conn);
        } else {
            // Check if there are any rows in the result set
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $nameValue = $row['name'];
                $nationalIdValue = $row['national_id'];
                $mobileNumValue = $row['mobile_num'];
                $jobValue = $row['job'];
                $salaryValue = $row['salary'];
                $hiredTimeValue = $row['hired_time'];
            } else {
                $nameValue = "لا يوجد بيانات";
                $nationalIdValue = "لا يوجد بيانات";
                $mobileNumValue = "لا يوجد بيانات";
                $jobValue = "لا يوجد بيانات";
                $salaryValue = "لا يوجد بيانات";
                $hiredTimeValue = "لا يوجد بيانات";
            }
        }
        
        unset($_POST['name1']);
    }


    if (isset($_POST['edit']))
    {
        // Get the button's value (trumba_type and trumba_number)
        $buttonValue = $_POST['edit'];
        // Split the value into an array
        $values = explode('|', $buttonValue);
        // Get the individual trumba_type and trumba_number
        $national_id = mysqli_real_escape_string($conn, $values[0]);
        $name = mysqli_real_escape_string($conn, $values[1]);
        $id = mysqli_real_escape_string($conn, $values[2]);
    
        // Use the trumba_type and trumba_number in the SQL query

        $query = "SELECT id, hired_time, name, national_id, mobile_num, job, salary FROM employee WHERE name = '$name' AND national_id = '$national_id' AND id = '$id'";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['name'] = $row['name'];
            $_SESSION['national_id'] = $row['national_id'];
            $_SESSION['mobile_num'] =  $row['mobile_num'];
            $_SESSION['job'] = $row['job'];
            $_SESSION['salary']= $row['salary'];
            $_SESSION['hired_time']= $row['hired_time'];
            $_SESSION['emp']= $row['id'];
            header('Location: employee_changes.php');
            exit;
        }
        header('Location: employee_changes.php');
        exit();
    }

    if (isset($_POST['delete']))
    {
        // Get the button's value (trumba_type and trumba_number)
        $buttonValue = $_POST['delete'];
        // Split the value into an array
        $values = explode('|', $buttonValue);
        // Get the individual trumba_type and trumba_number
        $national_id = mysqli_real_escape_string($conn, $values[0]);
        $name = mysqli_real_escape_string($conn, $values[1]);
        $id = mysqli_real_escape_string($conn, $values[2]);
    
        // Use prepared statement to avoid SQL injection
    $sql = "DELETE FROM employee WHERE (national_id = ? AND name = ? AND id = ?)";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Bind parameters to the prepared statement
        mysqli_stmt_bind_param($stmt, "sss", $national_id, $name, $id);

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
           
            echo '<script>alert("تم حذف البيانات بنجاح"); window.location.href = "employee.php";</script>';
        } else {
            echo '<script>alert("لم يتم العثور على بيانات للحذف"); window.location.href = "employee.php";</script>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("حدث خطأ أثناء حذف البيانات"); window.location.href = "employee.php";</script>';
    }
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
        </div>
  <div class="form-container">
    
    <form class="form2" method="post">
        <h2>بحث</h2>
        <input type="text" placeholder="أدخل الاسم" id="name1" name="name1" required>
        <br>
  
        <button style="
                    margin-top: 20px;
                    margin-left: 35px;" name="form_search"  id="myform" type="submit" class="save">بحث</button>
    </form>
  
    <form class="form1" method="post">
        <h2>اضافه</h2>
        <label for="name"></label>
        <input type="text" placeholder="ادخل الاسم" id="name" name="name" required>
        <label for="name"></label>

        <input type="text" placeholder="أدخل الرقم القومي" id="national_id" name="national_id" required>
        <label for="national_id"></label>

        <input type="text" placeholder="أدخل رقم الهاتف" id="number" name="number" required>
        <label for="number"></label>

        <input type="text" id="job" placeholder="أدخل الوظيفه" name="job" required>
        <label for="job"></label>

        <input type="text" id="salary" placeholder="أدخل المرتب" name="salary" required>
        <br>
  
        <button style="margin-bottom: -50px;
                    margin-top: 30px;
                    margin-left: 37px;" name="form_add" id="myform1" type="submit" class="save" >حفظ</button>
    </form>
    </div>
    
    <form class="form3" method="post">
    <h2 style="margin-top: -150px;">بيانات الموظف</h2>
    <label for="name3">الاسم</label>
    <input type="text" placeholder="الاسم" id="name3" name="name3" value="<?php echo isset($nameValue) ? $nameValue : ''; ?>"readonly>
    <br>

    <label for="national_id">الرقم القومي</label>
    <input type="text" placeholder="الرقم القومي" id="national_id" name="national_id" value="<?php echo isset($nationalIdValue) ? $nationalIdValue : ''; ?>"readonly>
    <br>

    <label for="mobile_num">رقم الهاتف</label>
    <input type="text" placeholder="رقم الهاتف" id="mobile_num" name="mobile_num" value="<?php echo isset($mobileNumValue) ? $mobileNumValue : ''; ?>" readonly>
    <br>

    <label for="job">الوظيفة</label>
    <input type="text" placeholder="الوظيفة" id="job" name="job" value="<?php echo isset($jobValue) ? $jobValue : ''; ?>" readonly>
    <br>

    <label for="salary">المرتب</label>
    <input type="text" placeholder="المرتب" id="salary" name="salary" value="<?php echo isset($salaryValue) ? $salaryValue : ''; ?>" readonly>
    <br>

    <label for="hired_time">معاد التعيين</label>
    <input type="text" placeholder="معاد التعيين" id="hired_time" name="hired_time" value="<?php echo isset($hiredTimeValue) ? $hiredTimeValue : ''; ?>" readonly >
    <br>
    <?php 
if (empty($nationalIdValue)) {
    echo '<button style="margin-bottom: 30px;
    margin-top: 5px;
    margin-left: 35px;" id="myform" type="submit" class="save">لا توجد بيانات</button>';

}
else {
    $nameValue = $row['national_id'] . '|' . $row['name'] . '|' . $row['id'];
    echo '<button style="margin-bottom: 30px; margin-top: 30px; margin-left: 35px;" id="myform" type="submit" class="save" name="edit"  value="' . $nameValue . '">تعديل</button>'; 
    echo '<br>';
    echo '<button style="margin-bottom: 30px; margin-top: 5px; margin-left: 35px;" id="myform" type="submit" class="save" name="delete"   value="' . $nameValue . '">حذف</button>'; }?>
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

<script>
document.getElementById('national_id').addEventListener('input', function() {
  const input = this.value;
  const cleanInput = input.replace(/\D/g, ''); // Remove non-digit characters
  const isValid = cleanInput.length === 14;

  if (isValid) {
    this.setCustomValidity(''); // Clear any previous validation message
  } else {
    this.setCustomValidity('يجب أن يحتوي الرقم القومي على 14 رقمًا.');
  }
});
</script>

</body>

</html>