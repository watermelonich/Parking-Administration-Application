<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_duration']))
{
	$data = array();

  	if (empty($_POST['duration_value'])) 
  	{
	    $errors[] = 'Enter Duration Value';
  	}
  	else
  	{
  		$data[] = trim($_POST['duration_value']);
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$data[] = time();

  		/*$query = "
  		INSERT INTO parking_duration (duration_value, duration_created_on) VALUES (?, ?)";*/

  		$query = "
  		INSERT INTO parking_duration (duration_value, duration_created_on) 
  		SELECT * FROM (SELECT ?, ?) AS tmp 
  		WHERE NOT EXISTS (
  			SELECT duration_value FROM parking_duration WHERE duration_value = '".$data[0]."'
		) LIMIT 1
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		if($statement->rowCount() > 0)
  		{
  			$_SESSION['success'] = 'New Parking Duration Data has been Added';

  			header('location:duration.php');
  			
  			exit();
  		}
  		else
  		{
  			$errors[] = 'This Duration Data already exists';
  		}
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Parking Duration</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="duration.php">Parking Duration Management</a></li>
        <li class="breadcrumb-item active">Add Parking Duration</li>
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
				<h5 class="card-title">Add Parking Duration</h5>
			</div>
			<div class="card-body">
				<form method="POST">
					<label class="form-label">Duration Value</label>
				  	<div class="input-group mb-3">				    	
				    	<input type="number" class="form-control" name="duration_value">
				    	<span class="input-group-text">Hour</span>
				  	</div>
				  	<button type="submit" name="add_duration" class="btn btn-primary">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>