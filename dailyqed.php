<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');

if (is_logged_in() || $_SESSION['id'] == 1)
{
    $id = $_SESSION['id'];
    // Fetch the data again after insertion
$query_type = "SELECT username , user_type FROM user_system WHERE id = '$id'";
$result_type = mysqli_query($conn, $query_type);

if ($result_type) {
$row = mysqli_fetch_assoc($result_type);
$name = $row['username'];
$user_type = $row['user_type'];
}
    $currentDate = date("Y-m-d");
$query = "SELECT * FROM ked WHERE DATE(date) = '$currentDate'";
$results = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($results);

if (!$row) {
    $currentDay = date('d');
    $currentMonth = date('m');

    if ($currentMonth === '01' && $currentDay === '01') {
        $number = 2;
    } else {
        $currentDate = date("Y-m-d");
        $previousDate = date("Y-m-d", strtotime("-1 day", strtotime($currentDate)));

        $sql_2 = "SELECT number FROM ked WHERE DATE(date) = '$previousDate'";
        $results_2 = mysqli_query($conn, $sql_2);
        $row_2 = mysqli_fetch_assoc($results_2);

        if (!$row_2) {
            $number = 2;
        } else {
            $number_yes = $row_2['number'];
            $number = $number_yes + 1;
        }
    }

    // Insert the record with the calculated number
    $sql_insert = "INSERT INTO ked (number, date) VALUES (?, CURDATE())";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "i", $number);

    if (mysqli_stmt_execute($stmt_insert)) {
        // Row insertion successful
    } else {
        // Error occurred while inserting row
        echo '<script>alert("حدث خطأ أثناء إدخال البيانات"); window.location.href = "dailyqed.php";</script>';
        exit;
    }
}


mysqli_set_charset($conn, 'utf8'); // Set character set to UTF-8
$sql = "SELECT Statement, Type, na2l, fr2s3r, date FROM na2l_fr2_ked WHERE date = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rd = mysqli_fetch_all($result, MYSQLI_ASSOC);

if (empty($rd)) {
    $sql = "SELECT name FROM statement";
    $result = mysqli_query($conn, $sql);
    $statement_names = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($statement_names as $name) {
        $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مقبوضات', ?, 0, 0, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $name['name'], $currentDate);
        mysqli_stmt_execute($stmt);

        $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مدفوعات', ?, 0, 0, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $name['name'], $currentDate);
        mysqli_stmt_execute($stmt);
    }
} else {
    $existing_statement_names = array_column($rd, 'Statement');
    $sql = "SELECT name FROM statement WHERE name NOT IN ('" . implode("','", $existing_statement_names) . "') AND NOT EXISTS (SELECT 1 FROM na2l_fr2_ked WHERE na2l_fr2_ked.Statement = statement.name AND na2l_fr2_ked.date = ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $currentDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $new_statement_names = mysqli_fetch_all($result, MYSQLI_ASSOC);

    foreach ($new_statement_names as $name) {
        $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مقبوضات', ?, 0, 0, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $name['name'], $currentDate);
        mysqli_stmt_execute($stmt);

        $sql = "INSERT INTO na2l_fr2_ked (Type, Statement, na2l, fr2s3r, date) VALUES ('مدفوعات', ?, 0, 0, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $name['name'], $currentDate);
        mysqli_stmt_execute($stmt);
    }
}


