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
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>



<section class="hero">

   <div class="swiper hero-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <div class="content">
               <span>order online</span>
               <h3>Delicious Foods All Over the World !!</h3>
               <a href="menu.html" class="btn">see menus</a>
            </div>
            <div class="image">
               <img src="FoodImages/home-img-1.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>order online</span>
               <h3>Cheesy Burger</h3>
               <a href="menu.html" class="btn">see menus</a>
            </div>
            <div class="image">
               <img src="FoodImages/home-img-2.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>order online</span>
               <h3>Roasted chicken</h3>
               <a href="menu.html" class="btn">see menus</a>
            </div>
            <div class="image">
               <img src="FoodImages/home-img-3.png" alt="">
            </div>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<section class="category" 

     <h1 class="title">food category</h1>

   <div class="box-container">

      <a href="category.php?category=Spain" class="box">
         <img src="FoodImages/cat-1.png" alt="">
         <h3>Spain</h3>
      </a>

      <a href="category.php?category=Italy" class="box">
         <img src="FoodImages/cat-2.png" alt="">
         <h3>Italy</h3>
      </a>

      <a href="category.php?category=India" class="box">
         <img src="FoodImages/cat-3.png" alt="">
         <h3>India</h3>
      </a>

      <a href="category.php?category=United States" class="box">
         <img src="FoodImages/cat-4.png" alt="">
         <h3>United States</h3>
      </a>

   </div>

</section>




<section class="menu-items">
    <h1 class="title">Featured Menu Items</h1>
    <div class="box-container">
        <?php
        $select_menuitems = $conn->prepare("SELECT * FROM menuitems LIMIT 5"); 
        $select_menuitems->execute();
        if($select_menuitems->rowCount() > 0){
            while($fetch_menuitem = $select_menuitems->fetch(PDO::FETCH_ASSOC)){
        ?>
        <div class="box">
            <img src="FoodImages/<?= $fetch_menuitem['Image']; ?>" alt="<?= htmlspecialchars($fetch_menuitem['DishName']); ?>" width = "200" height = "200">
            <div class="content">
                <h3><?= htmlspecialchars($fetch_menuitem['DishName']); ?></h3>
                <span>$<?= number_format($fetch_menuitem['Price'], 2); ?></span>
                <a href="menu.php?item=<?= $fetch_menuitem['ID']; ?>" class="btn">Order Now</a> 
            </div>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">No menu items found!</p>';
        }
        ?>
    </div>
	  <div class="more-btn">
      <a href="menu.php" class="btn">view all</a>
   </div>

</section>


 


















<?php include 'components/footer.php'; ?>


<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".hero-slider", {
   loop:true,
   grabCursor: true,
   effect: "flip",
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
});

</script>

</body>
</html>