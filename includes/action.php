<?php

require_once '../config/config.php';

require_once '../config/function.php';

if(isset($_POST['action']))
{
	if($_POST['action'] == 'fetch_category')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'category_id',
		    'category_name',
		    'category_added_on',
		    'category_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_category';
		$primaryKey = 'category_id';

		// Define the base query
		$query = "SELECT " . implode(", ", $columns) . " FROM $table";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (category_name LIKE '%$search%')";
		}

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY category_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'category_id'			=>	$row['category_id'],
				'category_name'			=>	$row['category_name'],
				'category_added_on'		=>	date(DT_FORMATE, $row['category_added_on']),
				'category_updated_on'	=>	($row['category_updated_on'] > 0) ? date(DT_FORMATE, $row['category_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" => intval($_REQUEST['draw']),
		    "recordsTotal" => intval($count),
		    "recordsFiltered" => intval($countFiltered),
		    "data" => $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'fetch_duration')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'duration_id',
		    'duration_value',
		    'duration_created_on',
		    'duration_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_duration';
		$primaryKey = 'duration_id';

		// Define the base query
		$query = "SELECT " . implode(", ", $columns) . " FROM $table";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (duration_value LIKE '%$search%')";
		}

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		
		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY duration_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'duration_id'			=>	$row['duration_id'],
				'duration_value'		=>	$row['duration_value'] . ' Hour',
				'duration_created_on'	=>	date(DT_FORMATE, $row['duration_created_on']),
				'duration_updated_on'	=>	($row['duration_updated_on'] > 0) ? date(DT_FORMATE, $row['duration_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" => intval($_REQUEST['draw']),
		    "recordsTotal" => intval($count),
		    "recordsFiltered" => intval($countFiltered),
		    "data" => $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'fetch_price')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'parking_price.price_id',
		    'parking_category.category_name',
		    'parking_duration.duration_value',
		    'parking_price.price_value',
		    'parking_price.price_created_on',
		    'parking_price.price_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_price';
		$primaryKey = 'parking_price.price_id';

		// Define the base query
		$query = "
		SELECT " . implode(", ", $columns) . " FROM $table
        JOIN parking_category ON parking_price.category_id = parking_category.category_id 
        JOIN parking_duration ON parking_price.duration_id = parking_duration.duration_id 
		";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (parking_category.category_name LIKE '%$search%' OR parking_duration.duration_value LIKE '%$search%' OR parking_price.price_value LIKE '%$search%')";
		}

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		
		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY parking_price.price_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'price_id'			=>	$row['price_id'],
				'category_name'		=>	$row['category_name'],
				'duration_value'	=>	$row['duration_value'] . ' Hour',
				'price_value'		=>	CURRENCY . ' ' . $row['price_value'],
				'price_created_on'	=>	date(DT_FORMATE, $row['price_created_on']),
				'price_updated_on'	=>	($row['price_updated_on'] > 0) ? date(DT_FORMATE, $row['price_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" 				=> intval($_REQUEST['draw']),
		    "recordsTotal" 		=> intval($count),
		    "recordsFiltered" 	=> intval($countFiltered),
		    "data" 				=> $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'fetch_users')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'user_id',
		    'user_email_address',
		    'user_password',
		    'user_type',
		    'user_created_on',
		    'user_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_user';
		$primaryKey = 'user_id';

		// Define the base query
		$query = "SELECT " . implode(", ", $columns) . " FROM $table";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (user_email_address LIKE '%$search%' OR user_type LIKE '%$search%')";
		}

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		
		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY user_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'user_id'				=>	$row['user_id'],
				'user_email_address'	=>	$row['user_email_address'],
				'user_password'			=>	$row['user_password'],
				'user_type'				=>	$row['user_type'],
				'user_created_on'		=>	date(DT_FORMATE, $row['user_created_on']),
				'user_updated_on'		=>	($row['user_updated_on'] > 0) ? date(DT_FORMATE, $row['user_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" 				=> intval($_REQUEST['draw']),
		    "recordsTotal" 		=> intval($count),
		    "recordsFiltered" 	=> intval($countFiltered),
		    "data" 				=> $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'fetch_slot')
	{
		// Define the columns that should be returned in the response
		$columns = array(
		    'parking_slot.parking_slot_id',
		    'parking_category.category_name',
		    'parking_slot.parking_slot_name',
		    'parking_slot.parking_slot_status',
		    'parking_slot.parking_slot_created_on',
		    'parking_slot.parking_slot_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_slot';
		$primaryKey = 'parking_slot.parking_slot_id';

		// Define the base query
		$query = "
		SELECT " . implode(", ", $columns) . " FROM $table
        JOIN parking_category ON parking_slot.vehicle_category_id = parking_category.category_id 
		";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (parking_category.category_name LIKE '%$search%' OR parking_slot.parking_slot_name LIKE '%$search%' OR parking_slot.parking_slot_status LIKE '%$search%')";
		}

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		
		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY parking_slot.parking_slot_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'parking_slot_id'			=>	$row['parking_slot_id'],
				'category_name'				=>	$row['category_name'],
				'parking_slot_name'			=>	$row['parking_slot_name'],
				'parking_slot_status'		=>	($row['parking_slot_status'] == 'Available') ? "<span class='badge bg-success'>Available</span>" : "<span class='badge bg-danger'>Not Available</span>",
				'parking_slot_created_on'	=>	date(DT_FORMATE, $row['parking_slot_created_on']),
				'parking_slot_updated_on'	=>	($row['parking_slot_updated_on'] > 0) ? date(DT_FORMATE, $row['parking_slot_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" 				=> intval($_REQUEST['draw']),
		    "recordsTotal" 		=> intval($count),
		    "recordsFiltered" 	=> intval($countFiltered),
		    "data" 				=> $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'fetch_customer')
	{
		// Define the columns that should be returned in the response
		/*$columns = array(
		    'parking_customer.customer_id',
		    'parking_customer.customer_name',
		    'parking_customer.customer_contact_no',
		    'category.category_name',
		    'parking_customer.vehicle_number',
		    'parking_slot.parking_slot_name',
		    'parking_customer.vehicle_in_datetime',
		    'parking_customer.vehicle_out_datetime',
		    'parking_duration.duration_value',
		    'parking_customer.parking_charges',
		    'parking_customer.vehicle_status',
		    'parking_user.user_email_address',
		    'parking_customer.customer_created_on',
		    'parking_customer.customer_updated_on'
		);*/

		$columns = array(
		    'parking_customer.customer_id',
		    'parking_customer.customer_name',
		    'parking_customer.customer_contact_no',
		    'parking_customer.vehicle_in_datetime',
		    'parking_customer.vehicle_out_datetime',
		    'parking_duration.duration_value',
		    'parking_customer.parking_charges',
		    'parking_customer.vehicle_status',
		    'parking_user.user_email_address',
		    'parking_customer.customer_created_on',
		    'parking_customer.customer_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_customer';
		$primaryKey = 'parking_customer.customer_id';

		// Define the base query
		$query = "
		SELECT " . implode(", ", $columns) . " FROM $table
        JOIN parking_category ON parking_customer.vehicle_category = parking_category.category_id 
        JOIN parking_slot ON parking_customer.parking_slot_number = parking_slot.parking_slot_id 
        JOIN parking_duration ON parking_customer.total_parking_duration = parking_duration.duration_id 
        JOIN parking_user ON parking_customer.enter_by = parking_user.user_id 
		";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table")->fetchColumn();

		// Define the filter query
		$filterQuery = '';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = " WHERE (parking_customer.customer_name LIKE '%$search%' OR parking_customer.customer_contact_no LIKE '%$search%' OR parking_category.category_name LIKE '%$search%' OR parking_customer.vehicle_number LIKE '%$search%' OR parking_slot.parking_slot_name LIKE '%$search%' OR parking_customer.vehicle_status LIKE '%$search%')";
		}

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];

		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY parking_customer.customer_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'customer_id'				=>	$row['customer_id'],
				'customer_name'				=>	$row['customer_name'],
				'customer_contact_no'		=>	$row['customer_contact_no'],
				/*'category_name'				=>	$row['category_name'],
				'vehicle_number'			=>	$row['vehicle_number'],
				'parking_slot_name'			=>	$row['parking_slot_name'],*/
				'vehicle_in_datetime'		=>	$row['vehicle_in_datetime'],
				'vehicle_out_datetime'		=>	($row['vehicle_out_datetime'] == '0000-00-00 00:00:00') ? 'N/A' : $row['vehicle_out_datetime'],
				/*'duration_value'			=>	$row['duration_value'] . ' Hour',
				'parking_charges'			=>	CURRENCY . $row['parking_charges'],*/
				'vehicle_status'			=>	($row['vehicle_status'] == 'In') ? '<span class="badge bg-success">In</span>' : '<span class="badge bg-danger">Out</span>',
				'user_email_address'		=>	$row['user_email_address'],
				'customer_created_on'		=>	date(DT_FORMATE, $row['customer_created_on']),
				'customer_updated_on'		=>	($row['customer_updated_on'] > 0) ? date(DT_FORMATE, $row['customer_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" 				=> intval($_REQUEST['draw']),
		    "recordsTotal" 		=> intval($count),
		    "recordsFiltered" 	=> intval($countFiltered),
		    "data" 				=> $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'parking_slot_list')
	{
		$data = '<option value="">Select Slot Number</option>';

		$data .= parking_slot_list($connect, $_POST['vehicle_category']);

		echo $data;
	}

	if($_POST['action'] == 'fetch_parking_charges')
	{
		$query = "SELECT price_value FROM parking_price WHERE category_id = '".$_POST['vehicle_category']."' AND duration_id = '".$_POST['total_duration']."'";

		$statement = $connect->prepare($query);

		$statement->execute();

		$result = $statement->fetchAll();

		$price_value = 0;

		foreach($result as $row)
		{
			$price_value = $row['price_value'];
		}

		echo $price_value;
	}

	if($_POST['action'] == 'fetch_single_customer')
	{
		$query = "
		SELECT * FROM parking_customer 
        JOIN parking_category ON parking_customer.vehicle_category = parking_category.category_id 
        JOIN parking_slot ON parking_customer.parking_slot_number = parking_slot.parking_slot_id 
        JOIN parking_duration ON parking_customer.total_parking_duration = parking_duration.duration_id 
        WHERE parking_customer.customer_id = '".$_POST["id"]."'
		";

		$result = $connect->query($query);

		$output = '
		<table class="table table-bordered">';

		foreach($result as $row)
		{
			$out_datetime = '';

			$update_button = '';

			$vehicle_status = ($row['vehicle_status'] == 'In') ? '<span class="badge bg-success">In</span>' : '<span class="badge bg-danger">Out</span>';

			$customer_updated_on = ($row['customer_updated_on'] > 0) ? date(DT_FORMATE, $row['customer_updated_on']) : 'N/A';

			if($row['vehicle_out_datetime'] == '0000-00-00 00:00:00')
			{
				$out_datetime = '<input type="datetime-local" id="out_datetime" name="out_datetime" class="form-control">';

				$update_button = '<tr><td colspan="2" align="center"><button type="button" class="btn btn-warning" id="update_button" data-id="'.$row['customer_id'].'" data-parking_slot_number="'.$row["parking_slot_number"].'">Update</button></td></tr>';
			}
			else
			{
				$out_datetime = $row['vehicle_out_datetime'];
			}

			$output .= '
			<tr>
				<th>ID</th>
				<td>'.$row["customer_id"].'</td>
			</tr>
			<tr>
				<th>Customer Name</th>
				<td>'.$row["customer_name"].'</td>
			</tr>
			<tr>
				<th>Contact No.</th>
				<td>'.$row["customer_contact_no"].'</td>
			</tr>
			<tr>
				<th>Vehicle Type</th>
				<td>'.$row["category_name"].'</td>
			</tr>
			<tr>
				<th>Vehicle No.</th>
				<td>'.$row["vehicle_number"].'</td>
			</tr>
			<tr>
				<th>Slot No.</th>
				<td>'.$row["parking_slot_name"].'</td>
			</tr>
			<tr>
				<th>In DateTime</th>
				<td>'.$row["vehicle_in_datetime"].'</td>
			</tr>
			<tr>
				<th>Out DateTime</th>
				<td>'.$out_datetime.'</td>
			</tr>
			<tr>
				<th>Total Duration</th>
				<td>'.$row['duration_value'] . ' Hour' .'</td>
			</tr>
			<tr>
				<th>Parking Charges</th>
				<td>'.CURRENCY . $row['parking_charges'].'</td>
			</tr>
			<tr>
				<th>Vehicle Status</th>
				<td>'.$vehicle_status.'</td>
			</tr>
			<tr>
				<th>Created At</th>
				<td>'.date(DT_FORMATE, $row['customer_created_on']).'</td>
			</tr>
			<tr>
				<th>Updated On</th>
				<td>'.$customer_updated_on.'</td>
			</tr>
			';

			$output .= $update_button;
		}

		$output .= '
		</table>';

		echo $output;
	}

	if($_POST['action'] == 'update_out_datetime')
	{
		$query = "
		UPDATE parking_customer 
		SET vehicle_out_datetime = '".$_POST["out_datetime"]."', vehicle_status = 'Out', customer_updated_on = '".time()."'  
		WHERE customer_id = '".$_POST["id"]."' 
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		$query = "
		UPDATE parking_slot 
		SET parking_slot_status = 'Available' 
		WHERE parking_slot_id = '".$_POST["parking_slot_number"]."'
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		echo 'done';
	}

	if($_POST['action'] == 'fetch_filter_customer')
	{
		$columns = array(
		    'parking_customer.customer_id',
		    'parking_customer.customer_name',
		    'parking_customer.customer_contact_no',
		    'parking_customer.vehicle_in_datetime',
		    'parking_customer.vehicle_out_datetime',
		    'parking_duration.duration_value',
		    'parking_customer.parking_charges',
		    'parking_customer.vehicle_status',
		    'parking_user.user_email_address',
		    'parking_customer.customer_created_on',
		    'parking_customer.customer_updated_on'
		);

		// Define the table name and the primary key column
		$table = 'parking_customer';
		$primaryKey = 'parking_customer.customer_id';

		// Define the base query
		$query = "
		SELECT " . implode(", ", $columns) . " FROM $table
        JOIN parking_category ON parking_customer.vehicle_category = parking_category.category_id 
        JOIN parking_slot ON parking_customer.parking_slot_number = parking_slot.parking_slot_id 
        JOIN parking_duration ON parking_customer.total_parking_duration = parking_duration.duration_id 
        JOIN parking_user ON parking_customer.enter_by = parking_user.user_id 
		";

		// Get the total number of records
		$count = $connect->query("SELECT COUNT(*) FROM $table WHERE DATE(FROM_UNIXTIME(parking_customer.customer_created_on)) >= '".$_POST["from_date"]."' AND DATE(FROM_UNIXTIME(parking_customer.customer_created_on)) <= '".$_POST["to_date"]."'")->fetchColumn();

		// Define the filter query
		$filterQuery = ' WHERE ';
		if (!empty($_POST['search']['value'])) 
		{
		    $search = $_POST['search']['value'];

		    $filterQuery = "(parking_customer.customer_name LIKE '%$search%' OR parking_customer.customer_contact_no LIKE '%$search%' OR parking_category.category_name LIKE '%$search%' OR parking_customer.vehicle_number LIKE '%$search%' OR parking_slot.parking_slot_name LIKE '%$search%' OR parking_customer.vehicle_status LIKE '%$search%') AND ";
		}

		$filterQuery .= " DATE(FROM_UNIXTIME(parking_customer.customer_created_on)) >= '".$_POST["from_date"]."' AND DATE(FROM_UNIXTIME(parking_customer.customer_created_on)) <= '".$_POST["to_date"]."' ";

		// Add the filter query to the base query
		$query .= $filterQuery;

		// Get the number of filtered records
		$countFiltered = $connect->query($query)->rowCount();

		// Add sorting to the query
		$orderColumn = $columns[$_POST['order'][0]['column']];
		$orderDirection = $_POST['order'][0]['dir'];
		
		if(isset($_POST["order"]))
		{
			$query .= " ORDER BY parking_customer.customer_id DESC";
		}
		else
		{
			$query .= " ORDER BY $orderColumn $orderDirection";
		}

		// Add pagination to the query
		$start = $_POST['start'];
		$length = $_POST['length'];
		$query .= " LIMIT $start, $length";

		// Execute the query and fetch the results
		$statement = $connect->query($query);
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		$data = array();

		foreach($results as $row)
		{
			$data[] = array(
				'customer_id'				=>	$row['customer_id'],
				'customer_name'				=>	$row['customer_name'],
				'customer_contact_no'		=>	$row['customer_contact_no'],
				'vehicle_in_datetime'		=>	$row['vehicle_in_datetime'],
				'vehicle_out_datetime'		=>	($row['vehicle_out_datetime'] == '0000-00-00 00:00:00') ? 'N/A' : $row['vehicle_out_datetime'],
				'vehicle_status'			=>	($row['vehicle_status'] == 'In') ? '<span class="badge bg-success">In</span>' : '<span class="badge bg-danger">Out</span>',
				'user_email_address'		=>	$row['user_email_address'],
				'customer_created_on'		=>	date(DT_FORMATE, $row['customer_created_on']),
				'customer_updated_on'		=>	($row['customer_updated_on'] > 0) ? date(DT_FORMATE, $row['customer_updated_on']) : 'N/A'
			);
		}

		// Build the response
		$response = array(
		    "draw" 				=> intval($_REQUEST['draw']),
		    "recordsTotal" 		=> intval($count),
		    "recordsFiltered" 	=> intval($countFiltered),
		    "data" 				=> $data
		);

		// Convert the response to JSON and output it
		echo json_encode($response);
	}

	if($_POST['action'] == 'fetch_revenue_data')
	{
		$query = "
		SELECT DATE(vehicle_in_datetime) AS date, SUM(parking_charges) as revenue, COUNT(customer_id) as daily_total_vehicle  
		FROM parking_customer 
		WHERE DATE(vehicle_in_datetime) BETWEEN '".$_POST["from_date"]."' AND '".$_POST["to_date"]."' 
		GROUP BY DATE(vehicle_in_datetime) 
		ORDER BY DATE(vehicle_in_datetime) DESC
		";

		$result = $connect->query($query);

		$data = array();

		foreach($result as $row)
		{
			$data[] = array(
				'date'					=>	$row['date'],
				'revenue'				=>	$row['revenue'],
				'total_daily_vehicle'	=>	$row['daily_total_vehicle']
			);
		}

		echo json_encode($data);
	}
}

?>