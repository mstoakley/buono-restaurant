<?php
include 'components/connect.php';
session_start();

if(!isset($_SESSION['user_id'])) {
   header('location:home.php');
   exit;
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['submit'])) {
   
   $total_price = $_POST['total_price'];

   
      $current_date = date('Y-m-d H:i:s');
      $insert_order = $conn->prepare("INSERT INTO orders (CustomerID, OrderDate, `Total Amount`) VALUES (?, ?, ?)");
      $insert_order->execute([$user_id, $current_date, $total_price]);

      // Assuming you clear the orderitems after placing an order
      $delete_cart = $conn->prepare("DELETE FROM orderitems WHERE CustomerID = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Order placed successfully. Please pay at pickup!';
   
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
 
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
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
           $select_cart = $conn->prepare("SELECT * FROM orderitems WHERE CustomerID = ?");
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
