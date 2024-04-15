<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
}

if(isset($_POST['submit'])){

   $fname = filter_var($_POST['Fname'], FILTER_SANITIZE_STRING); // Assuming 'fname' field for the first name
   $lname = filter_var($_POST['LName'], FILTER_SANITIZE_STRING); // Assuming 'lname' field for the last name
   $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
   $pass = $_POST['pass'];
   $cpass = $_POST['cpass'];

   if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      $message[] = 'invalid email format!';
   } else if($pass != $cpass){
      $message[] = 'confirm password not matched!';
   } else {
      $select_user = $conn->prepare("SELECT * FROM `customers` WHERE Email = ?");
      $select_user->execute([$email]);
      if($select_user->rowCount() > 0){
         $message[] = 'email already exists!';
      }else{
         $pass = password_hash($pass, PASSWORD_DEFAULT);
         $insert_user = $conn->prepare("INSERT INTO `customers`(Email, Password, Fname, LName) VALUES(?,?,?,?)");
         $insert_user->execute([$email, $pass, $fname, $lname]);
         $_SESSION['user_id'] = $conn->lastInsertId();
         header('location:home.php');
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
   <title>register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">

   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="name" required placeholder="enter your name" class="box" maxlength="50">
      <input type="email" name="email" required placeholder="enter your email" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" required placeholder="enter your number" class="box" min="0" max="9999999999" maxlength="10">
      <input type="password" name="pass" required placeholder="enter your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="cpass" required placeholder="confirm your password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" name="submit" class="btn">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</section>











<?php include 'components/footer.php'; ?>







<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>