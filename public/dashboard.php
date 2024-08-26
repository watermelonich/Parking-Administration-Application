<?php

require_once '../config/config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'Master') 
{
  	header('Location: logout.php');
  	exit();
}

include('header.php');

?>


                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>

                    <div class="card">
                    	<div class="card-header">
                    		<div class="row">
                    			<div class="col-md-9">Parking Management System Analytics</div>
                    			<div class="col-md-3">
                    				<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
				    					<i class="fa fa-calendar"></i>&nbsp;
				    					<span></span> <i class="fa fa-caret-down"></i>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-6">
									<div class="card">
										<div class="card-header">
											<div class="row">
												<div class="col col-8">
													<h5 class="card-title">Daily Revenue</h5>
												</div>
												<div class="col col-4">
													<span class="float-end" id="span_total_revenue"></span>
												</div>
											</div>
										</div>
										<div class="card-body">
											<div id="revenue_no_data" style="text-align: center; width: 100%; height: 100%; line-height: 250px;">
												<b>No data available</b>
											</div>
											<canvas id="revenue_chart" style="height: 400px;"> </canvas>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="card">
										<div class="card-header">
											<div class="row">
												<div class="col col-8">
													<h5 class="card-title">Daily Parked Vehicle</h5>
												</div>
												<div class="col col-4">
													<span class="float-end" id="span_total_vehicle"></span>
												</div>
											</div>
										</div>
										<div class="card-body">
											<div id="daily_vehicle_no_data" style="text-align: center; width: 100%; height: 100%; line-height: 250px;">
												<b>No data available</b>
											</div>
											<canvas id="daily_vehicle_chart" style="height: 400px;"> </canvas>
										</div>
									</div>
								</div>
							</div>
						</div>
                    </div>
                </div>

<?php
	include('footer.php');
?>

<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>

$(document).ready(function() {

	var revenue_chart;

	var daily_vehicle_chart;

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
	    $.ajax({
    		url:"action.php",
    		method:"POST",
    		data:{from_date: from_date, to_date : to_date, action : 'fetch_revenue_data'},
    		dataType:"JSON",
    		beforeSend:function(){
    			$('#revenue_no_data').css('display', 'block');
    			$('#revenue_no_data').html('<div class="fa-2x"><i class="fas fa-spinner fa-spin"></i> Wait...</div>');
				$('#revenue_chart').css('display', 'none');
    		},
    		success:function(data)
    		{
				var date_arr = [];
        		var revenue_arr = [];
        		var total_daily_vehicle_arr = [];

        		var total_revenue = 0;
        		var total_vehicle = 0;

        		if(data.length > 0)
        		{
        			$('#revenue_no_data').css('display', 'none');
	    			$('#revenue_no_data').html('<b>No data available</b>');
					$('#revenue_chart').css('display', 'block');

					$('#daily_vehicle_no_data').css('display', 'none');
	    			$('#daily_vehicle_no_data').html('<b>No data available</b>');
					$('#daily_vehicle_chart').css('display', 'block');

					for(var i = 0; i < data.length; i++)
					{
						date_arr.push(data[i].date);
        				revenue_arr.push(parseFloat(data[i].revenue));
        				total_daily_vehicle_arr.push(parseInt(data[i].total_daily_vehicle));

        				total_revenue += parseFloat(data[i].revenue);
        				total_vehicle += parseInt(data[i].total_daily_vehicle);
					}

					$('#span_total_revenue').html('<h5><span class="badge bg-success">Total Revenue : <?php echo html_entity_decode(CURRENCY); ?> '+total_revenue+'</span></h5>');

					$('#span_total_vehicle').html('<h5><span class="badge bg-primary">Total Vehicle : '+total_vehicle+'</span></h5>')

					var revenue_chart_data = {
	                    labels:date_arr,
	                    datasets:[
	                        {
	                            label : 'Revenue in <?php echo html_entity_decode(CURRENCY); ?>',
	                            data:revenue_arr,
	                            backgroundColor : 'rgb(255, 128, 191, 0.30)',
	                            borderWidth : 1,
	                            borderColor : 'rgb(255, 128, 191, 1)'
	                        }
	                    ]   
	                };

	                var revenue_chart_area = document.getElementById('revenue_chart').getContext('2d');

	                if(revenue_chart)
	                {
	                    revenue_chart.destroy();
	                }

	                revenue_chart = new Chart(revenue_chart_area, {
	                    type:'bar',
	                    data:revenue_chart_data,
	                    options: {
	                    	maintainAspectRatio: false,
					        scales: {
					            yAxes: [{
					                ticks: {
					                    beginAtZero: true
					                }
					            }]
					        }
					    }
	                });

	                var total_vehicle_chart_data = {
	                    labels:date_arr,
	                    datasets:[
	                        {
	                            label : 'Daily Parked Vehicle Data',
	                            data:total_daily_vehicle_arr,
	                            backgroundColor : 'rgb(0, 153, 255, 0.30)',
	                            borderWidth : 1,
	                            borderColor : 'rgb(0, 153, 255, 1)'
	                        }
	                    ]   
	                };

	                var daily_vehicle_chart_area = document.getElementById('daily_vehicle_chart').getContext('2d');

	                if(daily_vehicle_chart)
	                {
	                    daily_vehicle_chart.destroy();
	                }

	                daily_vehicle_chart = new Chart(daily_vehicle_chart_area, {
	                    type:'bar',
	                    data:total_vehicle_chart_data,
	                    options: {
	                    	maintainAspectRatio: false,
					        scales: {
					            yAxes: [{
					                ticks: {
					                    beginAtZero: true
					                }
					            }]
					        }
					    }
	                });
        		}
        		else
        		{
					$('#revenue_no_data').css('display', 'block');
	    			$('#revenue_no_data').html('<b>No data available</b>');
					$('#revenue_chart').css('display', 'none');

					$('#daily_vehicle_no_data').css('display', 'block');
	    			$('#daily_vehicle_no_data').html('<b>No data available</b>');
					$('#daily_vehicle_chart').css('display', 'none');
        		}
    		}
    	});
	}

});

</script>