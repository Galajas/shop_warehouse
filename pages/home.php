<h1>Pagrindinis</h1>
<?php
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $get_user = mysqli_query($database, "SELECT * FROM employees WHERE email = '$email'");
    $get_user = mysqli_fetch_object($get_user);

    $get_user_name = $get_user->name;
    $get_user_role = $get_user->role;
}
?>
<h3>
    <?php
    if (isset($_SESSION['email'])) {
        ?> Sveiki <?php

        switch ($get_user_role) {
            case 'warehouse_worker':
                echo 'sandelio darbuotojas ';
                break;
            case 'shop_worker':
                echo 'parduotuves darbuotojas ';
                break;
        }
        echo $get_user_name;
        ?>
        prisijunge.
        <?php
    }
    ?>

</h3>