if (isset($_POST['delete'])) {
    // Get the button's value (statement, Type, and date)
    $buttonValue = $_POST['delete'];
    // Split the value into an array
    $values = explode('|', $buttonValue);
    // Get the individual statement, Type, and date
    $Statement = urldecode($values[0]);
    $Type = urldecode($values[1]);
    $date_no = urldecode($values[2]);
    // Use the statement, Type, and date in the SQL query
    $query_na2l = "SELECT na2l, fr2s3r, id, Statement, Type, DATE_FORMAT(date, '%Y-%m-%d') AS formatted_date FROM na2l_fr2_ked WHERE Statement = ? AND date = ? AND Type = ?";
    $stmt_na2l = mysqli_prepare($conn, $query_na2l);
    mysqli_stmt_bind_param($stmt_na2l, 'sss', $Statement, $date_no, $Type);
    mysqli_stmt_execute($stmt_na2l);
    $result_na2l = mysqli_stmt_get_result($stmt_na2l);

    if ($row_na2l = mysqli_fetch_assoc($result_na2l)) {
        $_SESSION['na2l'] = $row_na2l['na2l'];
        $_SESSION['fr2s3r'] = $row_na2l['fr2s3r'];
        $_SESSION['Statement'] = $row_na2l['Statement'];
        $_SESSION['Type'] = $row_na2l['Type'];
        $_SESSION['formatted_date'] = $row_na2l['formatted_date'];
        $_SESSION['id_na2l'] = $row_na2l['id'];
    }

    header('Location: dailyqed_update.php');
    exit();
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
    <link rel="stylesheet" href="css/all.min.css">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/responsiveqed.css">
 
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>قيد يومي</title>
</head>



<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>قيد يومي</h1>
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting-time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
<br><br>
                </form>
            </div>
                    <div  style="width: 80%;
                    margin-left:-20px" class="table" id="datatable">
                        <div class="table-header">
                            <div class="header__item"><a id="name" class="filter__link" href="#">فرق السعر</a></div>
                            <div class="header__item"><a id="name" class="filter__link" href="#">نقل</a></div>
                            <div class="header__item"><a id="name" class="filter__link" href="#">البيان</a></div>
                            <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">دائن</a></div>
                            <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">مدين</a></div>
                        </div>
                        <div class="table-content">
                        <div class="table-row">
                <div class="table-data">  قيد رقم  :<?php
                 $search_date = $_POST['date'] ?? ''; // Get the search date if it's provided
            $currentDate = $search_date ? date('Y-m-d', strtotime($search_date)) : date('Y-m-d');
                $query = "SELECT number FROM ked WHERE DATE(date) = '$currentDate'";
                $results = mysqli_query($conn, $query);
        
                $row = mysqli_fetch_assoc($results);
                if ($row){
               echo $row['number']; }
               else { echo '  لا يوجد رقم  '  ;}?></div>
            </div>
                        <div class="table-row">
                <div class="table-data">من مذكورين</div>
            </div>
            <form method="post">
            <?php
$search_date = $_POST['date'] ?? ''; // Get the search date if it's provided
$currentDate = $search_date ? date('Y-m-d', strtotime($search_date)) : date('Y-m-d');
mysqli_set_charset($conn, 'utf8'); // Set character set to UTF-8

$sql = "SELECT DISTINCT id, Type, Statement, date FROM treasury_movement WHERE Type = 'مقبوضات' AND DATE(date) = '$currentDate'";
$result_statements = mysqli_query($conn, $sql);
while ($row_statement = mysqli_fetch_assoc($result_statements)) {
    $id_S = $row_statement['id'];
    $statement = $row_statement['Statement'];

    $sql_data = "SELECT DISTINCT money, sand FROM treasury_movement WHERE Statement = ? AND Type = 'مقبوضات' AND DATE(date) = ?";
    $stmt_data = mysqli_prepare($conn, $sql_data);

    if ($stmt_data) {
        mysqli_stmt_bind_param($stmt_data, 'ss', $statement, $currentDate);
        mysqli_stmt_execute($stmt_data);
        $result_data = mysqli_stmt_get_result($stmt_data);

        echo '<div class="table-row">';
        $sql_na2l = "SELECT na2l, fr2s3r FROM na2l_fr2_ked WHERE Statement = ? AND DATE(date) = ? AND Type = 'مقبوضات'";
        $stmt_na2l = mysqli_prepare($conn, $sql_na2l);
        
        if ($stmt_na2l) {
            mysqli_stmt_bind_param($stmt_na2l, 'ss', $statement, $currentDate);
            mysqli_stmt_execute($stmt_na2l);
            $result_na2l = mysqli_stmt_get_result($stmt_na2l);

            $na2l = 0;
            $fr2s3r = 0;
            while ($row_na2l = mysqli_fetch_assoc($result_na2l)) {
                $na2l = $row_na2l['na2l'];
                $fr2s3r = $row_na2l['fr2s3r'];
            }
            $nameValue = urlencode($row_statement['Statement']) . '|' . urlencode($row_statement['Type']) . '|' . urlencode($row_statement['date']);
echo ' <button class="edit-button" type="submit"  name="delete" value="' . $nameValue . '"><i class="fa-solid fa-pen"></i></button>';

            echo '<div class="table-data">' . htmlspecialchars($na2l) . '</div>';
            echo '<div class="table-data">' . htmlspecialchars($fr2s3r) . '</div>';
            echo '<div class="table-data">';
            echo '<table style="margin-left: 40px;" class="nested-table">';

            echo '<tr>';
            echo '<th>' . htmlspecialchars($statement) . '</th>';
            echo '</tr>';

            while ($row_data = mysqli_fetch_assoc($result_data)) {
                $money = $row_data['money'];
                $sand = $row_data['sand'];

                echo '<tr>';
                echo '<td style="padding-right: 50px;">' . htmlspecialchars($sand) . ' </td>';
                echo '<td style="padding-right: 50px;">' . htmlspecialchars($money) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
            echo '</div>';

            echo '<div class="table-data">0</div>';

            $sql_money = "SELECT SUM(money) AS total_money FROM treasury_movement WHERE Statement = ? AND DATE(date) = ? AND Type = 'مقبوضات'";
            $stmt_money = mysqli_prepare($conn, $sql_money);
            
            if ($stmt_money) {
                mysqli_stmt_bind_param($stmt_money, 'ss', $statement, $currentDate);
                mysqli_stmt_execute($stmt_money);
                $result_money = mysqli_stmt_get_result($stmt_money);

                if ($row_money = mysqli_fetch_assoc($result_money)) {
                    $total_money = $row_money['total_money'];
                    echo '<div class="table-data">' . htmlspecialchars($total_money) . '</div>';
                }
            } else {
                echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
            }
        }

        echo '</div>'; // Close the table-row
    }
}

?>


                            <div class="table-row">
                                <div class="table-data">الي مذكورين</div>
                            </div>
<?php
$search_date = $_POST['date'] ?? ''; // Get the search date if it's provided
$currentDate = $search_date ? date('Y-m-d', strtotime($search_date)) : date('Y-m-d');
mysqli_set_charset($conn, 'utf8'); // Set character set to UTF-8

$sql = "SELECT DISTINCT id, Type, Statement, date FROM treasury_movement WHERE Type = 'مدفوعات' AND DATE(date) = '$currentDate'";
$result_statements = mysqli_query($conn, $sql);

while ($row_statement = mysqli_fetch_assoc($result_statements)) {
    $id_S = $row_statement['id'];
    $statement = $row_statement['Statement'];

    $sql_data = "SELECT money, sand FROM treasury_movement WHERE Statement = ? AND Type = 'مدفوعات' AND DATE(date) = ?";
    $stmt_data = mysqli_prepare($conn, $sql_data);

    if ($stmt_data) {
        mysqli_stmt_bind_param($stmt_data, 'ss', $statement, $currentDate);
        mysqli_stmt_execute($stmt_data);
        $result_data = mysqli_stmt_get_result($stmt_data);

        echo '<div class="table-row">';
        $sql_na2l = "SELECT na2l, fr2s3r FROM na2l_fr2_ked WHERE Statement = ? AND DATE(date) = ? AND Type = 'مدفوعات'";
        $stmt_na2l = mysqli_prepare($conn, $sql_na2l);

        if ($stmt_na2l) {
            mysqli_stmt_bind_param($stmt_na2l, 'ss', $statement, $currentDate);
            mysqli_stmt_execute($stmt_na2l);
            $result_na2l = mysqli_stmt_get_result($stmt_na2l);

            $na2l = 0;
            $fr2s3r = 0;
            while ($row_na2l = mysqli_fetch_assoc($result_na2l)) {
                $na2l = $row_na2l['na2l'];
                $fr2s3r = $row_na2l['fr2s3r'];
            }

            if ($statement) {

                    $nameValue = urlencode($row_statement['Statement']) . '|' . urlencode($row_statement['Type']) . '|' . urlencode($row_statement['date']);
echo ' <button class="edit-button" type="submit"  name="delete" value="' . $nameValue . '"><i class="fa-solid fa-pen"></i></button>';

            
                echo '<div class="table-data">' . htmlspecialchars($na2l) . '</div>';
                echo '<div class="table-data">' . htmlspecialchars($fr2s3r) . '</div>';
                echo '<div class="table-data">';
                echo '<table style="margin-left: 40px;" class="nested-table">';
                echo '<tr>';
                echo '<th>' . htmlspecialchars($statement) . '</th>';
                echo '</tr>';
            } else {
                echo '<div class="table-data">لا توجد بيانات</div>';
                echo '<div class="table-data">لا توجد بيانات</div>';
                echo '<div class="table-data">';
                echo '<table style="margin-left: 40px;" class="nested-table">';
                echo '<tr>';
                echo '<th>لا توجد بيانات</th>';
                echo '</tr>';
            }

            while ($row_data = mysqli_fetch_assoc($result_data)) {
                $money = $row_data['money'];
                $sand = $row_data['sand'];

                echo '<tr>';
                echo '<td style="padding-right: 50px;">' . htmlspecialchars($sand) . ' </td>';
                echo '<td style="padding-right: 50px;">' . htmlspecialchars($money) . '</td>';
                echo '</tr>';
            }

            echo '</table>';
            echo '</div>';

            $sql_money = "SELECT SUM(money) AS total_money FROM treasury_movement WHERE Statement = ? AND DATE(date) = ? AND Type = 'مدفوعات'";
            $stmt_money = mysqli_prepare($conn, $sql_money);

            if ($stmt_money) {
                mysqli_stmt_bind_param($stmt_money, 'ss', $statement, $currentDate);
                mysqli_stmt_execute($stmt_money);
                $result_money = mysqli_stmt_get_result($stmt_money);

                if ($row_money = mysqli_fetch_assoc($result_money)) {
                    $total_money = $row_money['total_money'];
                    echo '<div class="table-data">' . htmlspecialchars($total_money) . '</div>';
                    echo '<div class="table-data">0</div>';
                }
            } else {
                echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
            }
        }

        echo '</div>'; // Close the table-row
    }
}
?>

            </form>
                        </div>
                        
                    </div>
                </div>
        </div>
    </div>
    <?php if( $user_type == "محاسب")
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
<?php 
mysqli_close($conn);
?>