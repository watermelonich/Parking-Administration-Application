<?php

require_once '../config/config.php';
require_once '../config/function.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master') 
{
  	header('Location: logout.php');
  	exit();
}

$message = '';

if(isset($_POST['save_button']))
{
	$data = array();

	// Validate name
  	if (empty(trim($_POST['parking_name']))) 
  	{
    	$errors[] = 'Please enter Parking name.';
  	} 
  	else 
  	{
    	$data[] = trim($_POST['parking_name']);
  	}

  	// Validate email
  	if (empty(trim($_POST['parking_contact_person'])))
  	{
    	$errors[] = 'Please enter Contact Person Details';
  	} 
  	else 
  	{
    	$data[] = trim($_POST['parking_contact_person']);
  	}

  	if (empty(trim($_POST['parking_contact_number'])))
  	{
    	$errors[] = 'Please enter Contact Number Details';
  	} 
  	else 
  	{
    	$data[] = trim($_POST['parking_contact_number']);
  	}

  	if (empty(trim($_POST['parking_timezone'])))
  	{
    	$errors[] = 'Please Select Timezone';
  	} 
  	else 
  	{
    	$data[] = trim($_POST['parking_timezone']);
  	}

  	if (empty(trim($_POST['parking_currency'])))
  	{
    	$errors[] = 'Please Select Currency';
  	} 
  	else 
  	{
    	$data[] = trim($_POST['parking_currency']);
  	}

  	if (empty(trim($_POST['parking_datetime_format'])))
  	{
    	$errors[] = 'Please Select Date & Time Format';
  	} 
  	else 
  	{
    	$data[] = trim($_POST['parking_datetime_format']);
  	}

  	if(empty($errors))
  	{
  		$data[] = time();
  		$data[] = $_POST["parking_id"];
  		$query = "
  		UPDATE parking_setting 
  		SET parking_name = ?, 
  		parking_contact_person = ?, 
  		parking_contact_number = ?, 
  		parking_timezone = ?, 
  		parking_currency = ?, 
  		parking_datetime_format = ?, 
  		parking_updated_on = ? 
  		WHERE parking_id = ?
  		";
        $statement = $connect->prepare($query);

        $statement->execute($data);
        
        // redirect the user to the profile page with a success message
        $message = 'Your profile has been updated.';
  	}
}

$query = "SELECT * FROM parking_setting ORDER BY parking_id DESC LIMIT 1";

$statement = $connect->prepare($query);

$statement->execute();

$data = $statement->fetch();

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Setting</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Edit Data</li>
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
				<h5 class="card-title">Edit Setting Data</h5>
			</div>
			<div class="card-body">
				<form method="post">
					<div class="mb-3">
						<label class="form-label">Parking Name</label>
						<input type="text" class="form-control" name="parking_name" value="<?php echo $data['parking_name']; ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Contact Person Name</label>
						<input type="text" class="form-control" name="parking_contact_person" value="<?php echo $data['parking_contact_person']; ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Contact Number</label>
						<input type="number" class="form-control" name="parking_contact_number" value="<?php echo $data['parking_contact_number']; ?>">
					</div>
					<div class="mb-3">
						<label class="form-label">Set Timezone</label>
						<select name="parking_timezone" id="parking_timezone" class="form-control">
							<option value="">Select Timezone</option>
							<?php echo Timezone_list(); ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label">Set Currency</label>
						<select name="parking_currency" id="parking_currency" class="form-control">
							<option value="">Select Currency</option>
							<?php echo Currency_list(); ?>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label">Set Datetime Format</label>
						<select name="parking_datetime_format" id="parking_datetime_format" class="form-control">
							<option value="">Select Datetime Format</option>
							<option value="d/m/Y H:i:s">d/m/Y H:i:s</option>
							<option value="m/d/Y H:i:s">m/d/Y H:i:s</option>
							<option value="m/d/Y H:i">m/d/Y H:i</option>
							<option value="d/m/Y H:i">d/m/Y H:i</option>
							<option value="Y-m-d H:i">Y-m-d H:i</option>
							<option value="Y-m-d H:i:s">Y-m-d H:i:s</option>
						</select>
					</div>
					<div class="mb-3" align="center">
						<input type="hidden" name="parking_id" value="<?php echo $data['parking_id']; ?>" />
						<button type="submit" name="save_button" class="btn btn-primary">Save</button>
					</div>
					<script>
						$('#parking_timezone').val('<?php echo $data["parking_timezone"]; ?>');
						$('#parking_currency').val('<?php echo $data["parking_currency"]; ?>');
						$('#parking_datetime_format').val('<?php echo $data["parking_datetime_format"]; ?>');
					</script>
				</form>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>