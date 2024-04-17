<?php

include 'components/connect.php';
if(isset($_POST['add_to_cart'])){

if(!isset($_SESSION['user_id'])){
    header('location:login.php');
    exit;
}else{
	$user_id = $_SESSION['user_id'];
$pid = filter_input(INPUT_POST, 'pid', FILTER_SANITIZE_NUMBER_INT);
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$qty = filter_input(INPUT_POST, 'qty', FILTER_VALIDATE_INT);

// Check if item already exists in the temporary cart
$check_cart_item = $conn->prepare("SELECT * FROM `cart` WHERE CustomerID = ? AND MenuID = ?");
$check_cart_item->execute([$user_id, $pid]);

if($check_cart_item->rowCount() > 0){
    $message[] = 'Item already added to cart!';
} else {
    // Insert item into the temporary cart table
	echo("Attempting to insert into cart: PID={$pid}, Name={$name}, Price={$price}, Qty={$qty}");

    $insert_cart = $conn->prepare("INSERT INTO `cart`(CustomerID, MenuID, Price, Quantity) VALUES(?,?,?,?)");
    $insert_cart->execute([$user_id, $pid, $price, $qty]);
    $message[] = 'Added to cart successfully!';
}

	
}

}
?>

