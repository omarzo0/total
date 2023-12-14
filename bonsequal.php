<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once('loggedin.php');
function  getTotalAmount($conn , $search_ben , $formatted_date ) {
    $conn = require __DIR__ . "/connect.php";
    // Query to get the sum of money for the given conditions
    $sql = "SELECT SUM(total) AS totalAmount, SUM(number) AS totalnumber FROM matching_bons WHERE ( DATE(date) = CURDATE() OR DATE(date) = '$formatted_date' ) AND (side = '$search_ben' OR benz_type = '$search_ben')";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalAmount = $row["totalAmount"];
    } else {
        $totalAmount = 0;
    }

    $conn->close();

    return $totalAmount;
}   

function getTotalnumber($conn , $search_ben, $formatted_date) {

    $conn = require __DIR__ . "/connect.php";
    // Query to get the sum of money for the given conditions
    $sql = "SELECT SUM(number) AS totalnumber FROM matching_bons WHERE ( DATE(date) = CURDATE() OR DATE(date) = '$formatted_date' ) AND (side = '$search_ben' OR benz_type = '$search_ben')";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $totalnumber = $row["totalnumber"];
    } else {
        $totalnumber = 0;
    }

    $conn->close();

    return $totalnumber;
}
if (is_logged_in() || $_SESSION['id'] == 1) {
    $id = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}
        // Query the matching_bons table with the formatted date
        $query = "SELECT category, benz_type, side, total, number
                  FROM matching_bons
                  WHERE benz_type = 'سولار' AND DATE(date) = CURDATE()";
   $result_booking = mysqli_query($conn, $query);

            // Check if the query was executed successfully
            if (!$result_booking) {
                echo "No matching records found in Term Clients.<br><br>";
            } else {
                $num_clients = mysqli_num_rows($result_booking);
            }

            $search_ben = 'سولار';
            $formatted_date = date('Y-m-d');
            getTotalAmount($conn , $search_ben, $formatted_date) ;
            getTotalnumber($conn , $search_ben, $formatted_date) ;

    if (isset($_POST['search_ben'])) {
        $search_ben = isset($_POST['search_ben']) ? $_POST['search_ben'] : '';
        $search_date = isset($_POST['date']) ? $_POST['date'] : '';

    
        // Convert the search date to the desired format "day-month-year"
        $formatted_date = date('Y-m-d', strtotime($search_date));
        // Query the matching_bons table with the formatted date
        $query = "SELECT category, benz_type, side, total, number
                  FROM matching_bons
                  WHERE (benz_type = ? OR side = ?) AND (DATE(date) = CURDATE() OR DATE (date) = '$formatted_date')";

        // Prepare the statement
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $query)) {
            // Bind the parameters and execute the query
            mysqli_stmt_bind_param($stmt, "ss", $search_ben, $search_ben);
            mysqli_stmt_execute($stmt);

            $result_booking = mysqli_stmt_get_result($stmt);

            // Check if the query was executed successfully
            if (!$result_booking) {
                echo "No matching records found in Term Clients.<br><br>";
            } else {
                $num_clients = mysqli_num_rows($result_booking);
            }
        } else {
            echo "Error in preparing the query: " . mysqli_error($conn);
        }
        getTotalAmount($conn , $search_ben, $formatted_date ) ;
        getTotalnumber($conn , $search_ben, $formatted_date) ;
        mysqli_stmt_close($stmt);
    }
}

mysqli_close($conn);
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

    <link rel="shortcut icon" type="x-icon" href="">

    <title>البونات</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1 style="margin-left: 850px;" >مطابقه البونات</h1>
                <form method="post">
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>

<br><br>
                <a href="bonsequal.php"><button style="margin-left: 90px;" class="save">مطابقه البونات</button></a>
                <a href="bons.php"><button class="save">البونات</button></a>

                <br><br>
                <div >
                    
                <button style="margin-left: 90px; background:#003a5d" class="save" name="search_ben" type="submit" value="سولار" > سولار</button>
                <button style="background: #003a5d;" class="save" name="search_ben" type="submit" value="بنزين 80" >بنزين 80</button>
                <button  style="background: #003a5d;" class="save" name="search_ben" type="submit" value="بنزين 92" >بنزين 92</button>
                <button  style="background: #003a5d;" class="save" name="search_ben" type="submit" value="بنزين 95" >بنزين 95</button>
                <button  style="background: #003a5d;" class="save" name="search_ben" type="submit" value="شرطة" >شرطة</button>
                <button  style="background: #003a5d; margin-bottom: 22px" class="save" name="search_ben" type="submit" value="جمعية" >جمعية</button>
                    </form>

                </div>
            </div>
            </div>                
                <div  style="width: 75%;
                margin-left:10px" class="table" id="datatable">
                 <div class="table-header">
                <div class="header__item"> <a id="wins" class="filter__link filter__link--number" href="#"> <?php echo isset($search_ben) ? $search_ben : 'سولار' ?> </a></div>
                 </div>
                    <div class="table-header">
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">الاجمالي</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">الفئه</a></div>
                        <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">العدد</a></div>
                    </div>
                    <div class="table-content">
                    <form method="post">
    <?php
    // Display data in the table
    if ($num_clients === 0) {
        echo '<div class="table-row"><div class="table-data" colspan="3">لا توجد بيانات</div></div>';
    } else {
        while ($row = mysqli_fetch_assoc($result_booking)) {
            echo '<div class="table-row">';
            echo '<div class="table-data">' . $row['total'] . '</div>';
            echo '<div class="table-data">' . $row['category'] . '</div>';
            echo '<div class="table-data">' . $row['number'] . '</div>';
            echo '</div>';
        }
    }
    ?>
</form>

                    </div>	
                </div>
            </div>
       
            <label style="margin-left: 650px;" for="total"> اجمالى المبلغ</label>
<input style="margin-left:830px; margin-top:-400px" type="number" id="meeting-time" value="<?php echo getTotalAmount($conn , $search_ben,  $formatted_date) ; ?>" readonly>
<br>
<label style="margin-left: 650px;" for="total"> اجمالى العدد</label>
</div>
<input style="margin-left:830px; margin-top:-400px" type="number" id="meeting-time" value="<?php echo getTotalnumber($conn , $search_ben, $formatted_date); ?>" readonly>

                  
        </div>
    </div>
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
        document.getElementById('meeting-time').value = today; // Set the input field value to today's date
    }
</script>
</body>

</html>