<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');

// Function to update the values in the table and insert into System_Move table
function updateData($conn, $na2l, $fr2s3r, $id_na2l, $Statement, $formatted_date, $Type) {
    // Use prepared statement to avoid SQL injection
    $sql = "UPDATE na2l_fr2_ked SET na2l = ?, fr2s3r = ?, Statement = ?, Type = ? WHERE Statement = ? AND id = ? AND date = ?";
    $stmt = mysqli_prepare($conn, $sql);

    // Bind parameters in the correct order and types
    mysqli_stmt_bind_param($stmt, "iisssss", $na2l, $fr2s3r, $Statement, $Type, $Statement, $id_na2l, $formatted_date);

    if (mysqli_stmt_execute($stmt)) {
        // Editing successful
        unset($Statement);
        unset($Type);
        unset($formatted_date);
        echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "dailyqed.php";</script>';
    } else {
        // Error occurred while updating
        echo '<script>alert("حدث خطأ أثناء تحديث البيانات"); window.location.href = "dailyqed_update.php";</script>';
    }

    mysqli_stmt_close($stmt);
}

if (is_logged_in() || $_SESSION['id'] == 1) {
    // Fetch the data again after insertion
    $id = $_SESSION['id'];

    // Fetch the data again after insertion
    $query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
    $result_type = mysqli_query($conn, $query_type);

    if ($result_type) {
        $row = mysqli_fetch_assoc($result_type);
        $name = $row['username'];
        $user_type = $row['user_type'];
    }

    $na2l = $_SESSION['na2l'];
    $fr2s3r = $_SESSION['fr2s3r'];
    $Statement = $_SESSION['Statement'];
    $Type = $_SESSION['Type'];
    $id_na2l = $_SESSION['id_na2l'];
    $formatted_date = $_SESSION['formatted_date'];
    $formdate = date('m-d-Y', strtotime($formatted_date));
    // Fetch the data again after insertion
    $query = "SELECT na2l, fr2s3r, id , Statement, Type FROM na2l_fr2_ked WHERE Statement = ? AND id = ? AND Type = ? AND DATE(date) = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ssss', $Statement, $id_na2l, $Type, $formatted_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $na2l = $row['na2l'];
        $fr2s3r = $row['fr2s3r'];
        $Statement = $row['Statement'];
        $Type = $row['Type'];
        $id_na2l = $row['id'];
        if ($Type == 'مقبوضات') {
            $stat = 'من مذكورين';
        } else {
            $stat = 'الى مذكورين';
        }
    }

    if (isset($_POST['edit'])) {
        // Get the updated values from the form
        $na2l = $_POST['na2l'];
        $fr2s3r = $_POST['fr2s3r'];
        // Update the data using the updateData() function
        updateData($conn, $na2l, $fr2s3r, $id_na2l, $Statement, $formatted_date, $Type);
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

    <title>من مذكورين</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1><?php echo $stat?></h1>
                <a href="dailyqed.php"><button class="save">قيد يومي</button></a>
                <h5><?php echo $formdate . " : يتم التعديل الان فى بيانات يوم " ?></h5>
                  <br><br>
                  <h5><?php echo"  يتم التعديل فى البيان : ". $Statement  ?></h5>
                 </div>
            <div style="width: 80%;
            margin-left:5px" class="table" id="datatable">
                
        <form class="form1" method="post">
        <div class="table-data">
        <input type="text" id="name3" name="na2l" required value="<?php echo isset($na2l) ? $na2l : ''; ?>">
    <label for="name3">نقل</label></div>
        <br>

        <div class="table-data">
    <input type="text" id="national_id" name="fr2s3r" required value="<?php echo isset($fr2s3r) ? $fr2s3r : ''; ?>">
    <label for="name3">فرق السعر<label>
        </div>
    <br>

        <button style="margin-bottom: -50px;
                    margin-top: 30px;
                    margin-left: 37px;" name="edit" id="myform1" type="submit" class="save" >حفظ</button>
    </form>
                
            </div>
        </div>
    </div>
   <!-- Vertical navbar section  -->
   <?php if( $user_type == "عامل" )
    {
      echo '  <section id="navbar" class="nav-bar"> ';
      echo '  <div class="menu-toggle">';
      echo '  <div class="hamburger">';
      echo '     <span></span>';
          echo '  </div>';
      echo '  </div>';
        echo ' <div class="sidebar close">';
        echo '  <div class="logo-details">';
        echo '   <span class="logo_name">';
        echo '   <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">';
        echo '   </span>';
        echo '   </div>';
        echo '<ul class="nav-links">';
        echo ' <li>';
        echo '<a href="dailytt.php">';
        echo " <i class='bx bxs-book-add' ></i>";
                    echo '  <span class="link_name">يوميه الورديه</span>';
                    echo '  </a>';
                echo ' </li>';
            echo ' <li>';
            echo '   <a href="oil.php">';
            echo " <i class='bx bx-folder-plus'></i>";
                    echo '   <span class="link_name">الزيوت</span>';
                    echo '  </a>';
                echo ' </li>';
            echo ' <li>';
            echo ' <a href="expensis.php">';
                  echo "  <i class='bx bx-folder-plus'></i>";
                    echo ' <span class="link_name">المصاريف</span>';
                    echo ' </a>';
                    echo '  </li>';
            echo '  <li>';
            echo '  <a href="bons.php">';
                echo " <i class='bx bxs-file-plus'></i>";
                    echo '<span class="link_name">البونات</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="logout.php">';
                echo "   <i class='bx bx-cog'></i>";
                echo '   <span class="link_name">تسجيل الخروح</span>';
                echo '  </a>';
                echo '</li>';
            echo '</div>';
            echo '</section>';

        
    }elseif( $user_type == "محاسب")
    {
        echo '  <section id="navbar" class="nav-bar"> ';
        echo '  <div class="menu-toggle">';
        echo '  <div class="hamburger">';
        echo '     <span></span>';
            echo '  </div>';
        echo '  </div>';
          echo ' <div class="sidebar close">';
          echo '  <div class="logo-details">';
          echo '   <span class="logo_name">';
          echo '   <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">';
          echo '   </span>';
          echo '   </div>';
          echo '<ul class="nav-links">';

            echo ' <li>';
            echo '<a href="dailytt.php">';
            echo " <i class='bx bxs-book-add' ></i>";
                        echo '  <span class="link_name">يوميه الورديه</span>';
                        echo '  </a>';
                    echo ' </li>';
                    echo ' <li>';
                    echo '   <a href="oil.php">';
                    echo " <i class='bx bx-folder-plus'></i>";
                            echo '   <span class="link_name">الزيوت</span>';
                            echo '  </a>';
                        echo ' </li>';
                    echo ' <li>';
                    echo ' <a href="expensis.php">';
                          echo "  <i class='bx bx-folder-plus'></i>";
                            echo ' <span class="link_name">المصاريف</span>';
                            echo ' </a>';
                            echo '  </li>';
                    echo '  <li>';
                    echo '  <a href="bons.php">';
                        echo " <i class='bx bxs-file-plus'></i>";
                            echo '<span class="link_name">البونات</span>';
                            echo ' </a>';
                        echo '</li>';


                        echo '  <li>';
                        echo ' <a href="client.php">';
                echo "   <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">عملاء اجله</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="daitre.php">';
                echo "  <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">يوميه الخزينه </span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="tamwenpass.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '    <span class="link_name">دفتر التموين</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="tremove.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '   <span class="link_name"> حركه الخزينه</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo ' <a href="dailyqed.php">';
                echo "<i class='bx bx-cog'></i>";
                    echo '  <span class="link_name"> قيد يومي</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="logout.php">';
                echo "   <i class='bx bx-cog'></i>";
                echo '   <span class="link_name">تسجيل الخروح</span>';
                echo '  </a>';
                echo '</li>';
                echo '  </ul>';
                echo '  </div>';
                echo ' </section>'; 
    }
    else {
        echo '  <section id="navbar" class="nav-bar"> ';
        echo '  <div class="menu-toggle">';
        echo '  <div class="hamburger">';
        echo '     <span></span>';
            echo '  </div>';
        echo '  </div>';
          echo ' <div class="sidebar close">';
          echo '  <div class="logo-details">';
          echo '   <span class="logo_name">';
          echo '   <img src="images/WhatsApp Image 2023-07-09 at 04.37.09.jpg" width="150px" alt="">';
          echo '   </span>';
          echo '   </div>';
          echo '<ul class="nav-links">';
          echo ' <li>';
          echo '   <a href="adminhome.php" id="btn" >';
                   echo " <i class='bx bxs-home'></i>";
                    echo '  <span class="link_name">نظره عامه</span>';
                    echo '</a>';
                    echo '</li>';
            echo ' <li>';
            echo '<a href="dailytt.php">';
            echo " <i class='bx bxs-book-add' ></i>";
                        echo '  <span class="link_name">يوميه الورديه</span>';
                        echo '  </a>';
                    echo ' </li>';
                    echo ' <li>';
                    echo '   <a href="oil.php">';
                    echo " <i class='bx bx-folder-plus'></i>";
                            echo '   <span class="link_name">الزيوت</span>';
                            echo '  </a>';
                        echo ' </li>';
                    echo ' <li>';
                    echo ' <a href="expensis.php">';
                          echo "  <i class='bx bx-folder-plus'></i>";
                            echo ' <span class="link_name">المصاريف</span>';
                            echo ' </a>';
                            echo '  </li>';
                    echo '  <li>';
                    echo '  <a href="bons.php">';
                        echo " <i class='bx bxs-file-plus'></i>";
                            echo '<span class="link_name">البونات</span>';
                            echo ' </a>';
                        echo '</li>';


                        echo '  <li>';
                        echo ' <a href="client.php">';
                echo "   <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">عملاء اجله</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="daitre.php">';
                echo "  <i class='bx bx-comment-error'></i>";
                    echo '  <span class="link_name">يوميه الخزينه </span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="tamwenpass.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '    <span class="link_name">دفتر التموين</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '   <a href="tremove.php">';
                echo "   <i class='bx bx-cog'></i>";
                    echo '   <span class="link_name"> حركه الخزينه</span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo ' <a href="dailyqed.php">';
                echo "<i class='bx bx-cog'></i>";
                    echo '  <span class="link_name"> قيد يومي</span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="employee.php">';
                echo "  <i class='bx bx-cog'></i>";
                    echo '  <span class="link_name">الموظفين </span>';
                    echo '  </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="systemmove.php">';
                  echo "  <i class='bx bx-cog'></i>";
                    echo '   <span class="link_name">حركه النظام </span>';
                    echo ' </a>';
                echo '</li>';
                echo '  <li>';
                echo '  <a href="logout.php">';
                echo "   <i class='bx bx-cog'></i>";
                echo '   <span class="link_name">تسجيل الخروح</span>';
                echo '  </a>';
                echo '</li>';
                echo '  </ul>';
                echo '  </div>';
                echo ' </section>'; }?>
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