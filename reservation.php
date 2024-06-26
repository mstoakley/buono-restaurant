<?php
include 'components/connect.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:home.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = ""; // To store messages to display to the user

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['make_reservation'])) {
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
        $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
        $time = filter_input(INPUT_POST, 'time', FILTER_SANITIZE_STRING);
        $party_size = filter_input(INPUT_POST, 'party_size', FILTER_VALIDATE_INT);

        // Combine date and time to fit the datetime format expected by SQL
        $reservation_datetime = $date . ' ' . $time;

        // Checking table availability
        $stmt = $conn->prepare("CALL CheckTableAvailability(?, ?)");
        $stmt->execute([$reservation_datetime, $party_size]);
        $available = $stmt->fetch();
        $stmt->closeCursor();

        if ($available) {
            // Proceed to add a new reservation
            $addReservation = $conn->prepare("CALL AddNewReservation(?, ?, ?, ?)");
            $addReservation->execute([$user_id, $available['ID'], $reservation_datetime, $party_size]);
            $message = "Reservation made successfully for {$date} at {$time}.";
            $addReservation->closeCursor();
        } else {
            $message = "No tables available for the selected date and time. Please choose another time.";
        }
    }

    if (isset($_POST['cancel_reservation'])) {
        $reservation_id = $_POST['reservation_id'];

        // Call the stored procedure to cancel the reservation
        $cancelReservation = $conn->prepare("CALL CancelReservation(?)");
        $cancelReservation->execute([$reservation_id]);

        // Check if the reservation was cancelled successfully
        if ($cancelReservation->rowCount() > 0) {
            $message = "Reservation cancelled successfully.";
        } else {
            $message = "Failed to cancel the reservation or reservation time is too close.";
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reservation</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />

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
   <h3>Reservations</h3>
   <p><a href="home.php">home</a> <span> / Reservations</span></p>
</div>

<!-- about section starts  -->

<section class="Reservation">

   <div class="row">

      <div class="image">
         <img src="images/about-img.svg" alt="">
      </div>

      <div class="content">
         <h3>Choose a Reservations Date!!!</h3>
         <p>Please  be patient, Reservations free up ANYTIME</p>

   <form action="reservation.php" method="post">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" required><br>
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required><br>
  <label for="phone">Phone Number:</label>
  <input type="tel" id="phone" name="phone" required><br>
  <label for="date">Date:</label>
  <input type="date" id="date" name="date" required><br>
  <label for="time">Time:</label>
  <input type="time" id="time" name="time" required><br>
  <label for="party_size">Party Size:</label>
  <input type="number" id="party_size" name="party_size" min="1" required><br>
  <button type="submit" name = "make_reservation">Make Reservation</button>
</form>
      </div>
<div class="reservations-list">
    <h2>Your Reservations</h2>
    <?php
    $reservations = $conn->prepare("SELECT ID, DateofRes, NumofGuests FROM reservations WHERE CustomerID = ?");
    $reservations->execute([$user_id]);
    if ($reservations->rowCount() > 0) {
     while ($row = $reservations->fetch(PDO::FETCH_ASSOC)) {
    echo "<div class='reservation-item'>";
    echo "<p>Reservation on: " . htmlspecialchars($row['DateofRes']) . " for " . htmlspecialchars($row['NumofGuests']) . " guests</p>";
    echo "<form method='post' action='update_reservation.php'>";
    echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($row['ID']) . "'>";
    echo "<button type='submit' name='update_reservation'>Edit</button><br>";
    echo "</form>";
	echo "<form method='post' action='reservation.php'>";
	 echo "<input type='hidden' name='reservation_id' value='" . htmlspecialchars($row['ID']) . "'>";
echo "<button type='submit' name='cancel_reservation' onclick='return confirm(\"Are you sure you want to cancel this reservation?\")'>Cancel</button>";
    echo "</div>";
	echo "</form>";
     }
}
 else {
        echo "<p>No reservations found.</p>";
    }
    ?>
</div>

   </div>

</section>

<!-- about section ends -->

<!-- steps section starts  -->

<section class="steps">

   <h1 class="title">simple steps</h1>

   <div class="box-container">

      <div class="box">
         <img src="FoodImages/step-1.png" alt="">
         <h3>choose order</h3>
      </div>

      <div class="box">
         <img src="FoodImages/clock-icon.png" alt="">
         <h3>or Make a Reservation</h3>
      </div>

      <div class="box">
         <img src="FoodImages/step-3.png" alt="">
         <h3>enjoy food</h3>
      </div>

   </div>

</section>

<!-- steps section ends -->

<!-- reviews section starts  -->

<section class="reviews">

   <h1 class="title">customer's reivews</h1>

   <div class="swiper reviews-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <img src="images/pic-1.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos voluptate eligendi laborum molestias ut earum nulla sint voluptatum labore nemo.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>David Blane</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-2.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos voluptate eligendi laborum molestias ut earum nulla sint voluptatum labore nemo.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Josh Peck</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-3.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos voluptate eligendi laborum molestias ut earum nulla sint voluptatum labore nemo.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Hiroyuki Sanada </h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-4.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos voluptate eligendi laborum molestias ut earum nulla sint voluptatum labore nemo.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>john deo</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-5.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos voluptate eligendi laborum molestias ut earum nulla sint voluptatum labore nemo.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Denzel Washington</h3>
         </div>

         <div class="swiper-slide slide">
            <img src="images/pic-6.png" alt="">
            <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quos voluptate eligendi laborum molestias ut earum nulla sint voluptatum labore nemo.</p>
            <div class="stars">
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star"></i>
               <i class="fas fa-star-half-alt"></i>
            </div>
            <h3>Viola Davis</h3>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>

<!-- reviews section ends -->



















<!-- footer section starts  -->
<?php include 'components/footer.php'; ?>
<!-- footer section ends -->=






<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>

var swiper = new Swiper(".reviews-slider", {
   loop:true,
   grabCursor: true,
   spaceBetween: 20,
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
   breakpoints: {
      0: {
      slidesPerView: 1,
      },
      700: {
      slidesPerView: 2,
      },
      1024: {
      slidesPerView: 3,
      },
   },
});

</script>

</body>
</html>