<?php

require_once '../../config/config.php';

if(isset($_GET['action'], $_GET['id']) && $_GET['action'] == 'delete')
{
	$statement = $connect->prepare("DELETE FROM parking_customer WHERE customer_id = ?");
  	
  	$statement->execute([$_GET['id']]);
  	
  	$_SESSION['success'] = 'Customer Data has been removed';
  	
  	header('location:customer.php?msg=success');
}

include('header.php');

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Parking Customer Management</h1>
    <ol class="breadcrumb mb-4">
    	<li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
        <li class="breadcrumb-item active">Parking Customer Management</li>
    </ol>
    <?php
    if(isset($_GET['msg']))
    {
	    if(isset($_SESSION['success']))
		{
			echo '<div class="alert alert-success">'.$_SESSION['success'].'</div>';

			unset($_SESSION['success']);
		}
	}

    ?>
	<div class="card">
		<div class="card-header">
			<div class="row">
				<div class="col col-11">
					<h5 class="card-title">Parking Customer Management</h5>
				</div>
				<div class="col col-1">					
					<div class="float-end"><a href="add_customer.php" class="btn btn-success btn-sm">Add</a></div>
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
							<!--<th>Vehicle Type</th>
							<th>Vehicle No.</th>
							<th>Slot No.</th>!-->
							<th>In DateTime</th>
							<th>Out DateTime</th>
							<!--<th>Total Duration</th>
							<th>Parking Charges</th>!-->
							<th>Vehicle Status</th>
							<th>Enter By</th>
							<th>Created At</th>
							<th>Updated At</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Offcanvas Sidebar -->
<div class="offcanvas offcanvas-start" id="customer_details">
	<div class="offcanvas-header">
    	<h1 class="offcanvas-title">Customer Details</h1>
    	<button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"></button>
  	</div>
  	<div class="offcanvas-body">
    	
  	</div>
</div>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap5.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.2/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css">

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


<?php

include('footer.php');

?>

<script>

$(document).ready(function() {

    $('#customer-table').DataTable({
	    "processing": true,
	    "serverSide": true,
	    "ajax": {
	        url: 'action.php',
	        method:"POST",
	        data: {action : 'fetch_customer'}
	    },
	    "columns": [
	        { "data": "customer_id" },
	        { "data": "customer_name" },
	        { "data": "customer_contact_no" },
	        /*{ "data": "category_name" },
	        { "data": "vehicle_number" },
	        { "data": "parking_slot_name" },*/
	        { "data": "vehicle_in_datetime" },
	        { "data": "vehicle_out_datetime" },
	        /*{ "data": "duration_value" },
	        { "data": "parking_charges" },*/
	        { "data": "vehicle_status" },
	        { "data": "user_email_address" },
	        { "data" : "customer_created_on"},
	        { "data": "customer_updated_on"},
	        {
	        	"data": null,
	        	"render": function(data, type, row) {
	          		return '<button type="button" class="btn btn-sm btn-primary view_btn" data-id="'+row.customer_id+'">View</button>&nbsp;<button type="button" class="btn btn-sm btn-danger delete_btn" data-id="'+row.customer_id+'">Delete</button>';
	        	}
	        }
	    ]
	});

    var myOffcanvas = document.getElementById('customer_details');

    var bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas);

    $(document).on('click', '.view_btn', function(){

    	var id = $(this).data('id');

    	$.ajax({
    		url : "action.php",
    		method:"POST",
    		data : {id:id, action:"fetch_single_customer"},
    		beforeSend:function(){
    			$('.offcanvas-body').html('<div align="center"><div class="spinner-border"></div></div>');
    		},
    		success:function(data)
    		{
    			console.log(data);

    			bsOffcanvas.show();

    			$('.offcanvas-body').html(data);
    		}
    	});

    });

    $(document).on('click', '#update_button', function(){

    	var out_datetime = $('#out_datetime').val();

    	if(out_datetime != '')
    	{
	    	var id = $('#update_button').data('id');

	    	var parking_slot_number = $('#update_button').data('parking_slot_number');

	    	$.ajax({
	    		url:"action.php",
	    		method:"POST",
	    		data : {id : id, out_datetime : out_datetime, parking_slot_number:parking_slot_number, action : 'update_out_datetime'},
	    		success:function(data)
	    		{
	    			bsOffcanvas.hide();

	    			$('#customer-table').DataTable().ajax.reload();
	    		}
	    	});
	    }

    });

    $(document).on('click', '.delete_btn', function(){
    	if(confirm("Are you sure you want to remove this Customer data?"))
    	{
    		window.location.href = 'customer.php?action=delete&id=' + $(this).data('id') + '';
    	}
    });
});

</script>