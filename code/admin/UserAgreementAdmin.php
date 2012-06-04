<?php
/**
 * ModelAdmin for managing UserAgreements
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementAdmin extends ModelAdmin {
	public static $managed_models = array(
		'UserAgreement'
	);
	
	public static $url_segment = 'user-agreements';
	public static $menu_title = "User Agreements";
	
}