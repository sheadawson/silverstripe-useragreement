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
		'Title' => 'Varchar',
		'Content' => 'HTMLText'
	);

	static $has_one = array(
		'Group' => 'Group'
	);

	static $summary_fields = array(
		'Title'
	);

	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->addFieldToTab('Root.Main', new TextField('Title'), 'GroupID');
		$fields->addFieldToTab('Root.Main', new HTMLEditorField('Content'));
		return $fields;
	}
}
