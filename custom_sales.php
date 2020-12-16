
add_action('woocommerce_cart_calculate_fees' , 'custom_2nd_item_discount', 10, 1);
function custom_2nd_item_discount( $cart ){
	
	// initialising variable
    $discount = 0;
    $percentage  = 10; // 10 %
	$percentage20 = 20; // 20%
	$lowest_price = 0;
	$middle_price = 0;
	$first_product = true;
	$products_count = $cart->get_cart_contents_count();

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
	 
	//first condition for sale
    if ($products_count >= 2) {

		foreach( $cart->get_cart() as $cart_item ){
			
		//firstly, min price equal to first product price
		if ($first_product) {
			$first_product = false;
			$lowest_price = wc_get_price_excluding_tax($cart_item['data']);
		}
			
		if (wc_get_price_excluding_tax($cart_item['data']) <= $lowest_price) {
			
			
			if ($products_count == 2) {
				$lowest_price = wc_get_price_excluding_tax($cart_item['data']);
			} else { 
				
				//take more than 2 same products
					if ($cart_item['quantity'] >= 2) {
						$lowest_price = wc_get_price_excluding_tax($cart_item['data']);
						$middle_price = $lowest_price;
					} else 
						//different products, and smaller price
						if (wc_get_price_excluding_tax($cart_item['data']) < $lowest_price) {
							$middle_price = $lowest_price;
							$lowest_price = wc_get_price_excluding_tax($cart_item['data']);
					} else 
						//different products with same price
						if (wc_get_price_excluding_tax($cart_item['data']) == $lowest_price) {
							$lowest_price = wc_get_price_excluding_tax($cart_item['data']);
							$middle_price = $lowest_price;
					}
			}
		}
		}
		
			if ($products_count == 2) {
				$discount = $lowest_price * $percentage / 100;
        		$cart->add_fee( sprintf( __("Price for second product %s%%"), $percentage), -$discount );
			} if ($products_count >= 3) { 
				$discount = $lowest_price * $percentage20 / 100 + $middle_price * $percentage / 100 ;
				$cart->add_fee( sprintf( __("Price for second and third products %s%% и %s%%"), $percentage, $percentage20), -$discount );
			}
		
		}  else {
        return;	
	}
}