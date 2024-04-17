<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:home.php');
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

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
   <h3>orders</h3>
   <p><a href="html.php">home</a> <span> / orders</span></p>
</div>

<section class="orders">

   <h1 class="title">your orders</h1>

   <div class="box-container">

   <?php
     if ($user_id != '') {
    $stmt = $conn->prepare("
SELECT 
    o.ID,
    o.OrderDate,
    o.TotalAmount,
    i.Quantity,
    i.Price,
    m.DishName
FROM 
    Orders o
JOIN 
    OrderItems i ON o.ID = i.OrderID
JOIN 
    MenuItems m ON i.MenuID = m.ID
WHERE 
    o.CustomerID = ?
ORDER BY 
    o.OrderDate DESC;");
    $stmt->execute([$user_id]);
    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='order-box'>";
            echo "<p>Ordered On: " . $row['OrderDate'] . "</p>";
            echo "<p>Dish Name: " . $row['DishName'] . "</p>";
            echo "<p>Quantity: " . $row['Quantity'] . "</p>";
            echo "<p>Price per Item: $" . $row['Price'] . "</p>";
            echo "<p>Subtotal: $" . ($row['Quantity'] * $row['Price']) . "</p>";
            echo "<p>Grand Total: $" . $row['TotalAmount'] . "</p>";
            echo "</div>";
        }
    } else {
        echo '<p class="empty">No orders placed yet!!</p>';
    }
} else {
    header('location:login.php');
    exit;
}

   ?>

   </div>

</section>










<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>