<?php

include('class/db.php');

$object = new db();

if(!$object->is_login())
{
	header('location:login.php');
}

if(!$object->is_master_user())
{
    header('location:product_purchase.php');
}

include('header.php');



?>

							<div class="container-fluid px-4">
		                        <h1 class="mt-4">Tableau de bord</h1>
		                        <ol class="breadcrumb mb-4">
				                    <li class="breadcrumb-item active">Tableau de bord</li>
				                </ol>
		                        
		                        <div class="row">
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-primary text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->Get_total_no_of_product(); ?></h2>
		                                    	<h5 class="text-center">In Stock Product</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-warning text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->Count_outstock_product(); ?></h2>
		                                    	<h5 class="text-center">Out of Stock Product</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-danger text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->cur_sym . number_format(floatval($object->Get_total_product_purchase()), 2, '.', ','); ?></h2>
		                                    	<h5 class="text-center">Total Purchase</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            <div class="col-xl-3 col-md-6">
		                                <div class="card bg-success text-white mb-4">
		                                    <div class="card-body">
		                                    	<h2 class="text-center"><?php echo $object->cur_sym . number_format(floatval($object->Get_total_product_sale()), 2, '.', ','); ?></h2>
		                                    	<h5 class="text-center">Total Sale</h5>
		                                    </div>
		                                </div>
		                            </div>
		                            
		                        </div>

		                        <div class="row">
		                            <div class="col-xl-12">
		                                <div class="card mb-4">
		                                    <div class="card-header">
		                                    	<div class="row">
		                                    		<div class="col col-md-9">
				                                        <i class="fas fa-chart-area me-1"></i>
				                                        Sale Status
				                                    </div>
				                                    <div class="col col-md-3">
				                                    	<div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
				    										<i class="fa fa-calendar"></i>&nbsp;
				    										<span></span> <i class="fa fa-caret-down"></i>
														</div>
													</div>
												</div>
		                                    </div>
		                                    <div class="card-body"><canvas id="saleChart" width="100%" height="30"></canvas></div>
		                                </div>
		                            </div>
		                        </div>

		                        <div class="card mb-4">
		                            <div class="card-header">
		                                <i class="fas fa-table me-1"></i>
		                                List of Out of Product
		                            </div>
		                            <div class="card-body">

		                            	<table id="out_stock_data" class="table table-bordered table-striped">
	                                        <thead>
	                                            <tr>
	                                                <th>Product Name</th>
	                                                <th>Company</th>
	                                                <th>Available Quantity</th>
	                                                <th>Location Rack</th>
	                                                <th>Status</th>
	                                                <th>Added On</th>
	                                                <th>Updated On</th>
	                                                <th>Action</th>
	                                            </tr>
	                                        </thead>
	                                    </table>
		                            </div>
		                    </div>

<?php

include('footer.php');

?>

<script>

var start = moment().subtract(29, 'days');

var end = moment();

var sale_chart;

function cb(start, end)
{
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));

    fetch_sale_purchase_data(start.format('Y-MM-DD'), end.format('Y-MM-DD'));
}

$('#reportrange').daterangepicker({
    startDate: start,
    endDate: end,
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

function fetch_sale_purchase_data(start_date, end_date)
{
	$.ajax({
		url:"action.php",
		method:"POST",
		data:{start_date:start_date, end_date:end_date, action:'fetch_chart_data'},
		dataType:"JSON",
		success:function(data)
		{
			var total_sale = [];

			var sale_date = [];

			if(data.length > 0)
			{
				for(var i = 0; i < data.length; i++)
				{
					total_sale.push(data[i].sale);

					sale_date.push(data[i].date);
				}

				var chart_data = {
					labels:sale_date,
					datasets:[
					{
						label : 'Sales',
						backgroundColor : 'rgb(35, 183, 229, 0.25)',
						data:total_sale,
						borderWidth: 1,
						borderColor: "#23b7e5",
						pointBackgroundColor: '#23b7e5',
					}
					]
				};

				var group_chart = $('#saleChart');

				if(sale_chart)
				{
					sale_chart.destroy();
				}

				sale_chart = new Chart(group_chart, {
					type:'line',
					data:chart_data,
					options: {
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
		}
	});
}


var dataTable = $('#out_stock_data').DataTable({
	"processing": true,
	"serverSide": true,
	"order": [],
	"ajax":{
		url:"action.php",
		type:"POST",
		data:{action:"fetch_out_stock_product"}
	},
	"columnDefs":[
		{
			"target":[7],
			"orderable":false
		}
	],
	"pageLength": 25
});




</script>