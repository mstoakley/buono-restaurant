<?php
include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
};

include 'components/add_cart.php';
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
   
<!-- Existing header include -->
<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>our menu</h3>
   <p><a href="home.php">home</a> <span> / menu</span></p>
</div>

<section class="products">
   <h1 class="title">latest dishes</h1>
   <div class="box-container">
      <?php
         $select_menuitems = $conn->prepare("SELECT * FROM `menuitems`");
         $select_menuitems->execute();
         if($select_menuitems->rowCount() > 0){
            while($fetch_menuitems = $select_menuitems->fetch(PDO::FETCH_ASSOC)){
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $fetch_menuitems['ID']; ?>">
         <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_menuitems['DishName'], ENT_QUOTES); ?>">
         <input type="hidden" name="price" value="<?= $fetch_menuitems['Price']; ?>">
         <input type="hidden" name="image" value="<?= $fetch_menuitems['Image']; ?>">
         <a href="quick_view.php?pid=<?= $fetch_menuitems['ID']; ?>" class="fas fa-eye"></a>
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="FoodImages/<?= $fetch_menuitems['Image']; ?>" alt="<?= htmlspecialchars($fetch_menuitem['DishName']); ?>">
         <a href="category.php?category=<?= htmlspecialchars($fetch_menuitems['Origin'], ENT_QUOTES); ?>" class="cat"><?= htmlspecialchars($fetch_menuitems['Origin'], ENT_QUOTES); ?></a>
         <div class="name"><?= htmlspecialchars($fetch_menuitems['DishName'], ENT_QUOTES); ?></div>
         <div class="flex">
            <div class="price"><span>$</span><?= $fetch_menuitems['Price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
         </div>
      </form>
      <?php
            }
         }else{
            echo '<p class="empty">no products added yet!</p>';
         }
      ?>
   </div>
</section>

</body>
</html>






















<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->








<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>