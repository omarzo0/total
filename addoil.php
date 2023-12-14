<?php
require_once ('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1)
{
    
    $conn = require __DIR__ . "/connect.php";
        $id = $_SESSION['id'];
        // Fetch the data again after insertion
    $query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
    $result_type = mysqli_query($conn, $query_type);
    
    if ($result_type) {
    $row = mysqli_fetch_assoc($result_type);
    $username = $row['username'];
    $user_type = $row['user_type'];
    }

// add employee
if (isset($_POST['form_add'])) {
    $name = $_POST['name'];
    $oil_price = $_POST['oil_price'];

    $sql = "INSERT INTO oil (name, price)
            VALUES (?, ?)";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ss",
        $name,
        $oil_price
    );
    $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
    $result_move = mysqli_query($conn, $query_move);
    
    if ($result_move) {
        $row_move = mysqli_fetch_assoc($result_move);
        $currentValue_move = $row_move['value'];

        if ($currentValue_move === "1") {
        // Insert into System_Move table for the start value
$action_description_start =  $oil_price. " بسعر " . $name . "  تم اضافة" ;
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $username, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);
        }
    }

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Unset the POST variables after successful insertion
        unset($name);
        unset($oil_price);

        header('Location: addoil.php');
        exit;
    } else {
        echo "Error inserting data: " . mysqli_stmt_error($stmt);
    }
    mysqli_close($conn);
}


if (isset($_POST['form_search'])) {
    $search_name = isset($_POST['name1']) ? $_POST['name1'] : '';

    // Use prepared statement to avoid SQL injection
    $sql = "SELECT name, price FROM oil WHERE name LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $search_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!empty($row)) {
        $nameValue = $row['name'];
        $oil_price = $row['oil_price'];
    } else {
        $nameValue = "لا يوجد بيانات";
        $oil_price = "لا يوجد بيانات";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}



if (isset($_POST['delete'])) {
    $search_name = $_POST['name_delete']; // Assuming $nameValue contains the name you want to delete

    // Use prepared statement to avoid SQL injection
    $sql = "DELETE FROM oil WHERE name LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $search_name);
    mysqli_stmt_execute($stmt);

    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
    $result_move = mysqli_query($conn, $query_move);
    
    if ($result_move) {
        $row_move = mysqli_fetch_assoc($result_move);
        $currentValue_move = $row_move['value'];

        if ($currentValue_move === "1") {
            $query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
    $result_type = mysqli_query($conn, $query_type);
    
    if ($result_type) {
    $row = mysqli_fetch_assoc($result_type);
    $username = $row['username'];
    $user_type = $row['user_type'];
    }
        // Insert into System_Move table for the start value
$action_description_start = $search_name . "  تم حذف";
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $username, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);
        }
    }
        // Deletion successful
        echo '<script>alert("تم حذف البيانات بنجاح"); window.location.href = "addoil.php";</script>';
    } else {
        echo '<script>alert("لم يتم العثور على بيانات للحذف"); window.location.href = "addoil.php";</script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}


if (isset($_POST['edit'])) {
    $search_name = $_POST['name_delete']; // Assuming $nameValue contains the name you want to edit
    $price_delete = $_POST['price_delete'];

    // Use prepared statement to avoid SQL injection
    $sql = "UPDATE oil SET price = ? WHERE name LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $price_delete, $search_name);
    mysqli_stmt_execute($stmt);

    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Editing successful
        echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "addoil.php";</script>';
    } else {
        // No rows were affected, so the data was not found
        echo '<script>alert("لم يتم العثور على بيانات للتعديل"); window.location.href = "addoil.php";</script>';
    }

    mysqli_stmt_close($stmt);
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
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>الزيوت</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="nav1">
            <h1>الزيوت</h1>
        </div>
        </div>
  <div class="form-container">
    
    <form class="form2" method="post" >
        <h2>بحث</h2>
        <input type="text" placeholder="أدخل الاسم" id="name1" name="name1" required>
        <br>
  
        <button style="
                    margin-top: 20px;
                    margin-left: 35px;
                    margin-bottom:200px"  id="myform" type="submit" class="save" name="form_search">بحث</button>
      </form>
  
      <form class="form1" method="post" >
        <h2>اضافه</h2>
        <label for="name"></label>
        <input type="text" placeholder="ادخل الاسم" id="name" name="name" required>
        <label for="name"></label>

        <input type="number" placeholder="أدخل السعر" id="oil_price" name="oil_price" required>
        <label for="oil_price"></label>

       
        <br>
  
        <button style="margin-bottom: -50px;
                    margin-top: 30px;
                    margin-left: 37px;"  id="myform" type="submit" class="save" name="form_add" >حفظ</button>
      </form>
    </div>
  
    <form  class="form3" method="post" >
      <h2 style="margin-top: -150px;">تعديل</h2>
      <label for="name3"></label>
      <input type="text" placeholder="الاسم" id="name3" name="name_delete" required value="<?php echo isset($nameValue) ? $nameValue : ''; ?>">
  
      <label for="email3"></label>
      <input type="text" placeholder="السعر" id="email3" name="price_delete" required value="<?php echo isset($oil_price) ? $oil_price : ''; ?>">

     
      <br>
  
      <button style="margin-bottom: 30px;
                  margin-top: 30px;
                  margin-left: 35px;"  id="myform" type="submit" class="save" name="edit" >حفظ</button>
        <br>
        <button style="margin-bottom: 30px;
                  margin-top: 5px;
                  margin-left: 35px;"  id="myform" type="submit" class="save" name="delete" >حذف</button>

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