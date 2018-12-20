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
	
	function numberFormat($number, $decimals = 2, $sep = ".", $k = ","){
		return number_format($number/100, $decimals, $sep, $k); // Format the number
	}
	
	$conn = new dbconnection('localhost', 'root', '', 'nwind');
	$orders = new dbmodel($conn);
	$orders->idcol = 'OrderID';
	$orders->table = 'orders';
	$orders->order_by = 'OrderID DESC';
	
	$order_details = new dbmodel($conn);
	$order_details->idcol = '';
	$order_details->table = 'order_details';

	$order_summary = array();
	
	foreach($order_details->get_all() as $od){
		
		$od['UnitPrice'] = ceil($od['UnitPrice'] * 100);
		
		if(!isset( $order_summary[$od['OrderID']]['TotalItems'] )) {
			$order_summary[$od['OrderID']]['TotalItems'] = 1;
		} else {
			$order_summary[$od['OrderID']]['TotalItems']++;
		}
		
		if(!isset( $order_summary[$od['OrderID']]['TotalAmount'] )) {
			$order_summary[$od['OrderID']]['TotalAmount'] = $od['UnitPrice'] * $od['Quantity'];
		} else {
			$order_summary[$od['OrderID']]['TotalAmount'] += ($od['UnitPrice'] * $od['Quantity']);			
		}		
		
		if(!isset( $order_summary[$od['OrderID']]['TotalDiscount'] )) {
			$order_summary[$od['OrderID']]['TotalDiscount'] = $od['UnitPrice'] * $od['Quantity'] * $od['Discount'];
		} else {
			$order_summary[$od['OrderID']]['TotalDiscount'] += ($od['UnitPrice'] * $od['Quantity'] * $od['Discount']);			
		}
						
		if(!isset( $order_summary[$od['OrderID']]['Discounted'] )) {
			$order_summary[$od['OrderID']]['Discounted'] = $od['UnitPrice'] * $od['Quantity'] * (1-$od['Discount']);
		} else {
			$order_summary[$od['OrderID']]['Discounted'] += ($od['UnitPrice'] * $od['Quantity'] * (1-$od['Discount']));			
		}		
	}
	
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
		<p>&nbsp;</p>
		<h2>Orders Summary (PHP Processing)</h2>
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
					$oid = $o['OrderID']; 
					$o['Freight'] = floor($o['Freight'] * 100);
				?>
				<tr>
					<td align="right"><?php echo $counter;?></td>
					<td><?php echo $oid;?></td>
					<td><?php echo $o['OrderDate'];?></td>
					<td><?php echo $o['CustomerID'];?></td>
					<td align="right"><?php echo numberFormat($o['Freight']);?></td>
					<td align="center"><?php echo $order_summary[$oid]['TotalItems'];?></td>
					<td align="right"><?php echo numberFormat($order_summary[$oid]['TotalAmount']);?></td>
					<td align="right"><?php echo numberFormat($order_summary[$oid]['TotalDiscount']);?></td>
					<td align="right"><?php echo numberFormat($order_summary[$oid]['TotalAmount'] - $order_summary[$oid]['TotalDiscount']);?></td>
					<td align="right"><?php echo numberFormat($o['Freight'] + $order_summary[$oid]['Discounted']);?></td>
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