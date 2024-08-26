<?php

require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) 
{
  	header('Location: logout.php');
  	exit();
}
else
{
	// Check if the user ID is set in the query string
	if (isset($_SESSION['user_id'])) 
	{
  		// Retrieve the user ID from the query string
  		$user_id = $_SESSION['user_id'];

  		// Prepare a SELECT statement to retrieve the user's details
  		$statement = $connect->prepare("SELECT * FROM parking_user WHERE user_id = ?");

  		$statement->execute([$user_id]);

  		// Fetch the user's details from the database
  		$user = $statement->fetch(PDO::FETCH_ASSOC);
	}
}

$message = '';

if(isset($_POST['save_button']))
{
	$data = array();
	// Validate name
  	if (empty(trim($_POST['user_email_address']))) 
  	{
    	$errors[] = 'Please Email Address Details';
  	} 
  	else 
  	{
  		if (!filter_var($_POST['user_email_address'], FILTER_VALIDATE_EMAIL))
        {
            $errors[] = 'Please Enter Valid Email Address';
        }
        else
        {
            $data[] = trim($_POST['user_email_address']);
        }
  	}

  	if(empty($_POST['user_password']))
    {
        $errors[] = 'Please Enter your Password Detail';
    }
    else
    {
        $data[] = trim($_POST['user_password']);
    }

  	if(empty($errors))
  	{
  		$data[] = $_POST['user_id'];

  		$query = "
  		UPDATE parking_user 
  		SET user_email_address = ?, user_password = ? 
  		WHERE user_id = ?";

        $statement = $connect->prepare($query);

        $statement->execute($data);
        
        $message = 'Your profile has been updated.';
  	}
}





include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Profile</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Edit Profile</li>
    </ol>
	<div class="col-md-4">
		<?php

		if($message != '')
		{
			echo '<div class="alert alert-success">'.$message.'</div>';
		}

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
				<h5 class="card-title">Edit Profile</h5>
			</div>
			<div class="card-body">
				<form method="post">
					<div class="mb-3">
						<label class="form-label">Email Address</label>
						<input type="email" class="form-control" name="user_email_address" value="<?php echo $user['user_email_address']; ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Password</label>
						<input type="password" class="form-control" name="user_password" value="<?php echo $user['user_password']; ?>">
					</div>
					<input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>" />
					<button type="submit" name="save_button" class="btn btn-primary">Save</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>