<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');
// Function to check if data exists for today's date and a specific trumba_type
function checkDataForTodayByType($conn, $trumba_type) {
    $query = "SELECT COUNT(*) AS count FROM benzene WHERE DATE(date) = CURDATE() AND trumba_type = '$trumba_type'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $count = $row['count'];
    return $count;
}

// Function to search for data by date in the format "year-month-day"
function searchDataByDate($conn, $search_date) {
    $search_date_formatted = date('Y-m-d', strtotime($search_date));
    $query = "SELECT id, trumba_number, trumba_type, start, end, total, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM benzene WHERE DATE(date) = '$search_date_formatted'";
    $result = mysqli_query($conn, $query);
    return $result;
}

if (is_logged_in() || $_SESSION['id'] == 1) {
    // Check if the search form is submitted
        // Perform the database query for today's date if search form is not submitted
        $id = $_SESSION['id'];
        // Fetch the data again after insertion
    $query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
    $result_type = mysqli_query($conn, $query_type);
    
    if ($result_type) {
    $row = mysqli_fetch_assoc($result_type);
    $name = $row['username'];
    $user_type = $row['user_type'];
    }
        // Check if data exists for today and "سولار"
        $data_exists_solar = checkDataForTodayByType($conn, "سولار");
        $data_exists_ben80 = checkDataForTodayByType($conn, "بنزين 80");
        $data_exists_ben92 = checkDataForTodayByType($conn, "بنزين 92");
        $data_exists_ben95 = checkDataForTodayByType($conn, "بنزين 95");

        if ($data_exists_solar && $data_exists_ben80 && $data_exists_ben92 && $data_exists_ben95) {
            // Data exists for all types and today, perform the database query (same as before)
            $query = "SELECT id, trumba_number, trumba_type, start, end, total, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM benzene WHERE DATE(date) = CURDATE()";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Count the number of output rows
                $num_clients = mysqli_num_rows($result);
            }
        } else {
            // Data does not exist for today, create new rows for each trumba_type

            // For "سولار" with trumba_number from 1 to 6
            $trumba_type_solar = "سولار";
            $start_solar = 0;
            $end_solar = 0;
            $total_solar = 0;

            for ($i = 1; $i <= 6; $i++) {
                $trumba_number_solar = $i;

                $sql_solar = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_solar = mysqli_prepare($conn, $sql_solar);
                mysqli_stmt_bind_param($stmt_solar, "isiii", $trumba_number_solar, $trumba_type_solar, $start_solar, $end_solar, $total_solar);

                if (mysqli_stmt_execute($stmt_solar)) {
                    // Row insertion successful
                } else {
                    // Error occurred while inserting row
                    echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                    exit;
                }

                mysqli_stmt_close($stmt_solar);
            }

            // For "بنزين 80" with one row and trumba_number 1
            $trumba_type_ben80 = "بنزين 80";
            $start_ben80 = 0;
            $end_ben80 = 0;
            $total_ben80 = 0;

            $trumba_number_ben80 = 1;

            $sql_ben80 = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
            $stmt_ben80 = mysqli_prepare($conn, $sql_ben80);
            mysqli_stmt_bind_param($stmt_ben80, "isiii", $trumba_number_ben80, $trumba_type_ben80, $start_ben80, $end_ben80, $total_ben80);

            if (mysqli_stmt_execute($stmt_ben80)) {
                // Row insertion successful
            } else {
                // Error occurred while inserting row
                echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                exit;
            }

            mysqli_stmt_close($stmt_ben80);

            // For "بنزين 92" with two rows and trumba_number from 1 to 2
            $trumba_type_ben92 = "بنزين 92";
            $start_ben92 = 0;
            $end_ben92 = 0;
            $total_ben92 = 0;

            for ($i = 1; $i <= 2; $i++) {
                $trumba_number_ben92 = $i;

                $sql_ben92 = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_ben92 = mysqli_prepare($conn, $sql_ben92);
                mysqli_stmt_bind_param($stmt_ben92, "isiii", $trumba_number_ben92, $trumba_type_ben92, $start_ben92, $end_ben92, $total_ben92);

                if (mysqli_stmt_execute($stmt_ben92)) {
                    // Row insertion successful
                } else {
                    // Error occurred while inserting row
                    echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                    exit;
                }

                mysqli_stmt_close($stmt_ben92);
            }

            // For "بنزين 95" with two rows and trumba_number from 1 to 2
            $trumba_type_ben95 = "بنزين 95";
            $start_ben95 = 0;
            $end_ben95 = 0;
            $total_ben95 = 0;

            for ($i = 1; $i <= 2; $i++) {
                $trumba_number_ben95 = $i;

                $sql_ben95 = "INSERT INTO benzene (trumba_number, trumba_type, start, end, total, date) VALUES (?, ?, ?, ?, ?, CURDATE())";
                $stmt_ben95 = mysqli_prepare($conn, $sql_ben95);
                mysqli_stmt_bind_param($stmt_ben95, "isiii", $trumba_number_ben95, $trumba_type_ben95, $start_ben95, $end_ben95, $total_ben95);

                if (mysqli_stmt_execute($stmt_ben95)) {
                    // Row insertion successful
                } else {
                    // Error occurred while inserting row
                    echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailytt.php";</script>';
                    exit;
                }

                mysqli_stmt_close($stmt_ben95);
            }

            // Fetch the data again after insertion
            $query = "SELECT id ,trumba_number, trumba_type, start, end, total, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM benzene WHERE DATE(date) = CURDATE()";
            $result = mysqli_query($conn, $query);

            if ($result) {
                // Count the number of output rows
                $num_clients = mysqli_num_rows($result);
            }
        }
    
        if (isset($_POST['search'])) {
            $search_date = $_POST['date'];
    
            // Perform the database query for the searched date using the search function
            $result = searchDataByDate($conn, $search_date);
    
            if ($result) {
                // Count the number of output rows
                $num_clients = mysqli_num_rows($result);
            }
        }


    if (isset($_POST['delete'])) {
        // Get the button's value (trumba_type and trumba_number)
        $buttonValue = $_POST['delete'];
        // Split the value into an array
        $values = explode('|', $buttonValue);
        // Get the individual trumba_type and trumba_number
        $trumba_type = mysqli_real_escape_string($conn, $values[0]);
        $trumba_number = mysqli_real_escape_string($conn, $values[1]);
        $id = mysqli_real_escape_string($conn, $values[2]);
        $date = mysqli_real_escape_string($conn, $values[3]);
    
        // Use the trumba_type and trumba_number in the SQL query
        $query = "SELECT trumba_number, trumba_type, start, end, total, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM benzene WHERE trumba_type = '$trumba_type' AND trumba_number = '$trumba_number' AND id = '$id' AND date = '$date'";
        $result = mysqli_query($conn, $query);
        if ($row = mysqli_fetch_assoc($result)) {
            $_SESSION['trumba_type'] = $row['trumba_type'];
            $_SESSION['trumba_number'] = $row['trumba_number'];
            $_SESSION['start'] = $row['start'];
            $_SESSION['end'] = $row['end'];
            $_SESSION['total'] = $row['total'];
            $_SESSION['date'] = $row['formatted_date'];
        }
        header('Location: dailytt_update.php');
        exit();
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
               
                <div class="cal-wrapper">
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

<button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
</form>
                <br><br>
                  </div>
                  
                 </div>
            <div style="width: 80%;
            margin-left:5px" class="table" id="datatable">
                <div class="table-header">
                    <div  style="margin-left: -25px;" class="header__item"><a id="name" class="filter__link" href="#">التحكم</a></div>
                    <div  style="margin-left: 35px;" class="header__item"><a id="name" class="filter__link" href="#">الاجمالي</a></div>
                    <div class="header__item" style="margin-left: 45px;"><a id="wins" class="filter__link filter__link--number" href="#">النهايه</a></div>
                    <div class="header__item" style="margin-left: 45px;"><a id="draws" class="filter__link filter__link--number" href="#">البدايه</a></div>
                    <div class="header__item" style="margin-left: 70px;" ><a id="losses" class="filter__link filter__link--number" href="#">النوع</a></div>
                    <div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">رقم الطرمبه</a></div>
                </div>
                <div class="table-content">
                <form method="post">
    <?php
    // Display data in the table
    if ($num_clients === 0) {
        echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="table-row">';
            // Edit button with hidden input fields for passing data
            $buttonValue = $row['trumba_type'] . '|' . $row['trumba_number'] . '|' . $row['id'] . '|' . $row['formatted_date'];
            echo '<div class="table-data"><button style="margin-bottom: 30px; margin-top: -50px; margin-left: 0px;" id="myform" type="submit" class="save" name="delete" value="' . $buttonValue . '">تعديل</button></div>';
            echo '<div class="table-data">' . $row['total'] . '</div>';
            echo '<div class="table-data">' . $row['end'] . '</div>';
            echo '<div class="table-data">' . $row['start'] . '</div>';
            echo '<div class="table-data">' . $row['trumba_type'] . '</div>';
            echo '<div class="table-data">' . $row['trumba_number'] . '</div>'; 
            echo '</div>';          
        }
    }
    ?>
</form>
                </div>	
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
    function goToToday() {
        const today = new Date().toISOString().split('T')[0]; // Get today's date in the format 'YYYY-MM-DD'
        document.getElementById('meeting_time').value = today; // Set the input field value to today's date
    }
</script>
</body>

</html>