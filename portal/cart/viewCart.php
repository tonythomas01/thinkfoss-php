<?php
session_start();
if ( !isset( $_SESSION['loggedin_user'] ) ) {
	header( 'Location: ../../signup.php');
} else if ( isset( $_SESSION['loggedin_user_id'] ) ) {
	require_once('../../assets/php/access/accessDB.php');
	require_once('../../assets/php/User.php');
	$user = User::newFromUserId( $_SESSION['loggedin_user_id'], $conn );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!-- Basic Page Needs
	================================================== -->
	<meta charset="utf-8">
	<!--[if IE]><meta http-equiv="x-ua-compatible" content="IE=9" /><![endif]-->
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>ThinkFOSS - code | train | grow</title>
	<meta name="msvalidate.01" content="AACA4869B9C746F7F151D39BF5D19CB2" />
	<meta name="description" content=" ThinkFOSS aims at providing Open Source training and solutions to Individuals, Schools, Universities and Industries in need. ThinkFOSS is a collection of Open Source enthusiasts and entrepreneurs who are ready to spend their time spreading FOSS technologies">
	<meta name="keywords" content="thinkfoss, fossatamrita, training, open source, open source solutions">
	<meta name="author" content="thinkfoss.com">

	<!-- Favicons
	================================================== -->
	<link rel="shortcut icon" href="../../img/favicon.ico" type="image/x-icon">
	<link rel="apple-touch-icon" href="../../img/apple-touch-icon.png">
	<link rel="apple-touch-icon" sizes="72x72" href="../../img/apple-touch-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="114x114" href="../../img/apple-touch-icon-114x114.png">

	<!-- Bootstrap -->
	<link rel="stylesheet" type="text/css"  href="../../css/bootstrap.css">
	<link rel="stylesheet" type="text/css" href="../../fonts/font-awesome/css/font-awesome.css">


	<!-- Stylesheet
	================================================== -->
	<link rel="stylesheet" type="text/css"  href="../../css/style.css">
	<link rel="stylesheet" type="text/css" href="../../css/responsive.css">

	<script type="text/javascript" src="../../js/modernizr.custom.js"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>

	<!-- Latest compiled and minified CSS -->


	<!-- jQuery library -->
	<script src="../../js/jquery.1.11.1.js"></script>

	<!-- Latest compiled JavaScript -->
	<script src="../../js/bootstrap.min.js"></script>
	<script src="../../js/main.js"></script>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-66359518-1', 'auto');
		ga('send', 'pageview');

	</script>

	<!--[endif]-->
</head>
<body style="background-color: #f5f5f5">
<!-- Navigation
==========================================-->
<?php include 'navigationcart.php' ?>

<div id="tf-portal" class="text-center">
	<div class="overlay">
		<div class="portal" >
			<?php
			session_start();
			if ( isset( $_SESSION['message'] ) ) {
				$message = $_SESSION['message'];
				echo "<p class='alert-success' style='text-align: center'> $message</p>";
				unset( $_SESSION['message'] );
			}
			if ( isset( $_SESSION['error'] ) ) {
				$errorMessage = $_SESSION['error'];
				echo "<p class='alert-warning' style='text-align: center'> $errorMessage </p>";
				unset( $_SESSION['error'] );
			}

			?>
			<div>
				<h2 class="section-title" style="color: white"> My Cart</h2>
			</div>
			<br>
			<form action="../../assets/php/doCheckOut.php" method="post">
			<table class="table table-hover table-bordered well" style="color : black">
				<thead>
				<th>Course Name</th>
				<th>Difficutly</th>
				<th>Mentor</th>
				<th><i class="fa fa-rupee"></i> </th>
				<th>Action</th>
				</thead>
				<tbody>

				<?php
				require_once("../../assets/php/Token.php");
				require_once("../../assets/php/access/accessTokens.php");
				$csrfToken = new Token( $csrfSecret );
				$loggedInUser = $_SESSION['loggedin_user_id'];

				$sqlSelect = "SELECT course_details.`course_id`, course_details.`course_name`,
					course_details.`course_difficulty`,
					course_details.`course_fees`, user_details.`user_first_name`, user_details.`user_last_name` FROM `course_details`
					 INNER JOIN `course_mentor_map` ON course_details.course_id = course_mentor_map.course_id
					 INNER JOIN `user_details` ON course_mentor_map.mentor_id = user_details.user_id
					 AND course_details.`course_id` IN ( SELECT `course_id` FROM `course_enrollment`
					 WHERE `user_id` = '$loggedInUser' AND `course_enrolled` = FALSE )";

				$result = $conn->query( $sqlSelect );
				if( $result->num_rows > 0 ) {
					while( $row = $result->fetch_assoc() ) {
						echo '
						<tr class="cart-row"> <td>'. $row['course_name']. '</td>
					        <td> '. $row['course_difficulty']. '</td>
					        <td> '. $row['user_first_name']. $row['user_last_name']. '</td>
					        <td> '. $row['course_fees']. '</td>
					        <td> <input type="checkbox"  name="checkout-item[]" value="course-'.$row['course_id'].'" /> &nbsp &nbsp
					        <button type="button" class="btn btn-danger course_remove" id="cart-remove" name='; echo $csrfToken->getCSRFToken(); echo ' value="course-'.$row['course_id'].'" ><i class="fa fa-trash"></i></button></td>
		                                ';
					}
				?>
				</tbody>
				</table>
					<script src='https://www.google.com/recaptcha/api.js'></script>
					<div class='input-group'>
						<div class="g-recaptcha"  data-sitekey="6LcuGAwTAAAAALbkjHwyE3Q9l8vtBDh-rD8P8_aS"></div>
					</div> <br>
					<input type="hidden" name="CSRFToken" value='<?php echo $csrfToken->getCSRFToken(); ?>'/>
					<button type="submit" class="btn btn-success"  > <i class="fa fa-check"></i> Checkout </button>
			</form>
				<?php
				} else { ?>
					</tbody>
					</table>
					<p class='alert-info' style="text-align: center">Looks like your cart is empty!</p>
					<?php
				} ?>
			<div style="text-align: right;">
			<img src="../../img/ccavenue-payment-partner.png" width="120px"/>
			</div>
		</div>
	</div>
</div>
<?php include '../../footer.html' ?>
</body>
</html>