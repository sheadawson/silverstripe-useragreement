<?php
/**
 * UserAgreement DataObject class
 *
 * A user agreement for a user Group
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreement extends DataObject {

	static $singular_name = "User Agreement";
	static $plural_name = "User Agreements";
	
	static $db = array(
		'Title' 	=> 'Varchar',
		'Content' 	=> 'HTMLText',
		'Type'		=> 'enum("Every Login, Once Only","Once Only")',
		'Sort'		=> 'Int'
	);

	static $has_one = array(
		'Group' => 'Group'
	);
	
	static $has_many = array(
		'Signatures'	=> 'UserAgreementSignature'
	);
	
	static $summary_fields = array(
		'Title'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TextField('Title'), 'GroupID');
		$fields->addFieldToTab('Root.Main', new DropdownField('Type', 'Type', $this->dbObject('Type')->EnumValues(), $this->Type));
		$fields->addFieldToTab('Root.Main', new TextField('Sort'));
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Content'));
		return $fields;
	}
	
	public function addSignature($member = null) {
		if(!$member) {
			$member = Member::currentUser();
		}
		
		$sig = new UserAgreementSignature();
		
		$sig->MemberID 			= $member->ID;
		$sig->DateAgreed		= SS_Datetime::now();
		$sig->SessionID			= session_id();
		$sig->AgreementContent	= $this->Content;
		$this->Signatures()->add($sig);
	}
}
