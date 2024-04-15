<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:home.php');
    exit;
}
$user_id = $_SESSION['user_id'];
$message = []; // Initialize as array to avoid undefined variable issues

// Assuming you have included the database connection already
include 'components/connect.php';

if (isset($_POST['submit'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    

    // Update Name
    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE customers SET Fname = ? WHERE ID = ?");
        $update_name->execute([$name, $user_id]);
    }

    // Update Email after checking its uniqueness
    if (!empty($email)) {
        $select_email = $conn->prepare("SELECT ID FROM customers WHERE Email = ? AND ID != ?");
        $select_email->execute([$email, $user_id]);
        if ($select_email->rowCount() > 0) {
            $message[] = 'Email already taken!';
        } else {
            $update_email = $conn->prepare("UPDATE customers SET Email = ? WHERE ID = ?");
            $update_email->execute([$email, $user_id]);
        }
    }


    // Update Password if old password matches
    if (!empty($_POST['old_pass']) && !empty($_POST['new_pass']) && !empty($_POST['confirm_pass'])) {
        $old_pass = sha1($_POST['old_pass']);
        $new_pass = sha1($_POST['new_pass']);
        $confirm_pass = sha1($_POST['confirm_pass']);

        $select_prev_pass = $conn->prepare("SELECT Password FROM customers WHERE ID = ?");
        $select_prev_pass->execute([$user_id]);
        $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);

        if ($old_pass != $fetch_prev_pass['Password']) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'Confirm password not matched!';
        } else {
            $update_pass = $conn->prepare("UPDATE customers SET Password = ? WHERE ID = ?");
            $update_pass->execute([$confirm_pass, $user_id]);
            $message[] = 'Password updated successfully!';
        }
    }
}

// Load user info for placeholders
$select_profile = $conn->prepare("SELECT * FROM customers WHERE ID = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

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

   <form action="" method="post">
      <h3>update profile</h3>
      <input type="text" name="name" placeholder="<?= $fetch_profile['Fname']; ?>" class="box" maxlength="50">
      <input type="email" name="email" placeholder="<?= $fetch_profile['Email']; ?>" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="old_pass" placeholder="enter your old password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="new_pass" placeholder="enter your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="confirm_pass" placeholder="confirm your new password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="update now" name="submit" class="btn">
   </form>

</section>










<?php include 'components/footer.php'; ?>






<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>