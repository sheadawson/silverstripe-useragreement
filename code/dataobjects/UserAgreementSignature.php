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

	static $singular_name = "User Agreement Signature";
	static $plural_name = "User Agreement Signatures";
	
	static $db = array(
		'DateAgreed' 		=> 'SS_Datetime',	
		'SessionID' 		=> 'Varchar(255)',
		'AgreementContent'	=> 'HTMLText'
	);

	static $has_one = array(
		'UserAgreement' => 'UserAgreement',
		'Member'		=> 'Member'
	);
	
	static $summary_fields = array(
		'DateAgreed',
		'Member.Name'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		return $fields;
	}
}
