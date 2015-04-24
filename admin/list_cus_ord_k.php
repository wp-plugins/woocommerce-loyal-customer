<?php
/* sorting by post */
//ini_set('memory_limit', '-1');

//print_r($_POST);
/*if(isset($_POST["code_sort_by"]))
   echo "Form 1 have been submitted";
else if(isset($_POST["id_sort_by"]))
   echo "Form 2 have been submitted";*/
if(isset($_POST['code_sort_by'])){
	$code_sort_by = $_POST['code_sort_by'];
}

if(isset($_POST['id_sort_by'])){
	$id_sort_by = $_POST['id_sort_by'];
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
            	<th>
                	<div style="float:left;">Customer ID</div>
					<div onClick="document.forms['id_sort'].submit();"  style="cursor: pointer;"><?php if($id_sort_by == 'acs'){echo '&#9650;';}else{echo '&#9660;';}?></div>
					<form action="" method="post" name="id_sort" id="id_sort">
						<input type="hidden" name="id_sort_by" value="<?php if($id_sort_by == 'acs'){echo 'desc';}else{echo 'acs';}?>">
					</form>
                </th>
            	<th>Customer Name</th>
                <th>Customer E-mail</th>
                <th>
					<div style="float:left;">Order Count </div>
					<div onClick="document.forms['code_sort'].submit();"  style="cursor: pointer;"><?php if($code_sort_by == 'acs'){echo '&#9650;';}else{echo '&#9660;';}?></div>
					<form action="" method="post" name="code_sort" id="code_sort">
						<input type="hidden" name="code_sort_by" value="<?php if($code_sort_by == 'acs'){echo 'desc';}else{echo 'acs';}?>">
					</form>
				</th>
                <th>Order Color</th>
            </tr>
		</thead>
		
        
        
        
        
 <?php        
function woocommerce_version_check(  ) {
	
  // If get_plugins() isn't available, require it
	if ( ! function_exists( 'get_plugins' ) )
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        // Create the plugins folder and file variables
	$plugin_folder = get_plugins( '/' . 'woocommerce' );
	$plugin_file = 'woocommerce.php';
	
	// If the plugin version number is set, return it 
	if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
		return (float) $plugin_folder[$plugin_file]['Version'];

	} else {
	// Otherwise return null
		return NULL;
	}
}

 ?>       
 
   
        
		<?php 
		
    
        function fused_get_all_user_orders($user_id){
			if(!$user_id)return false;
			
			if( woocommerce_version_check() < 2.2 ) {
					
				
				
			$user_order = query_posts(
				array(
					'post_type'   => 'shop_order', 
					'meta_key'    => '_customer_user', 
					'meta_value'  => $user_id,
					'posts_per_page' => -1
				)
			);
			 //getting each order of single user..... where order status = completed 
			$c = 0;
			foreach ($user_order as $customer_order) {
				$order = new WC_Order();
				$order->populate($customer_order);
				$orderdata = (array) $order;
				if( $orderdata['status'] == 'completed' ){$c++;}
			}
			 //return counted array 
			return $c;
			}
			else
			
			{
				
				
				$arguments = array('post_type' => 'shop_order','meta_key' => '_customer_user','meta_value'  => $user_id,'posts_per_page' => -1);
				global $post;
				$user_order= get_posts( $arguments ); 
			$c = 0;
			foreach ($user_order as $customer_order) {
				
				
				$order = new WC_Order();
				$order->populate($customer_order);
				
				$orderdata = (array) $order;
				
				if( $orderdata['post_status'] == 'wc-completed' ){$c++;}
			}
			return $c;
			}
			
		}
		
		if( woocommerce_version_check() < 2.2 ) {
			
			
			
			
		$arguments = array('post_type' => 'shop_order','meta_key' => '_customer_user','posts_per_page' => -1);
		$orders = new WP_Query($arguments);
		
	
		
		if($orders->have_posts()){
		
			error_reporting(0);
			$ids = array(); 
			
			while($orders->have_posts()){ 
				$orders->the_post();
				$order_id = get_the_ID();
				$order = new WC_Order($order_id);
			// print_r($order);
				$users_id=$order->user_id; 
				$ids[] = $users_id ;
			}
			$ids = array_unique($ids) ;
			$ids = array_values(array_filter($ids));
			
			if($id_sort_by=="acs"){
			sort($ids);
			}
			if($id_sort_by=="desc"){
			rsort($ids);	
			}
			
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
    }
		}
		else
    	{			
        /*************************************************************************************************************************/
		
		
		$arguments = array('post_type' => 'shop_order','meta_key' => '_customer_user','posts_per_page' => -1);
		global $post;
	$posts_array = get_posts( $arguments ); 
	
	
	//echo "<pre>";print_r($posts_array );
		if($posts_array){
	
			error_reporting(0);
			$ids = array(); 
						
			foreach( $posts_array as $post ) {
				
				 setup_postdata($post);
				 $order_id =  $post->ID;
				$order = new WC_Order($order_id);
			 $users_id=$order->get_user_id(); 
				$ids[] = $users_id ;
				
			}
			wp_reset_postdata();
			$ids = array_unique($ids) ;
			$ids = array_values(array_filter($ids));
			
		
		
			if($id_sort_by=="acs"){
			sort($ids);
			}
			if($id_sort_by=="desc"){
			rsort($ids);	
			}
			
			//print_r($ids);
			
			
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
        
        }
	}
	
	
////// sort by order count ////////
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
			
	/*		/////////////// calling sorting function ///////////////////////////////////////////
		if($code_sort_by == 'acs'){
				$users_count = array_orderby($users_count, 'count', SORT_ASC, 'id', SORT_ASC);
			}else{
				$users_count = array_orderby($users_count, 'count', SORT_DESC, 'id', SORT_ASC);
			}
	*/
//print_r($users_count);

if($code_sort_by == 'acs'){
				$users_count = array_orderby($users_count, 'count', SORT_ASC, 'id', SORT_ASC);
			}
if( woocommerce_version_check() < 2.2  ){			
if($code_sort_by == 'desc'){
				$users_count = array_orderby($users_count, 'count', SORT_DESC, 'id', SORT_ASC);
			}			
			
	}
	
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
						else if($user_count['count']>=2 && $user_count['count']<=4){echo "<div style='background:orange;width:20px;height:20px; border-radius: 1em;'></div>";}
						else if($user_count['count']>=5 && $user_count['count']<=8){ echo "<div style='background:yellow;width:20px;height:20px; border-radius: 1em;'></div>";}
						else if($user_count['count']>=9 && $user_count['count']<=12){echo "<div style='background:green;width:20px;height:20px; border-radius: 1em;'></div>";}
						else if($user_count['count']>=13){echo "<div style='background:blue;width:20px;height:20px; border-radius: 1em;'></div>";}?>
					</td>
						 
				</tr>
				<?php 
			}	
	
	
	
			
			
		?>
        
        
        
        
        
	</table>
</div>
<?php
 /***********************************************************************************************/
?>