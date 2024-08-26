<?php

require_once '../../config/config.php';

require_once '../../config/function.php';

if(!isset($_SESSION['user_id']))
{
	header('Location: logout.php');
  	exit();
}

if(isset($_POST['add_customer']))
{
	$data = array();

  	if (empty($_POST['customer_name'])) 
  	{
	    $errors[] = 'Enter Customer Name';
  	}
  	else
  	{
  		$data[] = trim($_POST['customer_name']);
  	}

  	if(empty($_POST['customer_contact_no']))
  	{
  		$errors[] = 'Enter Contact Number';
  	}
  	else
  	{
  		$data[] = trim($_POST['customer_contact_no']);
  	}

  	if(empty($_POST['vehicle_category']))
  	{
  		$errors[] = 'Select Vehicle Category';
  	}
  	else
  	{
  		$data[] = trim($_POST['vehicle_category']);
  	}

  	if(empty($_POST['vehicle_number']))
  	{
  		$errors[] = 'Enter Vehicle Number Detail';
  	}
  	else
  	{
  		$data[] = trim($_POST['vehicle_number']);
  	}

  	if(empty($_POST['parking_slot_number']))
  	{
  		$errors[] = 'Select Parking Slot Number';
  	}
  	else
  	{
  		$data[] = trim($_POST['parking_slot_number']);
  	}

  	if(empty($_POST['in_datetime']))
  	{
  		$errors[] = 'Select Parking In Datetime';
  	}
  	else
  	{
  		$data[] = $_POST['in_datetime'];
  	}

  	if(empty($_POST['total_parking_duration']))
  	{
  		$errors[] = 'Select Parking Duration';
  	}
  	else
  	{
  		$data[] = $_POST['total_parking_duration'];
  	}

  	if(empty($_POST['parking_charges']))
  	{
  		$errors[] = 'Parking Charges required';
  	}
  	else
  	{
  		$data[] = $_POST['parking_charges'];
  	}

  	//Check

  	// If the form data is valid, update the user's password
  	if (empty($errors)) 
  	{
  		$data[] = $_SESSION['user_id'];
  		$data[] = time();

  		$query = "
  		INSERT INTO parking_customer (customer_name, customer_contact_no, vehicle_category, vehicle_number, parking_slot_number, vehicle_in_datetime, total_parking_duration, parking_charges, enter_by, customer_created_on) 
  		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
  		";

  		$statement = $connect->prepare($query);

  		$statement->execute($data);

  		$update_query = "UPDATE parking_slot SET parking_slot_status = 'Not Available' WHERE parking_slot_id = ?";

  		$update_statement = $connect->prepare($update_query);

  		$update_statement->execute([$data[4]]);

  		$_SESSION['success'] = 'New Data Added';

	  	header('location:customer.php?msg=success');
	  	exit();
  	}
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Add Parking Customer</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="customer.php">Parking Customer Management</a></li>
        <li class="breadcrumb-item active">Add Parking Customer</li>
    </ol>
	<div class="col-md-12">
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
				<h5 class="card-title">Add Parking Customer</h5>
			</div>
			<div class="card-body">
				<form method="POST">
					<div class="mb-3 row">
				  		<div class="col-md-6">
					    	<label class="form-label">Customer Name <span class="text-danger">*</span></label>
					    	<input type="text" class="form-control" name="customer_name">
					    </div>
					    <div class="col-md-6">
					    	<label class="form-label">Contact Number <span class="text-danger">*</span></label>
					    	<input type="text" class="form-control" name="customer_contact_no">
					    </div>
				  	</div>
				  	<div class="row mb-3">
				  		<div class="col-md-6">
				  			<label class="form-label">Vehicle Category <span class="text-danger">*</span></label>
				  			<select name="vehicle_category" id="vehicle_category" class="form-control">
				  				<option value="">Select Category</option>
				  				<?php echo category_list($connect); ?>
				  			</select>
				  		</div>
				  		<div class="col-md-6">
					    	<label class="form-label">Vehicle Number <span class="text-danger">*</span></label>
					    	<input type="text" class="form-control" name="vehicle_number">
					    </div>
				  	</div>
				  	<div class="row mb-3">
				  		<div class="col-md-6">
				  			<label class="form-label">Slot Number <span class="text-danger">*</span></label>
				  			<select name="parking_slot_number" id="parking_slot_number" class="form-control">
				  				<option value="">Select Slot</option>
				  			</select>
				  		</div>
				  		<div class="col-md-6">
				  			<label class="form-label">Vehicle In Time <span class="text-danger">*</span></label>
				  			<input type="datetime-local" id="in_datetime" name="in_datetime" class="form-control">
				  		</div>
				  	</div>
				  	<div class="row mb-3">
				  		<div class="col-md-6">
				  			<label class="form-label">Parking Duration <span class="text-danger">*</span></label>
				  			<select name="total_parking_duration" id="total_parking_duration" class="form-control">
				  				<option value="">Select Duration</option>
				  				<?php echo duration_list($connect); ?>
				  			</select>
				  		</div>
				  		<div class="col-md-6">
				  			<label class="form-label">Parking Charges <span class="text-danger">*</span></label>
				  			<div class="input-group">
				  				<span class="input-group-text"><?php echo CURRENCY ?></span>
				  				<input type="number" class="form-control" name="parking_charges" id="parking_charges">
				  			</div>
				  		</div>
				  	</div>
				  	<button type="submit" name="add_customer" class="btn btn-primary">Add</button>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>

<script>

$(document).ready(function(){

	var vehicle_category;
	var total_duration;

	$('#vehicle_category').change(function(){

		vehicle_category = $('#vehicle_category').val();

		if(vehicle_category != '')
		{
			$.ajax({
				url:"action.php",
				method:"POST",
				data:{vehicle_category:vehicle_category, action : 'parking_slot_list'},
				success:function(data)
				{
					$('#parking_slot_number').html(data);
					$('#total_parking_duration').val('');
				}
			});
		}
		else
		{
			alert("Select Vehicle Category");
		}

	});

	$('#total_parking_duration').change(function(){

		total_duration = $('#total_parking_duration').val();

		if(vehicle_category != '' && total_duration != '')
		{
			$.ajax({
				url:"action.php",
				method:"POST",
				data : {vehicle_category : vehicle_category, total_duration : total_duration, action : "fetch_parking_charges"},
				success:function(data)
				{
					$('#parking_charges').val(data);
				}
			});
		}
		else
		{
			if(vehicle_category == '')
			{
				$('#vehicle_category').focus();

				alert("Select Vehicle Category");
			}

			if(total_duration == '')
			{
				$('#total_parking_duration').focus();

				alert("Select Time Duration");
			}
		}

	});

});

</script>

