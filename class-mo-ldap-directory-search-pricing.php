<?php
/**
 * This file contains the class with the pricing model.
 *
 * @package miniOrange_LDAP_Directory_Search
 * @subpackage Main
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'MO_LDAP_Directory_Search_Pricing' ) ) {

	/**
	 * MO_LDAP_Directory_Search_Pricing Contains pricing.
	 */
	class MO_LDAP_Directory_Search_Pricing {
		/**
		 * Var premium_plan_pricing
		 *
		 * @var array
		 */
		public $premium_plan_pricing;
		/**
		 * __construct
		 *
		 * @return void
		 */
		public function __construct() {
			$this->premium_plan_pricing = array(
				'1'         => '249',
				'2'         => '448',
				'3'         => '634',
				'4'         => '808',
				'5'         => '970',
				'6'         => '1,127',
				'7'         => '1,289',
				'8'         => '1,421',
				'9'         => '1,540',
				'10'        => '1,645',
				'11'        => '1,776',
				'12'        => '1,881',
				'13'        => '1,919',
				'14'        => '2,017',
				'15'        => '2,075',
				'16'        => '2,129',
				'17'        => '2,175',
				'18'        => '2,215',
				'19'        => '2,249',
				'20'        => '2,278',
				'30'        => '3,087',
				'40'        => '3,847',
				'50'        => '4,258',
				'100'       => '4,695',
				'UNLIMITED' => '4,999',
			);
		}
	}
}
