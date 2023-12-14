<?php
require_once ('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1)
{
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
// add employee
if (isset($_POST['form_add'])) {
    $conn = require __DIR__ . "/connect.php";
    $name2 = $_POST['name2'];
    $nat_id = $_POST['nat_id'];
    $num = $_POST['num'];

    $sql = "INSERT INTO client (name, national_id, mobile_num)
            VALUES (?, ?, ?)";

    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        die(mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "sss",
        $name2,
        $nat_id,
        $num
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Unset the POST variables after successful insertion
        unset($name2);
        unset($nat_id);
        unset($num);

        header('Location: addclient.php');
        exit;
    } else {
        echo "Error inserting data: " . mysqli_stmt_error($stmt);
    }
    mysqli_close($conn);
}


if (isset($_POST['form_search'])) {
    $search_name = isset($_POST['name1']) ? $_POST['name1'] : '';
    $conn = require __DIR__ . "/connect.php";

    // Use prepared statement to avoid SQL injection
    $sql = "SELECT name, national_id, mobile_num FROM client WHERE name LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $search_name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if (!empty($row)) {
        $nameValue = $row['name'];
        $idValue = $row['national_id'];
        $numValue = $row['mobile_num'];
    } else {
        $nameValue = "لا يوجد بيانات";
        $idValue = "لا يوجد بيانات";
        $numValue = "لا يوجد بيانات";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}



if (isset($_POST['delete'])) {
    $search_name = $_POST['id_delete']; // Assuming $nameValue contains the name you want to delete
    $conn = require __DIR__ . "/connect.php";
    $id_type = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id_type'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}

$sql_sel = "SELECT name, national_id, mobile_num FROM client WHERE national_id = ?";
$stmt_sel = mysqli_prepare($conn, $sql_sel);
mysqli_stmt_bind_param($stmt_sel, "s", $search_name);
mysqli_stmt_execute($stmt_sel);
$result_sel = mysqli_stmt_get_result($stmt_sel);
$row_sel = mysqli_fetch_assoc($result_sel);


    // Use prepared statement to avoid SQL injection
    $sql = "DELETE FROM client WHERE national_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $search_name);
    mysqli_stmt_execute($stmt);
    if (!empty($row_sel)) {
        $nameValue = $row_sel['name'];
        $idValue = $row_sel['national_id'];
        $numValue = $row_sel['mobile_num'];
    } 
    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
        $result_move = mysqli_query($conn, $query_move);
        
        if ($result_move) {
            $row_move = mysqli_fetch_assoc($result_move);
            $currentValue_move = $row_move['value'];
    
            if ($currentValue_move === "1") {
            // Insert into System_Move table for the start value
$action_description_start =  $nameValue." تم حذف العميل  " ;
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);


            }
        }
        // Deletion successful
        echo '<script>alert("تم حذف البيانات بنجاح"); window.location.href = "addclient.php";</script>';
    } else {
        echo '<script>alert("لم يتم العثور على بيانات للحذف"); window.location.href = "addclient.php";</script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

if (isset($_POST['edit'])) {
    $search_name = $_POST['name_delete']; // Assuming $nameValue contains the name you want to edit
    $id_delete = $_POST['id_delete'];
    $num_delete = $_POST['num_delete'];
    $conn = require __DIR__ . "/connect.php";
    $id_type = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id_type'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}
// Use prepared statement to avoid SQL injection
$sql_sel = "SELECT name, national_id, mobile_num FROM client WHERE national_id = ?";
$stmt_sel = mysqli_prepare($conn, $sql_sel);
mysqli_stmt_bind_param($stmt_sel, "s", $id_delete);
mysqli_stmt_execute($stmt_sel);
$result_sel = mysqli_stmt_get_result($stmt_sel);
$row_sel = mysqli_fetch_assoc($result_sel);

    // Use prepared statement to avoid SQL injection
    $sql = "UPDATE client SET name = ?, national_id = ?, mobile_num = ? WHERE name LIKE ? OR national_id LIKE ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $search_name, $id_delete, $num_delete, $search_name, $id_delete);
    mysqli_stmt_execute($stmt);

if (!empty($row_sel)) {
    $nameValue = $row_sel['name'];
    $idValue = $row_sel['national_id'];
    $numValue = $row_sel['mobile_num'];
} 
    // Check if any rows were affected
    if (mysqli_stmt_affected_rows($stmt) > 0) {
        // Editing successful
        $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
        $result_move = mysqli_query($conn, $query_move);
        
        if ($result_move) {
            $row_move = mysqli_fetch_assoc($result_move);
            $currentValue_move = $row_move['value'];
    
            if ($currentValue_move === "1") {
            // Insert into System_Move table for the start value// Construct the action description
$action_description_start =  $num_delete . "و رقم الهاتف من: "  .$numValue . " الى " . $search_name . " إلى " . $nameValue . " بيانات العميل و تم تعديل الاسم من";

$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);


            }
        }
        echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "addclient.php";</script>';
    } else {
        // No rows were affected, so the data was not found
        echo '<script>alert("لم يتم العثور على بيانات للتعديل"); window.location.href = "addclient.php";</script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

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

    <title>العملاء</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="nav1">
            <h1>العملاء</h1>
            
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
                    margin-bottom:200px"  id="myform" type="submit" class="save" name="form_search" >بحث</button>
      </form>
  
      <form class="form1" method="post" >
        <h2>اضافه</h2>
        <label for="name2"></label>
        <input type="text" placeholder="ادخل الاسم" id="name2" name="name2" required>
        <label for="name2"></label>

        <input type="text" placeholder="أدخل الرقم القومي" id="email2" name="nat_id" required>
        <label for="name2"></label>

        <input type="text" placeholder="أدخل رقم الهاتف" id="email2" name="num" required>
        <label for="name2"></label>

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
      <input type="text" placeholder="الرقم القومي" id="email3" name="id_delete" required value="<?php echo isset($idValue) ? $idValue : ''; ?>">

      <label for="email"></label>
      <input type="text" placeholder="رقم الهاتف" id="email" name="num_delete" required value="<?php echo isset($numValue) ? $numValue : ''; ?>">

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