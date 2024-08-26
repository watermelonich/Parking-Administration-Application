<?php

require_once '../../config/config.php';

require_once '../../config/function.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_slot']))
{
	$data = array();

	if(empty($_POST['vehicle_category_id']))
	{
		$errors[] = 'Please Select Category';
	}
	else
	{
		$data[] = $_POST['vehicle_category_id'];
	}

  	if (empty($_POST['parking_slot_name'])) 
  	{
	    $errors[] = 'Enter Parking Slot Name';
  	}
  	else
  	{
  		$data[] = trim($_POST['parking_slot_name']);
  	}

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$query = "
  		INSERT INTO parking_slot (vehicle_category_id, parking_slot_name, parking_slot_created_on)
		SELECT * FROM (SELECT ?, ?, ?) AS tmp
		WHERE NOT EXISTS (
		    SELECT * FROM parking_slot WHERE vehicle_category_id = '".$data[0]."' AND parking_slot_name = '".$data[1]."' 
		) LIMIT 1;

  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		if($statement->rowCount() > 0)
  		{
	  		$_SESSION['success'] = 'New Parking Slot Data Added';

	  		header('location:slots.php?msg=success');
	  		exit();
	  	}
	  	else
	  	{
	  		$errors[] = 'Parking Slot Name already Exists with selected category';
	  	}
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Parking Slot</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="slots.php">Parking Slot Management</a></li>
        <li class="breadcrumb-item active">Add Parking Slot</li>
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
				<h5 class="card-title">Add Parking Slot</h5>
			</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3">
				    	<label class="form-label">Category</label>
				    	<select name="vehicle_category_id" class="form-control">
				    		<option value="">Select Category</option>
				    		<?php echo category_list($connect); ?>
				    	</select>
				  	</div>
				  	
				  	<div class="mb-3">
				  		<label class="form-label">Parking Slot Name</label>
				    	<input type="text" class="form-control" name="parking_slot_name">
				  	</div>
				  	<button type="submit" name="add_slot" class="btn btn-primary">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>