<?php

require_once '../../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_duration']))
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

  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$data[] = $_POST['duration_id'];

  		$query = "
  		UPDATE parking_duration 
  		SET duration_value = ?, duration_updated_on = ? 
  		WHERE duration_id = ?
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		$_SESSION['success'] = 'Duration Data has been Changed';

  		header('location:duration.php');
  	}
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the vehical category's details
  	$statement = $connect->prepare("SELECT * FROM parking_duration WHERE duration_id = ?");

  	$statement->execute([$_GET['id']]);

  	// Fetch the user's details from the database
  	$duration_data = $statement->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Parking Duration</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="flats.php">Parking Duration Management</a></li>
        <li class="breadcrumb-item active">Edit Parking Duration</li>
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
				<h5 class="card-title">Edit Parking Duration</h5>
			</div>
			<div class="card-body">
				<form method="POST">
				  	<div class="mb-3">
				    	<label class="form-label">Duration Value</label>
				    	<div class="input-group mb-3">
				    		<input type="text" class="form-control" name="duration_value" value="<?php echo $duration_data['duration_value']; ?>">
				    		<span class="input-group-text">Hour</span>
				    	</div>
				  	</div>
				  	<input type="hidden" name="duration_id" value="<?php echo $duration_data['duration_id']; ?>" />
				  	<button type="submit" name="edit_duration" class="btn btn-primary">Edit Duration</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>