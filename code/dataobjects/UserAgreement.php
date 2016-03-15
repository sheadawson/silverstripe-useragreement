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

	private static $singular_name = "User Agreement";
	private static $plural_name = "User Agreements";
	
	private static $db = array(
		'Title' 	=> 'Varchar',
		'Content' 	=> 'HTMLText',
		'Type'		=> 'enum("Every Login, Once Only","Once Only")',
		'Sort'		=> 'Int',
		'AgreeText' => 'HTMLText',
		'Archived'	=> 'Boolean'
	);

	private static $defaults = array(
		'AgreeText' => 'I Agree to the terms and conditions'
	);
	
	private static $has_one = array(
		'Group' => 'Group'
	);
	
	private static $has_many = array(
		'Signatures'	=> 'UserAgreementSignature'
	);
	
	private static $summary_fields = array(
		'Title' => 'Title',
		'Archived.Nice' => 'Archived',
		'Created.Nice' => 'Created'
	);
	
	private static $default_sort = 'Sort DESC, Created DESC';
	

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TextField('Title'), 'GroupID');
		$fields->addFieldToTab('Root.Main', new DropdownField('Type', 'Type', $this->dbObject('Type')->EnumValues(), $this->Type));
		$fields->addFieldToTab('Root.Main', new TextField('Sort'));
		$fields->addFieldToTab('Root.Main', new CheckboxField('Archived'));
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Content'));
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('AgreeText'));
				
		$signatureGridfield = $fields->fieldByName("Root.Signatures.Signatures");
		$config = $signatureGridfield->getConfig();
		$config->removeComponentsByType('GridFieldAddExistingAutocompleter');
		$config->removeComponentsByType('GridFieldAddNewButton');
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
