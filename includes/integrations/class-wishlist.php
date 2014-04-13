<?php

class Affiliate_WP_RCP extends Affiliate_WP_Base {
	
	public function init() {

		$this->context = 'rcp';

		add_action( 'init', array( $this, 'add_pending_referral' ), -1 );
		add_action( 'rcp_insert_payment', array( $this, 'mark_referral_complete' ), 10, 3 );
		//add_action( 'rcp_delete_payment', array( $this, 'revoke_referral_on_delete' ), 10 );

		add_filter( 'affwp_referral_reference_column', array( $this, 'reference_link' ), 10, 2 );
	}

	public function add_pending_referral() {

		if( ! function_exists( 'wlm_arrval' ) ) {
			return;
		}

		if( is_admin() || empty( $_POST ) ) {
			return;
		}

		echo '<pre>'; print_r( $_POST ); echo '</pre>';
		//echo '<pre>'; print_r( $_POST ); echo '</pre>'; exit;

		if( $this->was_referred() ) {

			if ( 'WPMRegister' == wlm_arrval( $_POST, 'WishListMemberAction' ) ) {

				/*
				if( $this->get_affiliate_email() == $user->user_email ) {
					return; // Customers cannot refer themselves
				}*/

				$key = get_user_meta( $user_id, 'rcp_subscription_key', true );

				$this->insert_pending_referral( 10, 'oaoisdadu9uasdas', 'Wishlist Test' );
	
			}

		}

	}

	public function mark_referral_complete( $payment_id, $args, $amount ) {

		$this->complete_referral( $args['subscription_key'] );
	
	}

	public function revoke_referral_on_delete( $payment_id = 0 ) {

		if( ! affiliate_wp()->settings->get( 'revoke_on_refund' ) ) {
			return;
		}

		$payments = new RCP_Payments;
		$payment  = $payments->get_payment( $payment_id );
		$this->reject_referral( $payment->subscription_key );

	}

	public function reference_link( $reference = 0, $referral ) {

		if( empty( $referral->context ) || 'rcp' != $referral->context ) {

			return $reference;

		}

		$url = admin_url( 'admin.php?page=rcp-payments&s=' . $reference );

		return '<a href="' . esc_url( $url ) . '">' . $reference . '</a>';
	}
	
}
new Affiliate_WP_RCP;