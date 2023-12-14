<?php
// Create connection
$conn = require __DIR__ . "/connect.php";
require_once ('loggedin.php');
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

    <title>الورديه</title>
</head>


<body>
    
    <!-- main body section -->
    <div class="main-body1">
        <div class="container">
            <div class="nav1">
                <h1>الورديه</h1>
                <a href="storageoil.php"><button style="margin-left: 90px;" class="save">المخزن</button></a>
                <a href="wardyaoil.php"><button class="save">الورديه</button></a>
                <input class="cal" type="datetime-local" id="meeting-time" name="meeting-time">
            </div>
                <div class="form-row1">
                    <label for="oil-type">اختر نوع الزيت</label>
                    <br>
                    <input type="text" id="oil-type" class="bo">
                </div>
                <div class="form-row1">
                    <label for="start-balance">رصيد اول المده</label>
                    <br>
                    <input type="number" id="start-balance" class="bo">
                </div>
                <div class="form-row1">
                    <label for="end-balance">رصيد اخر المده</label>
                    <br>
                    <input type="number" id="end-balance" class="bo">
                </div>
                <div class="form-row1">
                    <label for="inventory-in">وارد</label>
                    <br>
                    <input type="number" id="inventory-in" class="bo">
                </div>
                <div class="form-row1">
                    <label for="inventory-in">مباع</label>
                    <br>
                    <input type="number" id="inventory-In" class="bo">
                </div>
                <br>
                    <button style="margin-bottom: 30px;
                    margin-top: 30px;
                    margin-left: 70px;"  id="myform" type="submit" class="save">حفظ</button>
                    <div class="container">
	
                        <div class="table" id="datatable">
                            <div class="table-header">
                                <div class="header__item"><a id="name" class="filter__link" href="#">نوع الزيت</a></div>
                                <div class="header__item"><a id="wins" class="filter__link filter__link--number" href="#">اول المده</a></div>
                                <div class="header__item"><a id="draws" class="filter__link filter__link--number" href="#">اخر المده</a></div>
                                <div class="header__item"><a id="losses" class="filter__link filter__link--number" href="#">وارد</a></div>
                                <div class="header__item"><a id="total" class="filter__link filter__link--number" href="#">مباع</a></div>
                            </div>
                            <div class="table-content">	
                                <div class="table-row">		
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                </div>
                                <div class="table-row">
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                </div>
                                <div class="table-row">
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                    <div class="table-data">1</div>
                                </div>
                            </div>	
                        </div>
                    </div>
                  <a href="/adminhome.html"><button  style="margin-bottom: 30px;
                    margin-top: 30px;
                    margin-left: 70px;" class="save">اضافه/حدف</button></a>
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
<script src="script.js"></script>
</body>

</html>