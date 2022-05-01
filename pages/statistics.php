<?php

$top_sold_shop = mysqli_query($database, 'SELECT shop.shop_name, sum(carts.paid) as summAll from carts inner join shop on shop.id = carts.shop_id group by carts.shop_id order by summAll desc limit 1');
$top_sold_shop = mysqli_fetch_row($top_sold_shop);

$get_most_loss_shop = mysqli_query($database, "select shop_id, sum(product_price * products_amount) as sum from shop_products where (utilized = 1 and products_amount !=0) group by shop_id order by sum desc limit 1");
$get_most_loss_shop = mysqli_fetch_row($get_most_loss_shop);
$get_shop_name = mysqli_query($database, 'select shop_name from shop where id = '. $get_most_loss_shop[0]);
$get_shop_name = mysqli_fetch_column($get_shop_name);

?>

<h1>Statistikos</h1>

<h3>Daugiausiai pardavusi parduotuve yra: <?php echo $top_sold_shop[0] ?> - <?php echo $top_sold_shop[1] ?>€ </h3>
<h3>Didziausia nuostoli turinti parduotuve yra: <?php echo $get_shop_name?> - <?php echo round($get_most_loss_shop[1], 2) ?>€ </h3>
<?php




