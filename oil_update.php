<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');

function updateOilData($conn, $oil_name, $id, $date, $price, $first_term_balance , $end_term_balance) {

        // Calculate saled and total based on the formula
        $saled = $first_term_balance - $end_term_balance;
        $total = $saled * $price;
        if ($first_term_balance < $end_term_balance)
        {
            echo '<script>alert("الرجاء ادخال بيانات صحيحة"); window.location.href = "oil_update.php";</script>';
            exit;
        }else {
        // Update the data
        $updateQuery = "UPDATE oil_ward SET saled = ?, total = ? , first_term_balance = ? , end_term_balance = ? WHERE oil_name = ? AND id = ? AND date = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, 'isiisis', $saled, $total, $first_term_balance , $end_term_balance, $oil_name, $id, $date);
        mysqli_stmt_execute($updateStmt);
        if (mysqli_stmt_execute($updateStmt)) {
            // Editing successful
            unset($first_term_balance);
            unset($end_term_balance);
            unset($oil_name);
            unset($id);
            unset($saled);
            unset($total);
            echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "oil_update.php";</script>';
        } else {
            // Error occurred while updating
            echo '<script>alert("حدث خطأ أثناء تحديث البيانات"); window.location.href = "oil_update.php";</script>';
        }}
    
}

if (is_logged_in() || $_SESSION['id'] == 1) {
    $oil_name= $_SESSION['oil_name'];
    $first_term_balance = $_SESSION['first_term_balance'];
    $end_term_balance = $_SESSION['end_term_balance'];
    $saled = $_SESSION['saled'];
    $price = $_SESSION['price'];
    $total = $_SESSION['total'];
   $date =  $_SESSION['date'];
   $id = $_SESSION['id_oil'];  
   $query = "SELECT id, oil_name, first_term_balance, end_term_balance, saled, price, total, photo,  DATE_FORMAT(date, '%m-%d-%Y') AS formatted_date FROM oil_ward  WHERE oil_name = '$oil_name' AND id = '$id'";
   $result = mysqli_query($conn, $query);
   if ($row = mysqli_fetch_assoc($result)) {
       $oil_name = $row['oil_name'];
       $first_term_balance = $row['first_term_balance'];
       $end_term_balance= $row['end_term_balance'];
       $saled = $row['saled'];
       $price = $row['price'];
       $total = $row ['total'];
       $date_oil = $row['formatted_date'];
       $photo = $row['photo'];
   }
   if (isset($_POST['edit'])) {
    // Get the updated values from the form
    $end_term_balance = $_POST['end_term_balance'];
    $first_term_balance = $_POST['first_term_balance'];
    // Update the data using the updateData() function
    updateOilData($conn, $oil_name, $id, $date, $price, $first_term_balance , $end_term_balance);

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
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="shortcut icon" type="x-icon" href="">

    <title> الزيوت </title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>الزيوت </h1>
                <h5><?php echo $date_oil . " : يتم التعديل الان فى بيانات يوم " ?></h5>
                  <br><br>
                 </div>
            <div style="width: 80%;
            margin-left:5px" class="table" id="datatable">
                
        <form class="form1" method="post">

        <div class="table-data">
    <input type="text" id="salary" name="trumba_number" required value="<?php echo isset($oil_name) ? $oil_name : ''; ?>"readonly>
    <label for="name3">نوع الزيت</label>
</div>
<br>
<div class="table-data">
    <input type="text" id="job" name="first_term_balance" required value="<?php echo isset($first_term_balance) ? $first_term_balance : ''; ?>">
    <label for="name3">اول المده</label>
</div>
<br>
    <div class="table-data">
    <input type="text"  id="mobile_num" name="end_term_balance" required value="<?php echo isset($end_term_balance) ? $end_term_balance : ''; ?>">
    <label for="name3">اخر المده</label>
    </div>
    <br>
    <div class="table-data">
    <input type="text" id="national_id" name="saled" required value="<?php echo isset($saled) ? $saled : ''; ?>"readonly>
    <label for="name3">مباع</label>
        </div>
    <br>

    <div class="table-data">
        <input type="text" id="name3" name="price" required value="<?php echo isset($price) ? $price : ''; ?>">
    <label for="name3">سعر الواحدة</label>
</div>
        <br>

        <div class="table-data">
        <input type="text" id="name3" name="total" required value="<?php echo isset($total) ? $total : ''; ?>"readonly>
    <label for="name3">الاجمالي</label>
</div> <div class="table-data">
        <?php if (isset($photo) && !empty($photo)) : ?>
            <label for="name3">الصورة</label>
            <div class="image-box">
                <img src="<?php echo $photo; ?>" alt="صورة الزيت">
            </div>
        <?php endif; ?>
<br>
        </div>

        <button style="margin-bottom: -50px;
                    margin-top: 30px;
                    margin-left: 37px;" name="edit" id="myform1" type="submit" class="save" >حفظ</button>
    </form>
                
            </div>
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
<!-- Javascript section -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"
integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN"
crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let arrow = document.querySelectorAll(".arrow");
for (var i = 0; i < arrow.length; i++) {
    arrow[i].addEventListener("click", (e) => {
        let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
        arrowParent.classList.toggle("showMenu");
    });
}
const menu_toggle = document.querySelector('.menu-toggle');
const sidebar = document.querySelector('.sidebar');

menu_toggle.addEventListener('click', () => {
    menu_toggle.classList.toggle('is-active');
    sidebar.classList.toggle('is-active');
});

function showConfirmation() {
        Swal.fire({
            title: 'Sign out?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form
                document.querySelector('form').submit();
            }
        })
    }

</script>

</body>

</html>