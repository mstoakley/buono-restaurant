<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
}

if(isset($_POST['delete'])){
   $item_id = $_POST['item_id']; // Assuming the form sends the ID of the item in the orderitems table
   $delete_cart_item = $conn->prepare("DELETE FROM `orderitems` WHERE ID = ? AND CustomerID = ?");
   $delete_cart_item->execute([$item_id, $user_id]);
   $message[] = 'cart item deleted!';
}

if(isset($_POST['delete_all'])){
   $delete_cart_items = $conn->prepare("DELETE FROM `orderitems` WHERE CustomerID = ?");
   $delete_cart_items->execute([$user_id]);
   // Optionally redirect or confirm to the user
   $message[] = 'all items deleted from cart!';
}

if(isset($_POST['update_qty'])){
   $item_id = $_POST['item_id']; // Adjusted to 'item_id' for clarity
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_VALIDATE_INT); // Using FILTER_VALIDATE_INT for better validation
   if($qty > 0){
       $update_qty = $conn->prepare("UPDATE `orderitems` SET Quantity = ? WHERE ID = ? AND CustomerID = ?");
       $update_qty->execute([$qty, $item_id, $user_id]);
       $message[] = 'cart quantity updated';
   } else {
       $message[] = 'Invalid quantity value'; // Providing feedback for invalid input
   }
}

$grand_total = 0; // Assuming you might calculate this later in the script

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>shopping cart</h3>
   <p><a href="home.php">home</a> <span> / cart</span></p>
</div>

<!-- shopping cart section starts  -->

<section class="products">

   <h1 class="title">your cart</h1>

   <div class="box-container">

   <?php
$grand_total = 0;
$select_order_items = $conn->prepare("SELECT o.ID, o.Quantity, o.Price, m.DishName, m.Image FROM orderitems o INNER JOIN menuitems m ON o.MenuID = m.ID WHERE o.CustomerID = ?");
$select_order_items->execute([$user_id]);
if($select_order_items->rowCount() > 0){
   while($fetch_order_item = $select_order_items->fetch(PDO::FETCH_ASSOC)){
?>
<form action="" method="post" class="box">
   <input type="hidden" name="item_id" value="<?= $fetch_order_item['ID']; ?>">
   <a href="quick_view.php?pid=<?= $fetch_order_item['MenuID']; ?>" class="fas fa-eye"></a>
   <button type="submit" class="fas fa-times" name="delete" onclick="return confirm('delete this item?');"></button>
   <img src="uploaded_img/<?= $fetch_order_item['Image']; ?>" alt="">
   <div class="name"><?= $fetch_order_item['DishName']; ?></div>
   <div class="flex">
      <div class="price"><span>$</span><?= $fetch_order_item['Price']; ?></div>
      <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_order_item['Quantity']; ?>" maxlength="2">
      <button type="submit" class="fas fa-edit" name="update_qty"></button>
   </div>
   <div class="sub-total"> sub total : <span>$<?= $sub_total = ($fetch_order_item['Price'] * $fetch_order_item['Quantity']); ?>/-</span> </div>
</form>
<?php
      $grand_total += $sub_total;
   }
} else {
   echo '<p class="empty">your cart is empty</p>';
}
?>

   </div>

   <div class="cart-total">
      <p>cart total : <span>$<?= $grand_total; ?></span></p>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
   </div>

   <div class="more-btn">
      <form action="" method="post">
         <button type="submit" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" name="delete_all" onclick="return confirm('delete all from cart?');">delete all</button>
      </form>
      <a href="menu.php" class="btn">continue shopping</a>
   </div>

</section>

<!-- shopping cart section ends -->










<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->








<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>