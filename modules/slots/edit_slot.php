<?php

require_once '../../config/config.php';

require_once '../../config/function.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}

if(isset($_POST['edit_slot']))
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
	    $errors[] = 'Enter Slot Name Data';
  	}
  	else
  	{
  		$data[] = trim($_POST['parking_slot_name']);
  	}

  	if (empty($errors)) 
  	{
  		$data[] = time();

  		$data[] = $_POST['parking_slot_id'];

  		$query = "
  		UPDATE parking_slot 
  		SET vehicle_category_id = ?, parking_slot_name = ?, parking_slot_updated_on = ?  
  		WHERE parking_slot_id = ?
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		$_SESSION['success'] = 'Parking Slot Data has been Changed';

  		header('location:slots.php?msg=success');
  	}
}

if(isset($_GET['id']))
{
	// Prepare a SELECT statement to retrieve the vehical parking_slot's details
  	$statement = $connect->prepare("SELECT * FROM parking_slot WHERE parking_slot_id = ?");

  	$statement->execute([$_GET['id']]);

  	// Fetch the user's details from the database
  	$slot = $statement->fetch(PDO::FETCH_ASSOC);
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Edit Parking Slot</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="slots.php">Parking Slot Management</a></li>
        <li class="breadcrumb-item active">Edit Parking Slot</li>
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
				<h5 class="card-title">Edit Parking Slot</h5>
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
				    	<input type="text" class="form-control" name="parking_slot_name" value="<?php echo $slot['parking_slot_name']; ?>">
				  	</div>
				  	<input type="hidden" name="parking_slot_id" value="<?php echo $slot['parking_slot_id']; ?>" />
				  	<button type="submit" name="edit_slot" class="btn btn-primary">Edit Slot</button>
				</form>
				<script>

					$("select[name='vehicle_category_id']").val("<?php echo $slot['vehicle_category_id']; ?>");

				</script>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>