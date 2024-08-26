<?php

require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master')
{
  	header('Location: logout.php');
  	exit();
}



if(isset($_GET['action'], $_GET['from_date'], $_GET['to_date']))
{
	$action = '';

	if(!empty($_GET['action']))
	{
		$action = $_GET['action'];
	}

	if(!empty($_GET['from_date']))
	{
		$from_date = $_GET['from_date'];
	}

	if(!empty($_GET['to_date']))
	{
		$to_date = $_GET['to_date'];
	}

	if($action == 'export' && $from_date != '' && $to_date != '')
	{
		$query = "
		SELECT * FROM parking_customer 
		JOIN parking_category ON parking_customer.vehicle_category = parking_category.category_id 
        JOIN parking_slot ON parking_customer.parking_slot_number = parking_slot.parking_slot_id 
        JOIN parking_duration ON parking_customer.total_parking_duration = parking_duration.duration_id 
        JOIN parking_user ON parking_customer.enter_by = parking_user.user_id 
        WHERE DATE(FROM_UNIXTIME(parking_customer.customer_created_on)) >= '".$from_date."' 
        AND DATE(FROM_UNIXTIME(parking_customer.customer_created_on)) <= '".$to_date."' 
        ORDER BY parking_customer.customer_id DESC
		";

		$statement = $connect->prepare($query);

		$statement->execute();

		$records = $statement->fetchAll(PDO::FETCH_ASSOC);

		// Output headers
		header('Content-Type: text/csv; charset=utf-8');
		
		header('Content-Disposition: attachment; filename="parking_report_for_'.$from_date.'_to_'.$to_date.'.csv"');

		// Output CSV data
		$output = fopen('php://output', 'w');
		
		fputcsv($output, array('ID', 'Customer Name', 'Contact No.', 'Vehicle Type', 'Vehicle No.', 'Slot No.', 'In DateTime', 'Out DateTime', 'Total Duration', 'Parking Charges', 'Vehicle Status', 'Enter By', 'Created At', 'Updated At'));

		foreach($records as $record)
		{
			$sub_array = array();

			$sub_array[] = $record['customer_id'];
			$sub_array[] = $record['customer_name'];
			$sub_array[] = $record['customer_contact_no'];
			$sub_array[] = $record['category_name'];
			$sub_array[] = $record['vehicle_number'];
			$sub_array[] = $record['parking_slot_name'];
			$sub_array[] = $record['vehicle_in_datetime'];
			$sub_array[] = ($record['vehicle_out_datetime'] == '0000-00-00 00:00:00') ? 'N/A' : $record['vehicle_out_datetime'];
			$sub_array[] = $record['duration_value'] . ' Hour';
			$sub_array[] = $record['parking_charges'];
			$sub_array[] = $record['vehicle_status'];
			$sub_array[] = $record['user_email_address'];
			$sub_array[] = date(DT_FORMATE, $record['customer_created_on']);
			$sub_array[] = ($record['customer_updated_on'] > 0) ? date(DT_FORMATE, $record['customer_updated_on']) : 'N/A';

			fputcsv($output, $sub_array);
		}

		fclose($output);
		exit;
	}
}


include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Report</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Reports</li>
    </ol>

	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col col-8">
					<h5 class="card-title">Report</h5>
				</div>
				<div class="col col-1">
					<div class="float-end"><button type="button" class="btn btn-success btn-sm" id="btn_export">Export to CSV</button></div>
				</div>
				<div class="col col-3">
					<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
    					<i class="fa fa-calendar"></i>&nbsp;
    					<span></span> <i class="fa fa-caret-down"></i>
					</div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-bordered" id="customer-table">
					<thead>
						<tr>
							<td>ID</td>
							<th>Customer Name</th>
							<th>Contact No.</th>
							<th>In DateTime</th>
							<th>Out DateTime</th>
							<th>Vehicle Status</th>
							<th>Enter By</th>
							<th>Created At</th>
							<th>Updated At</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?php

include('footer.php');

?>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>

$(document).ready(function() {

	var start = moment().subtract(29, 'days');

    var end = moment();

	function cb(start, end)
    {
    	load_data(start.format('Y-MM-DD'), end.format('Y-MM-DD'));

    	$('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    }

    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        dateLimit: {
		    "month": 1
		},
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, function(start_range, end_range){

    	start = start_range;

    	end = end_range;

    	cb(start_range, end_range);

    });

    cb(start, end);

    function load_data(from_date, to_date)
    {
	    $('#customer-table').DataTable({
	        "processing": true,
	        "serverSide": true,
	        "ajax": {
	        	url: 'action.php',
	        	method:"POST",
	        	data: {from_date: from_date, to_date : to_date, action : 'fetch_filter_customer'}
	        },
	        "bDestroy": true,
	        "columns": [
	            { "data": "customer_id" },
	            { "data": "customer_name" },
	            { "data": "customer_contact_no" },
	            { "data": "vehicle_in_datetime" },
	            { "data": "vehicle_out_datetime" },
	            { "data": "vehicle_status" },
	            { "data": "user_email_address" },
	            { "data" : "customer_created_on"},
	            { "data": "customer_updated_on"}
	        ]
	    });
	}

	$('#btn_export').click(function(){

		window.location.href = "reports.php?action=export&from_date="+start.format('Y-MM-DD')+"&to_date="+end.format('Y-MM-DD')+"";

	});

});

</script>