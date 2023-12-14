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
    <link rel="shortcut icon" type="x-icon" href="images/TOT-b644c798.png">

    <title>يوميه الخزينه</title>
</head>



<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>يوميه الخزينه</h1>
               
                <div class="cal-wrapper">
                <form method="post">
    <button style="margin-bottom: 30px; margin-top: -50px; margin-left: 70px;" id="myform" type="submit" class="save" name="search">بحث</button>
    <input style="margin-left: 70px;" class="cal" type="date" id="meeting_time" name="date" value="<?php echo isset($_POST['date']) ? $_POST['date'] : date('Y-m-d'); ?>">

<button style="margin-bottom: 30px; margin-top: -50px; margin-left: 10px;" id="todayButton" class="save" onclick="goToToday()">اليوم</button>
</form>
                <br><br>
                  </div>           
                 </div>
                      <div style="width: 81%;
                      margin-left:15px" class="table" id="datatable">
                        <div class="table-header">
                            <div class="header__item"><a id="name" class="filter__link" href="#">المبلغ</a></div>
                            <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">السعر</a></div>
                            <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">الكميه</a></div>
                            <div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">البيان</a></div>
                        </div>
                        <div class="table-content">	
                        <?php

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
if (isset($_POST['search'])) {
    $search_date = $_POST['date'];
    $search_date_formatted = date('Y-m-d', strtotime($search_date));

    // Display data in the table
    if (!isset($_POST['search'])) {
        echo '<div class="table-row">لا توجد بيانات<br><br></div>';
    } else {

        $query2 = "SELECT * FROM benzene WHERE date = '$search_date_formatted'";
        $result2 = mysqli_query($conn, $query2);

        if (!$result2) {
            echo '<div class="table-row">لا توجد بيانات<br><br></div>';
        }

        // Fetch the prices of each type from benzene_price table
        $queryPrices = "SELECT * FROM benzene_price WHERE date = '$search_date_formatted'";
        $resultPrices = mysqli_query($conn, $queryPrices);
        if (!$resultPrices) {
            $solar_price = 0;
            $ben80_price = 0;
            $ben92_price = 0;
            $ben95_price = 0;
        } else {
            $pricesRow = mysqli_fetch_assoc($resultPrices);
            $solar_price = $pricesRow['solar_price'] ?? 0;
            $ben80_price = $pricesRow['ben80_price'] ?? 0;
            $ben92_price = $pricesRow['ben92_price'] ?? 0;
            $ben95_price = $pricesRow['ben95_price'] ?? 0;
        }

        $totals = array(); // Initialize an associative array to store totals for each trumba_type
        $grandTotal = 0; // Initialize the grand total variable

        while ($row = mysqli_fetch_assoc($result2)) {
            // Check if the row date matches the custom search date
            if (date('Y-m-d', strtotime($row['date'])) === $search_date_formatted) {
                $trumba_type = $row['trumba_type'];

                // Check if the trumba_type exists in the $totals array
                if (array_key_exists($trumba_type, $totals)) {
                    // If exists, add the current total to the existing total
                    $totals[$trumba_type] += $row['total'];
                } else {
                    // If not exists, initialize the total for the trumba_type
                    $totals[$trumba_type] = $row['total'];
                }

                // Update the grand total by adding the current row's total multiplied by its price
                $grandTotal += ($row['total'] * getPriceByType($trumba_type));
            }
        }

        // Fetch data from the oil_ward table for the custom search date
        $queryOil = "SELECT SUM(saled) AS total_saled, SUM(total) AS total_price FROM oil_ward WHERE date = '$search_date_formatted'";
        $resultOil = mysqli_query($conn, $queryOil);
        $oilRow = mysqli_fetch_assoc($resultOil);
        if (!$resultOil) {
            $totalSaledOil = 0;
            $totalPriceOil = 0;
        } else {
            // Calculate the total sales and total price for the oil row
            $totalSaledOil = $oilRow['total_saled'] ?? 0;
            $totalPriceOil = $oilRow['total_price'] ?? 0;
        }

        // Update the grand total by adding the totalPriceOil to it
        $grandTotal += $totalPriceOil;

        // Fetch data from the بونات table for the custom search date
        $queryBonat = "SELECT COUNT(*) AS total_rows, SUM(total) AS total_values FROM bons WHERE date = '$search_date_formatted'";
        $resultBonat = mysqli_query($conn, $queryBonat);
        $bonatRow = mysqli_fetch_assoc($resultBonat);
        if (!$resultBonat) {
            $totalRowsBonat = 0;
            $totalValuesBonat = 0;
        } else {
            // Calculate the total number of rows and total values for the بونات table
            $totalRowsBonat = $bonatRow['total_rows'] ?? 0;
            $totalValuesBonat = $bonatRow['total_values'] ?? 0;
        }

        // Fetch data from the term_clients table for the custom search date
        $queryTermClients = "SELECT COUNT(*) AS total_rows, SUM(money) AS total_values FROM term_clients WHERE date = '$search_date_formatted'";
        $resultTermClients = mysqli_query($conn, $queryTermClients);
        $termClientsRow = mysqli_fetch_assoc($resultTermClients);
        if (!$resultTermClients) {
            $totalRowsTermClients = 0;
            $totalValuesTermClients = 0;
        } else {
            // Calculate the total number of rows and total values for the term_clients table
            $totalRowsTermClients = $termClientsRow['total_rows'] ?? 0;
            $totalValuesTermClients = $termClientsRow['total_values'] ?? 0;
        }

        // Fetch data from the expenses table for the custom search date
        $queryexpenses = "SELECT COUNT(*) AS total_rows, SUM(money) AS total_values FROM expenses WHERE date = '$search_date_formatted'";
        $resultexpenses = mysqli_query($conn, $queryexpenses);
        $expensesRow = mysqli_fetch_assoc($resultexpenses);
        if (!$resultexpenses) {
            $totalRowsexpenses = 0;
            $totalValuesexpenses = 0;
        } else {
            // Calculate the total number of rows and total values for the expenses table
            $totalRowsexpenses = $expensesRow['total_rows'] ?? 0;
            $totalValuesexpenses = $expensesRow['total_values'] ?? 0;
        }

        $grandTotal2 = $totalValuesexpenses + $totalValuesBonat + $totalValuesTermClients;
        $grandTotal3 = $grandTotal - $grandTotal2;

        $querygzyra = "SELECT Quantity, money, price FROM daily_treasury WHERE Statement = 'الجزيرة' AND date = '$search_date_formatted'";
        $resultgzyra = mysqli_query($conn, $querygzyra);
        if (!$resultgzyra) {
            die('Query Error: ' . mysqli_error($conn));
        }

        // If the row exists, fetch and display its data
        $row_gzyra = mysqli_fetch_assoc($resultgzyra);
        $totalRowsgzyra = $row_gzyra['Quantity'] ?? 0;
        $priceRowsgzyra = $row_gzyra['price'] ?? 0;
        $totalValuesgzyra = $row_gzyra['money'] ?? 0;

        // Fetch data from the daily_treasury table for 'من الوردية' row
        $querywardya = "SELECT Quantity, money, price FROM daily_treasury WHERE Statement = 'من الوردية' AND date = '$search_date_formatted'";
        $resultwardya = mysqli_query($conn, $querywardya);
        if (!$resultwardya) {
            die('Query Error: ' . mysqli_error($conn));
        }

        // If the row exists, fetch and display its data
        $row_wardya = mysqli_fetch_assoc($resultwardya);
        $totalRowswardya = $row_wardya['Quantity'] ?? 0;
        $priceRowswardya = $row_wardya['price'] ?? 0;
        $totalValueswardya = $row_wardya['money'] ?? 0;
    }
        // Now, we can loop through the $totals array to display the table
        foreach ($totals as $trumba_type => $total) {
            echo '<div class="table-row">';
            echo '<div class="table-data">' . ($total * getPriceByType($trumba_type)) . '</div>';
            echo '<div class="table-data">' . getPriceByType($trumba_type) . '</div>';
            echo '<div class="table-data">' . $total . '</div>';
            echo '<div class="table-data">' . $trumba_type . '</div>';
            echo '</div>';
        }

        // Display the "oil" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($totalPriceOil) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>';
        echo '<div class="table-data">' . ($totalSaledOil) . '</div>';
        echo '<div class="table-data">زيت</div>';
        echo '</div>';

        // Display the grand total
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($grandTotal) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the grand total row
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for total column in the grand total row
        echo '<div class="table-data">الاجمالي</div>';
        echo '</div>';

        // Display the "بونات" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($totalValuesBonat) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the "بونات" row
        echo '<div class="table-data">' . $totalRowsBonat . '</div>';
        echo '<div class="table-data">بونات</div>';
        echo '</div>';

        // Display the "عملاء اجلا" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($totalValuesTermClients) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the "عملاء اجلا" row
        echo '<div class="table-data">' . $totalRowsTermClients . '</div>';
        echo '<div class="table-data">عملاء اجله</div>';
        echo '</div>';

        // Display the "مصاريف" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($totalValuesexpenses) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the "مصاريف" row
        echo '<div class="table-data">' . $totalRowsexpenses . '</div>';
        echo '<div class="table-data"> المصاريف</div>';
        echo '</div>';

        // Display the grand total
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($grandTotal2) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the grand total row
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for total column in the grand total row
        echo '<div class="table-data">الاجمالي</div>';
        echo '</div>';

        // Display the grand total
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($grandTotal3) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the grand total row
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for total column in the grand total row
        echo '<div class="table-data">الصافي</div>';
        echo '</div>';

        // Display the "الجزيرة" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($totalValuesgzyra) . '</div>';
        echo '<div class="table-data">' . ($priceRowsgzyra) . '</div>';
        echo '<div class="table-data">' . ($totalRowsgzyra) . '</div>';
        echo '<div class="table-data">الجزيرة</div>';
        echo '</div>';

        // Display the "من الوردية" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($totalValueswardya) . '</div>';
        echo '<div class="table-data">' . ($priceRowswardya) . '</div>';
        echo '<div class="table-data">' . ($totalRowswardya) . '</div>';
        echo '<div class="table-data">من الوردية</div>';
        echo '</div>';

        // Calculate "الصافي النقديه"
        $safy_na2dya = ($grandTotal3 + $totalValuesgzyra) - $totalValueswardya;

        // Display the "الصافي النقديه" row
        echo '<div class="table-row">';
        echo '<div class="table-data">' . ($safy_na2dya) . '</div>';
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for price column in the grand total row
        echo '<div class="table-data">' . '-' . '</div>'; // Empty cell for total column in the grand total row
        echo '<div class="table-data"> الصافي النقديه</div>';
        echo '</div>';
    }
    else {
        echo '<div class="table-row"><div class="table-data" colspan="7">لا توجد بيانات</div></div>';
    }
}


// Function to get the price by type from the fetched prices
function getPriceByType($type)
{
    global $solar_price, $ben80_price, $ben92_price, $ben95_price;
    switch ($type) {
        case 'سولار':
            return $solar_price;
        case 'بنزين 80':
            return $ben80_price;
        case 'بنزين 92':
            return $ben92_price;
        case 'بنزين 95':
            return $ben95_price;
        default:
            return 0;
    }
}

?>

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
        document.getElementById('meeting_time').value = today; // Set the input field value to today's date
    }
</script>
</body>

</html>
<?php
mysqli_close($conn);
?>