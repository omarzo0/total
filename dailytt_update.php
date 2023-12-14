<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');
// Function to update the values in the table and insert into System_Move table
function updateData($conn, $trumba_number, $trumba_type, $start, $end, $total, $date, $id) {
    // Calculate the "total" value
    $total = $start - $end;

    // Reformat the date to "year-month-day" format
    $formatted_date = date('Y-m-d', strtotime($date));
    if ($end < $start) {
        // Use prepared statement to avoid SQL injection
        $sql = "UPDATE benzene SET start = ?, end = ?, total = ? WHERE trumba_type = ? AND trumba_number = ? AND date = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiisis", $start, $end, $total, $trumba_type, $trumba_number, $formatted_date);

        if (mysqli_stmt_execute($stmt)) {
           
// Fetch the data again after insertion
$query = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result = mysqli_query($conn, $query);

if ($result) {
   $row = mysqli_fetch_assoc($result);
   $name = $row['username'];
   $user_type = $row['user_type'];
}
 // Prepare the SQL query to select the value column where sys_type is 'web'
        $query_move = "SELECT value FROM setting WHERE sys_type = 'web' AND option = 'sys_move'";
            
        $result_move = mysqli_query($conn, $query_move);
        
        if ($result_move) {
            $row_move = mysqli_fetch_assoc($result_move);
            $currentValue_move = $row_move['value'];
    
            if ($currentValue_move === "1") {

            // Insert into System_Move table for the start value
$action_description_start = "طرمبة رقم " . $trumba_number . " " . $trumba_type . " تم تعديل القيمة البداية من " . $_SESSION['start'] . " إلى " . $start;
$query_move_start = "INSERT INTO system_move (user_type,username, move, date) VALUES (?,?,?, CURDATE())";
$stmt_move_start = mysqli_prepare($conn, $query_move_start);
mysqli_stmt_bind_param($stmt_move_start, "sss",$user_type, $name, $action_description_start);
mysqli_stmt_execute($stmt_move_start);
mysqli_stmt_close($stmt_move_start);

// Insert into System_Move table for the end value
$action_description_end = "طرمبة رقم " . $trumba_number . " " . $trumba_type . " تم تعديل القيمة النهائية من " . $_SESSION['end'] . " إلى " . $end;
$query_move_end = "INSERT INTO system_move (user_type,username ,move, date) VALUES (?, ?,?, CURDATE())";
$stmt_move_end = mysqli_prepare($conn, $query_move_end);
mysqli_stmt_bind_param($stmt_move_end, "sss",$user_type,$name, $action_description_end);
mysqli_stmt_execute($stmt_move_end);
mysqli_stmt_close($stmt_move_end);
            }
        }
 // Editing successful
 echo '<script>alert("تم تعديل البيانات بنجاح"); window.location.href = "dailytt.php";</script>';
        } else {
            // Error occurred while updating
            echo '<script>alert("حدث خطأ أثناء تحديث البيانات"); window.location.href = "dailytt_update.php";</script>';
        }

        mysqli_stmt_close($stmt);
    } else {
        echo '<script>alert("لا يمكن ادخال قيمة البداية اكثر من قيمة النهاية"); window.location.href = "dailytt_update.php";</script>';
    }
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

     $total = $_SESSION['total'] ;
     $trumba_number= $_SESSION['trumba_number'] ;
     $trumba_type = $_SESSION['trumba_type'] ;
     $start  = $_SESSION['start'];
     $end  = $_SESSION['end'];
     $date = $_SESSION['date'];
     $id=  $_SESSION['id'];
// Reformat the date to "year-month-day" format
$data = date('m-d-Y', strtotime($date));

     // Fetch the data again after insertion
     $query = "SELECT trumba_number, trumba_type, start, end, total, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM benzene WHERE date = '$date' AND trumba_type = '$trumba_type' AND trumba_number = '$trumba_number' ";
     $result = mysqli_query($conn, $query);

     if ($result) {
        $row = mysqli_fetch_assoc($result);
        $total = $row['total'];
     $trumba_number= $row['trumba_number'] ;
     $trumba_type = $row['trumba_type'] ;
     $start  = $row['start'];
     $end  = $row['end'];
    
     }

     
     if (isset($_POST['edit'])) {
        // Get the updated values from the form
        $trumba_number = $_POST['trumba_number'];
        $trumba_type = $_POST['trumba_type'];
        $start = $_POST['start'];
        $end = $_POST['end'];
        $total = $_POST['name3'];
        $date = $_SESSION['date'];
        // Update the data using the updateData() function
        updateData($conn, $trumba_number, $trumba_type, $start, $end, $total, $date, $id);
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

    <title>يوميه الورديه </title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>يوميه الورديه</h1>
                <h5><?php echo $data . " : يتم التعديل الان فى بيانات يوم " ?></h5>
                  <br><br>
                 </div>
            <div style="width: 80%;
            margin-left:5px" class="table" id="datatable">
                
        <form class="form1" method="post">
        <div class="table-data">
        <input type="text" id="name3" name="name3" required value="<?php echo isset($total) ? $total : ''; ?>"readonly>
    <label for="name3">الاجمالي</label></div>
        <br>

        <div class="table-data">
    <input type="text" id="national_id" name="end" required value="<?php echo isset($end) ? $end : ''; ?>">
    <label for="name3">النهايه</label>
        </div>
    <br>

    <div class="table-data">
    <input type="text"  id="mobile_num" name="start" required value="<?php echo isset($start) ? $start : ''; ?>">
    <label for="name3">البدايه</label>
    </div>
    <br>

    <div class="table-data">
    <input type="text" id="job" name="trumba_type" required value="<?php echo isset($trumba_type) ? $trumba_type : ''; ?>"readonly>
    <label for="name3">النوع الطرمبه</label>
</div>
    <br>

    <div class="table-data">
    <input type="text" id="salary" name="trumba_number" required value="<?php echo isset($trumba_number) ? $trumba_number : ''; ?>"readonly>
    <label for="name3">رقم الطرمبه</label>
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