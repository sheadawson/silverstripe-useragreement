<?php
/**
 * ModelAdmin for managing UserAgreements
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementAdmin extends ModelAdmin {
	private static $managed_models = array(
		'UserAgreement' => array('title' => 'Agreements'),
		'UserAgreementSignature' => array('title' => 'Signatures')
	);
		
	private static $url_segment = 'user-agreements';
	private static $menu_title = "User Agreements";
	
}