<h1>Pagrindinis</h1>
<h3>
    <?php
        ?> Sveiki <?php
        switch (loggedInEmployeesData('role')) {
            case 'warehouse_worker':
                echo 'sandelio darbuotojas ';
                break;
            case 'shop_worker':
                echo 'parduotuves darbuotojas ';
                break;
        }
        echo loggedInEmployeesData('name');
        ?>
        prisijunge.
</h3>