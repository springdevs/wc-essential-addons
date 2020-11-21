<?php

namespace springdevs\WooAdvanceCoupon\Admin;

/**
 * MetaBoxes Class for coupon
 */
class MetaBoxes
{

	function __construct()
	{
		add_action('add_meta_boxes', array($this, "coupon_metaboxes"));
	}

	/**
	 * create Meta Box
	 *
	 * @uses add_meta_box
	 **/
	public function coupon_metaboxes()
	{
		// sdwac_coupon Discount
		add_meta_box(
			'sdwac_coupon_discount_box',
			'Rules for cart total',
			[$this, 'coupon_discount_screen'],
			'shop_coupon',
			'normal',
			'default'
		);

		// sdwac_coupon Rules
		add_meta_box(
			'sdwac_coupon_rules_box',
			'Advanced Rules (optional)',
			[$this, 'coupon_rules_screen'],
			'shop_coupon',
			'normal',
			'default'
		);
	}

	/**
	 * Screen of Discount Box
	 */
	public function coupon_discount_screen()
	{
?>
		<superdiscount />
	<?php
	}

	/**
	 * Screen of Rules Box
	 */
	public function coupon_rules_screen()
	{
	?>
		<superrules />
<?php
	}
}
