<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_user']))
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
  		$data[] = time();

  		$data[] = $_POST['user_id'];

	  	$query = "UPDATE parking_user SET user_email_address = ?, user_password = ?, user_updated_on = ? WHERE user_id = ?";

	  	$statement = $connect->prepare($query);

	  	$statement->execute($data);

  		$_SESSION['success'] = 'User Data has been edited';

  		header('location:users.php?msg=success');
  		exit();
  	}
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the user's details
  	$statement = $connect->prepare("SELECT * FROM parking_user WHERE user_id = ?");

  	$statement->execute([$_GET['id']]);

  	// Fetch the user's details from the database
  	$user = $statement->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Users</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="flats.php">Users Management</a></li>
        <li class="breadcrumb-item active">Edit User Data</li>
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
				<h5 class="card-title">Edit Users Data</h5>
			</div>
			<div class="card-body">
				<form method="post">
				  	<div class="mb-3">
				    	<label for="email">Email address</label>
				    	<input type="email" class="form-control" name="user_email_address" value="<?php echo $user['user_email_address']; ?>">
				  	</div>
				  	<div class="mb-3">
				    	<label for="password">Password</label>
				    	<input type="password" class="form-control" name="user_password" value="<?php echo $user['user_password']; ?>">
				  	</div>
				  	<input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>" />
				  	<button type="submit" name="edit_user" class="btn btn-primary">Edit</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>