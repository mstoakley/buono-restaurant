<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:home.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$message = []; 

include 'components/connect.php';

// Check if the reservation_id is set and fetch the reservation details
if (isset($_POST['reservation_id'])) {
    $reservation_id = filter_var($_POST['reservation_id'], FILTER_VALIDATE_INT);

    $select_reservation = $conn->prepare("SELECT * FROM reservations WHERE ID = ?");
    $select_reservation->execute([$reservation_id]);
    $reservation = $select_reservation->fetch(PDO::FETCH_ASSOC);
}

// Check if the form to change the reservation has been submitted
if (isset($_POST['change_reservation'])) {
    // Filter the input
    $date = filter_var($_POST['date'], FILTER_SANITIZE_STRING);
    $time = filter_var($_POST['time'], FILTER_SANITIZE_STRING);
    $party_size = filter_var($_POST['party_size'], FILTER_SANITIZE_NUMBER_INT);

    // Combine the date and time to fit the datetime format for SQL
    $reservation_datetime = $date . ' ' . $time . ':00';

    // Update the reservation in the database
    $update_reservation = $conn->prepare("UPDATE reservations SET DateofRes = ?, NumofGuests = ? WHERE ID = ?");
    $update_reservation->execute([$reservation_datetime, $party_size, $reservation_id]);

    if ($update_reservation->rowCount() > 0) {
        $message[] = 'Reservation updated successfully.';
    } else {
        $message[] = 'Failed to update the reservation or no changes were made.';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>update profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container update-form">

  <form action="update_reservation.php" method="post">
     <h3>update or cancel reservation</h3>
    <input type="hidden" name="reservation_id" value="<?= $reservation_id; ?>">
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
          <button type="submit" name="change_reservation">Update Reservation</button>
          </td>
        </tr>
      
    </tbody>
  </table>
   </form>

</section>










<?php include 'components/footer.php'; ?>






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>