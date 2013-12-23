<?php
/**
 * UserAgreementSignature DataObject class
 *
 * Contains a log of all Agreement Acceptances
 *
 * @package silverstripe-useragreement
 * @author rodney@silverstirpe.com.au
 **/
class UserAgreementSignature extends DataObject {

	private static $singular_name = "User Agreement Signature";
	private static $plural_name = "User Agreement Signatures";
	
	private static $db = array(
		'DateAgreed' 		=> 'SS_Datetime',	
		'SessionID' 		=> 'Varchar(255)',
		'AgreementContent'	=> 'HTMLText'
	);

	private static $has_one = array(
		'UserAgreement' => 'UserAgreement',
		'Member'		=> 'Member'
	);
	
	private static $summary_fields = array(
		'DateAgreed',
		'Member.Name'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		return $fields;
	}
}
