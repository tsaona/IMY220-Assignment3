<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "", "dbUser");

	$email = isset($_POST["email"]) ? $_POST["email"] : false;
	$pass = isset($_POST["pass"]) ? $_POST["pass"] : false;	
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 3</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Otshepeng Morake">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php

			$email = isset($_POST["user_email"]) ? $_POST["user_email"] : $email;
			$pass = isset($_POST["user_pass"]) ? $_POST["user_pass"] : $pass;
			$usr_id =0;

			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
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
				
					echo 	"<form action='' method='post' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload' id='picToUpload' /><br/>
									<input type='hidden' name='user_email' id='user_email' value='$email'/>
									<input type='hidden' name='user_pass' id='user_pass' value='$pass'/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";

					if(isset($_FILES["picToUpload"])){
						
						$target_dir = "gallery/"; 
						$uploadFile = $_FILES["picToUpload"]; 
						$target_file = $target_dir . basename($uploadFile["name"]); 
						$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);	
					
						$usr_id = $row['user_id'];
						$uploadOk = false;

						if(isset($_POST["submit"]) && $uploadFile["size"] > 0){ 
							$check = getimagesize($uploadFile["tmp_name"]); 
							if($check !== false){ 
								$uploadOk = true; 
							}
						}

						if(($uploadOk = true) && ($uploadFile["type"] == "image/jpeg") && ($uploadFile["size"] < 1000000)){
							if($uploadFile["error"] <= 0){

								if(file_exists($target_dir . $uploadFile["name"])){

									$query= "SELECT filename FROM tbgallery WHERE user_id=$usr_id";

									if($records = $mysqli->query($query)){

										$uploadagain = true;
										
										while($entry= $records->fetch_array()){
											if ($entry['filename'] == $uploadFile["name"])
												$uploadagain = false;
										}
										
										if($uploadagain === true){

											$filename = $uploadFile["name"];
											$insert="INSERT INTO tbgallery(user_id, filename) VALUES ('$usr_id','$filename')";

											move_uploaded_file($uploadFile["tmp_name"], $target_dir . $filename);

											if ($mysqli->query($insert) === true){}
										}
									}
								}
								else{ 

									$filename = $uploadFile["name"];
									$insert="INSERT INTO tbgallery(user_id, filename) VALUES ('$usr_id','$filename')";

									move_uploaded_file($uploadFile["tmp_name"], $target_dir . $filename);

									if ($mysqli->query($insert) === true){}
								}
							}  
						}
					}

					//show gallery
					echo "<h3>Image Gallery</h3>
						  <div class='row imageGallery mb-2'>";

						$savier = $row['user_id'];
						$query = "SELECT * FROM tbgallery WHERE user_id=$savier";

						if($records = $mysqli->query($query)){

							if($records->num_rows > 0){

								while($entry= $records->fetch_array()){
									echo "<div class='col-3' style='background-image: url(gallery/".$entry['filename'].")'> </div>";
								}
							}
						}

					echo "</div>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>