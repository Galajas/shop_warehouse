<h1>Pagrindinis</h1>
<h3>
    <?php
    
    if (isLoged()) {
        ?> Sveiki <?php
        $email = $_SESSION['email'];
        switch (getEmployeesData($email, 'role')) {
            case 'warehouse_worker':
                echo 'sandelio darbuotojas ';
                break;
            case 'shop_worker':
                echo 'parduotuves darbuotojas ';
                break;
        }
        echo getEmployeesData($email, 'name');
        ?>
        prisijunge.
        <?php
    } else {
        ?>
        Sveiki atvyke i parduotuve.
        <?php
    }
    ?>
</h3>