<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// If email and/or pass POST values are set, set the variables to those values, otherwise make them false

	// If a file was uploaded
	if(isset($_FILES['picToUpload']))
	{
		$extensions = array('jpg', 'jpeg');	// The allowed file extensions

		$MB = 1048576;	// The maximum allowed size of the file
		//$MB = 1000000;
					
		$fileExtension = explode('.', $_FILES['picToUpload']['name']);	// Getting the file extension of the uploaded file
		$fileExtension = end($fileExtension);
		//echo $_FILES['picToUpload']['size']; // The size of the file being uploaded
			
		
		if(in_array($fileExtension, $extensions) && $_FILES['picToUpload']['size'] < $MB)	// File meets the extension and size requirements
		{
			// Move the file into the 'gallery' directory
			move_uploaded_file($_FILES['picToUpload']['tmp_name'], 'gallery/'.$_FILES['picToUpload']['name']);


			// Getting the current user's user ID from tbusers
			$userQuery = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
			$userResult = $mysqli->query($userQuery);

			$userDetails = mysqli_fetch_array($userResult); 

			$userID = $userDetails['user_id'];				// The current user's user ID
			$fileName = $_FILES['picToUpload']['name'];		// The name of the file that has just been uploaded
				
			
			// Insert the uploaded file into tbgallery
			$galleryQuery = "INSERT INTO tbgallery(user_id, filename) VALUES('$userID', '$fileName')";


			// ERROR CHECKING
			if($mysqli->query($galleryQuery) == TRUE)
			{
				/*echo 
					"<div class='alert alert-success' role='alert'>
						" . $_FILES['picToUpload']['name'] . " successfully uploaded!
					 </div>";*/

			}//end if

			else
			{
				echo	'<div class="alert alert-danger mt-3" role="alert">
							Failed to upload!
						</div>';

			}//end else											 
					
		}//end if

		// ERROR CHECKING
		/*else	// File was not successfully uploaded
		{
			if($_FILES['picToUpload']['size'] == 0)	// No file was uploaded
			{
				echo 
					'<div class="alert alert-danger mt-3" role="alert">
						No file was selected!
					</div>';

			}//end if

			else	// A file was uploaded
			{									
				if(!in_array($fileExtension, $extensions))
				{
					echo 
						'<div class="alert alert-danger mt-3" role="alert">
							File not a jpg/jpeg image!
						</div>';
										
				}//end if

				if($_FILES['picToUpload']['size'] >= $MB)
				{
					echo 
						'<div class="alert alert-danger mt-3" role="alert">
							File larger than 1MB!
						</div>';

				}//end if

			}//end else							
								
		}//else*/
						
	}//end if

?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Madelyn Nestler">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass)	// An email and password were sent
			{
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				
				if($row = mysqli_fetch_array($res))	// The current user's details are in the database
				{
					//echo $email;
					//echo $pass;

					// Display the user's details in the table
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='' method='POST' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='hidden' name='loginEmail' value='$email'/>
									<input type='hidden' name='loginPass' value='$pass'/>
									<input type='submit' class='btn-standard' value='Upload Image' name='submit' />
								</div>
							</form>";

					echo	"<h2>
								Image Gallery
							</h2>";

					//echo	"<div class='row imageGalley'>
					//		</div>";

					
					// Displaying the image gallery from tbgallery

					// Getting the current user's user ID
					$userQuery = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
					$userResult = $mysqli->query($userQuery);
				
					$userDetails = mysqli_fetch_array($userResult);				
					$userID = $userDetails['user_id'];

					//echo $userID;

					// Accessing the rows in the gallery where the user_id is the current user's user ID
					$galleryQuery2 = "SELECT * FROM tbgallery WHERE user_id = '$userID'";
					$galleryResult2 = $mysqli->query($galleryQuery2);
							
					$names = array();			
					$count = 0;			
			
					while($row = mysqli_fetch_array($galleryResult2))
					{
						$names[$count] = $row['filename'];
			
						$count++;
			
					}//end for			
			
					// Displaying the user's files from tbgallery

					echo	"<div class='row imageGallery'>";
					for($i = 0; $i < $count; $i++)
					{
						//echo $names[$i];

						//echo	"<img src='gallery/$names[$i]' width='150' height='100'>";
						echo	"<div class='col-3' style='background-image: url(gallery/$names[$i])'></div>";

					}//end for
					echo	"</div>";

				}//end if
					
				else	//user details are not in database
				{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}//end else

			}//end if 

			else	//invalid login details
			{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}//end else
		?>
	</div>
</body>
</html>