<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};


if (isset($_POST['delete'])) {
    $item_id = $_POST['item_id'];
    $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE ID = ? AND CustomerID = ?");
    $delete_cart_item->execute([$item_id, $user_id]);
    echo "<p class='update-success'>Item removed successfully!</p>";
}

if (isset($_POST['delete_all'])) {
    $delete_cart_items = $conn->prepare("DELETE FROM `cart` WHERE CustomerID = ?");
    $delete_cart_items->execute([$user_id]);
    echo "<p class='update-success'>All items removed successfully!</p>";
}

if (isset($_POST['update_qty'])) {
    $item_id = $_POST['item_id'];
    $qty = filter_input(INPUT_POST, 'qty', FILTER_VALIDATE_INT);
    if ($qty > 0) {
        $update_qty = $conn->prepare("UPDATE `cart` SET Quantity = ? WHERE ID = ? AND CustomerID = ?");
        $update_qty->execute([$qty, $item_id, $user_id]);
        echo "<p class='update-success'>Quantity updated successfully!</p>";
    } else {
        echo "<p class='update-error'>Invalid quantity value!</p>";
    }
}

$grand_total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'components/user_header.php'; ?>

<section class="products">
   <h1 class="title">Your Cart</h1>
   <div class="box-container">
   <?php
   $select_cart_items = $conn->prepare("SELECT c.ID, c.Quantity, c.Price, m.DishName, m.Image, m.ID as MenuID FROM cart c INNER JOIN menuitems m ON c.MenuID = m.ID WHERE c.CustomerID = ?");
   $select_cart_items->execute([$user_id]);

   if ($select_cart_items->rowCount() > 0) {
       while ($fetch_cart_item = $select_cart_items->fetch(PDO::FETCH_ASSOC)) {
           $sub_total = $fetch_cart_item['Price'] * $fetch_cart_item['Quantity'];
           $grand_total += $sub_total;
           ?>
           <form action="" method="post" class="box">
               <input type="hidden" name="item_id" value="<?= $fetch_cart_item['ID']; ?>">
               <a href="quick_view.php?pid=<?= $fetch_cart_item['MenuID']; ?>" class="fas fa-eye"></a>
               <button type="submit" class="fas fa-times" name="delete" onclick="return confirm('Delete this item?');"></button>
               <img src="FoodImages/<?= $fetch_cart_item['Image']; ?>" alt="">
               <div class="name"><?= $fetch_cart_item['DishName']; ?></div>
               <div class="flex">
                   <div class="price"><span>$</span><?= $fetch_cart_item['Price']; ?></div>
                   <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart_item['Quantity']; ?>">
                   <button type="submit" class="fas fa-edit" name="update_qty"></button>
               </div>
               <div class="sub-total">Sub total: <span>$<?= $sub_total; ?></span></div>
           </form>
           <?php
       }
   } else {
       echo '<p class="empty">Your cart is empty</p>';
   }
   ?>
   </div>
   <div class="cart-total">
       <p>Cart total: <span>$<?= $grand_total; ?></span></p>
       <a href="checkout.php" class="btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
   </div>
   <div class="more-btn">
       <form action="" method="post">
           <button type="submit" class="delete-btn <?= ($grand_total > 1) ? '' : 'disabled'; ?>" name="delete_all" onclick="return confirm('Delete all from cart?');">Delete All</button>
       </form>
       <a href="menu.php" class="btn">Continue Shopping</a>
   </div>
</section>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
