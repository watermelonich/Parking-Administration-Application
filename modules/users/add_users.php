<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_user']))
{
	// Validate the form data
  	$data = array();

  	if (empty($_POST['user_email_address']))
  	{
	    $errors[] = 'Please Enter Email Address';
  	}
  	else
  	{
  		if (!filter_var($_POST['user_email_address'], FILTER_VALIDATE_EMAIL)) 
  		{
  			$errors[] = 'Please enter a valid email address';
  		}
  		else
  		{
  			$data[] = trim($_POST['user_email_address']);
  		}
  	}

  	if (empty($_POST['user_password'])) 
  	{
    	$errors[] = 'Please enter your Password';
  	}
  	else
  	{
  		$data[] = trim($_POST['user_password']);
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		// Insert user data into the database

  		$data[] = 'User';

  		$data[] = time();

  		$query = "
  		INSERT INTO parking_user (user_email_address, user_password, user_type, user_created_on) 
  		SELECT * FROM (SELECT ?, ?, ?, ?) AS tmp 
  		WHERE NOT EXISTS (
  			SELECT * FROM parking_user WHERE user_email_address = '".$data[0]."'
  		) LIMIT 1
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		if($statement->rowCount() > 0)
  		{
  			$_SESSION['success'] = 'New User Data Added';

  			header('location:users.php?msg=success');
  			
  			exit();
  		}
  		else
  		{
  			$errors[] = 'User Email Already Exists';
  		}
  	}
}



include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Users</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="users.php">Users Management</a></li>
        <li class="breadcrumb-item active">Add User</li>
    </ol>
	<div class="col-md-4">
		<?php

		if(isset($errors))
        {
            foreach ($errors as $error) 
            {
                echo "<div class='alert alert-danger'>$error</div>";
            }
        }

		?>
		<div class="card">
			<div class="card-header">
				<h5 class="card-title">Add User</h5>
			</div>
			<div class="card-body">
				<form method="post">
				  	<div class="mb-3">
				    	<label for="email">Email address</label>
				    	<input type="email" class="form-control" name="user_email_address">
				  	</div>
				  	<div class="mb-3">
				    	<label for="password">Password</label>
				    	<input type="password" class="form-control" name="user_password">
				  	</div>
				  	<button type="submit" name="add_user" class="btn btn-primary">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>