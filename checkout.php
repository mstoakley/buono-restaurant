<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

if(isset($_POST['submit'])) {
  $total_price = $_POST['total_price'];
    $conn->beginTransaction();
    try {
        $insert_order = $conn->prepare("INSERT INTO orders (CustomerID, OrderDate, TotalAmount) VALUES (?, NOW(), ?)");
        $insert_order->execute([$user_id, $total_price]);
        $order_id = $conn->lastInsertId();

        // Transfer items from cart to orderitems
        $transfer_items = $conn->prepare("INSERT INTO orderitems (OrderID, MenuID, Quantity, Price,CustomerID) SELECT ?, MenuID, Quantity, Price, CustomerID FROM cart WHERE CustomerID = ?");
        $transfer_items->execute([$order_id, $user_id]);

        // Clear the cart
        $clear_cart = $conn->prepare("DELETE FROM `cart` WHERE CustomerID = ?");
        $clear_cart->execute([$user_id]);

        $conn->commit();
        $message[] = 'Order placed successfully!';
    } catch (PDOException $e) {
        $conn->rollBack();
        $message[] = 'Failed to place order: ' . $e->getMessage();
    }
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="checkout">
   <h1 class="title">Order Summary</h1>
   <form action="" method="post">
       <div class="cart-items">
           <h3>Cart Items</h3>
           <?php
           $grand_total = 0;
           $select_cart = $conn->prepare("SELECT * FROM cart WHERE CustomerID = ?");
           $select_cart->execute([$user_id]);
           if($select_cart->rowCount() > 0){
               while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                   $grand_total += ($fetch_cart['Price'] * $fetch_cart['Quantity']);
                   echo '<p><span class="price">$'.$fetch_cart['Price'].' x '.$fetch_cart['Quantity'].'</span></p>';
               }
           } else {
               echo '<p class="empty">Your cart is empty!</p>';
           }
           ?>
           <p class="grand-total"><span class="name">Grand Total:</span><span class="price">$<?= $grand_total; ?></span></p>
           <a href="cart.php" class="btn">View Cart</a>
       </div>
       
       <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
       <input type="submit" value="Place order" class="btn" name="submit">
   </form>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>

