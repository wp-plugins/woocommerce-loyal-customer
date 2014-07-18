<?php
/* sorting by post */
if(isset($_POST['code_sort_by'])){
	$sort_by = $_POST['code_sort_by'];
}
?>
<div class="wrap">
	<h2>
		WLC
	</h2>
	<?php /* admin notic ... color code information */ ?>
	<table class="widefat">
		<thead>
			<tr>
				<th><div style='background:grey;width:20px;height:20px; border-radius: 1em; float:left;'></div> &nbsp;&nbsp;1 Order</th>
				<th><div style='background:orange;width:20px;height:20px; border-radius: 1em; float:left;'></div> &nbsp;&nbsp;2 - 4 Order</th>
				<th><div style='background:yellow;width:20px;height:20px; border-radius: 1em; float:left;'></div> &nbsp;&nbsp;5 - 8 Order</th>
				<th><div style='background:green;width:20px;height:20px; border-radius: 1em; float:left;'></div> &nbsp;&nbsp;9 - 12 Order</th>
				<th><div style='background:blue;width:20px;height:20px; border-radius: 1em; float:left;'></div> &nbsp;&nbsp;13+ Order</th>
				<th style="background:#615555; padding-right: 0px;">
					<form action="" method="post">
						<input type="search" name="search_cus_ord" style="height: 2em;">
						<input type="submit" name="search_cus_ord_b" value="Search" class="button">
					</form>
				</th>
			</tr>
		</thead>
	</table>
	<table class="widefat">
        <thead>
            <tr>
            	<th>ID</th>
            	<th>Customer Name</th>
                <th>Customer E-mail</th>
                <th>
					<div style="float:left;">Order Count </div>
					<div onClick="document.forms['code_sort'].submit();"  style="cursor: pointer;"><?php if($sort_by == 'acs'){echo '&#9650;';}else{echo '&#9660;';}?></div>
					<form action="" method="post" name="code_sort" id="code_sort">
						<input type="hidden" name="code_sort_by" value="<?php if($sort_by == 'acs'){echo 'desc';}else{echo 'acs';}?>">
					</form>
				</th>
                <th>Order Color</th>
            </tr>
		</thead>
		
		<?php 
		function fused_get_all_user_orders($user_id){
			if(!$user_id)return false;
			
			$user_order = query_posts(
				array(
					'post_type'   => 'shop_order', 
					'meta_key'    => '_customer_user', 
					'meta_value'  => $user_id,
					'posts_per_page' => -1
				)
			);
			/* getting each order of single user..... where order status = completed */
			$c = 0;
			foreach ($user_order as $customer_order) {
				$order = new WC_Order();
				$order->populate($customer_order);
				$orderdata = (array) $order;
				if( $orderdata['status'] == 'completed' ){$c++;}
			}
			/* return counted array */
			return $c;
		}
		
		$arguments = array('post_type' => 'shop_order','meta_key' => '_customer_user','posts_per_page' => -1,);
		$orders = new WP_Query($arguments);
		
		if($orders->have_posts()){
		
			error_reporting(0);
			$ids = array(); 
			
			while($orders->have_posts()){ 
				$orders->the_post();
				$order_id = get_the_ID();
				$order = new WC_Order($order_id);
			 
				$users_id=$order->user_id; 
				$ids[] = $users_id ;
			}
			$ids = array_unique($ids) ;
			$ids = array_values(array_filter($ids));
			
			$users_count = array();
			for($i=0 ; $i<count($ids) ; $i++ ){
				$total = fused_get_all_user_orders($ids[$i]);
				
				if($total > 0){
					$user_info = get_userdata( $ids[$i] );
					$users_count[$ids[$i]]['id'] = $ids[$i];
					$users_count[$ids[$i]]['count'] = $total;
					$users_count[$ids[$i]]['username'] = $user_info->first_name." ".$user_info->last_name;
					$users_count[$ids[$i]]['useremail'] = $user_info->billing_email;
				}
			}
			/////////////// sorting function ///////////////////////////////////////////
			function array_orderby(){
				$args = func_get_args();
				$data = array_shift($args);
				foreach ($args as $n => $field) {
					if (is_string($field)) {
						$tmp = array();
						foreach ($data as $key => $row)
							$tmp[$key] = $row[$field];
						$args[$n] = $tmp;
						}
				}
				$args[] = &$data;
				call_user_func_array('array_multisort', $args);
				return array_pop($args);
			}
			
			/////////////// calling sorting function ///////////////////////////////////////////
			if($sort_by == 'acs'){
				$users_count = array_orderby($users_count, 'count', SORT_ASC, 'id', SORT_ASC);
			}else{
				$users_count = array_orderby($users_count, 'count', SORT_DESC, 'id', SORT_ASC);
			}
			
			//////////////////  displying sorted array  ///////////////
			foreach( $users_count as $user_count){
				if(isset($_POST['search_cus_ord_b'])){
					$pos_code = strpos(trim($user_count['username']), trim($_POST['search_cus_ord']));
					$pos1_code = strpos(trim($user_count['useremail']), trim($_POST['search_cus_ord']));
				}
				if(($pos_code === false) && ($pos1_code === false))continue;
				?>
				<tr>
					<td><?php echo $user_count['id']; ?></td>
					<td><?php echo $user_count['username'];?></td>
					<td><?php echo $user_count['useremail']; ?></td>
					<td><?php echo $user_count['count']; ?></td>
					<td><?php if($user_count['count']==1){echo "<div style='background:grey;width:20px;height:20px; border-radius: 1em;'></div>";} 
						else if($user_count['count']>=2 && $total<=4){echo "<div style='background:orange;width:20px;height:20px; border-radius: 1em;'></div>";}
						else if($user_count['count']>=5 && $total<=8){echo "<div style='background:yellow;width:20px;height:20px; border-radius: 1em;'></div>";}
						else if($user_count['count']>=9 && $total<=12){echo "<div style='background:green;width:20px;height:20px; border-radius: 1em;'></div>";}
						else if($user_count['count']>=13){echo "<div style='background:blue;width:20px;height:20px; border-radius: 1em;'></div>";}?>
					</td>
						 
				</tr>
				<?php 
			}
		}?>
	</table>
</div>
<?php
 /***********************************************************************************************/
?>