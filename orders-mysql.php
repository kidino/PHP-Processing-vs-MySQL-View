<?php
$started_at = microtime(true);
?><html>
<head>
	<title>ORDERS</title>
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
	<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!------ Include the above in your HEAD tag ---------->

	<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
	<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
	<!------ Include the above in your HEAD tag ---------->

</head>
<body>
<?php
	include('db.php');
	
	$conn = new dbconnection('localhost', 'root', '', 'nwind');
	$orders = new dbmodel($conn);
	$orders->idcol = 'OrderID';
	
	// 'order_summary_opt' or 'order_summary'
	// order_summary_opt produces better result by eliminating
	// multiple sub-selects
	$orders->table = 'order_summary_opt'; 
	
	$orders->order_by = 'OrderID DESC';
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
		<p>&nbsp;</p>
		<h2>Orders Summary (MySQL View)</h2>
		<table class="table table-striped">
			<thead>
				<tr>
					<th> </th>
					<th>Order ID</th>
					<th>Order Date</th>
					<th>Customer ID</th>
					<th class="text-right">Shipping</th>
					<th class="text-center">Total Items</th>
					<th class="text-right">Total Amount</th>
					<th class="text-right">Total Discount</th>
					<th class="text-right">Discounted</th>
					<th class="text-right">Grand Total</th>
				</tr>
			</thead>
			<tbody>
			<?php 
				$counter = 0;
				foreach($orders->get_all() as $o) { 
					$counter++;
					$oid = $o['OrderID']; ?>
				<tr>
					<td align="right"><?php echo $counter;?></td>
					<td><?php echo $oid;?></td>
					<td><?php echo $o['OrderDate'];?></td>
					<td><?php echo $o['CustomerID'];?></td>
					<td align="right"><?php echo $o['Shipping'];?></td>
					<td align="center"><?php echo $o['TotalItems'];?></td>
					<td align="right"><?php echo $o['TotalAmount'];?></td>
					<td align="right"><?php echo $o['TotalDiscount'];?></td>
					<td align="right"><?php echo $o['DiscountedAmount'];?></td>
					<td align="right"><?php echo $o['GrandTotal'];?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
		
		</div>
	</div>
</div>
</body>
</html>
<?php
echo '<p align="right">Cool, that only took ' . (microtime(true) - $started_at) . ' seconds!</p>';
?>