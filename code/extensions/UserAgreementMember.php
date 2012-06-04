<?php
/**
 * UserAgreementMember extension class
 *
 * Decorates Member to check and save agreements required for his/her group(s)
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementMember extends DataObjectDecorator {

	function extraStatics() {
		return array(
			'many_many' => array(
				'SignedAgreements' => 'UserAgreement'
			),
		);
	}


	/**
	 * check if any agreements are required before signing the user in
	 * @return boolean
	 **/
	public function memberLoggedIn(){
		if($this->needsToSignAgreement()){
			Session::set('RequiresAgreement', 1);
		}
	}


	/**
	 * checks to see if the user has agreed to the terms assigned to his/her Group
	 * @return boolean
	 **/
	public function needsToSignAgreement(){
		// are there any required agreements for this users groups?
		$groups 	= $this->owner->Groups();
		$groupIDs 	= implode(',', $groups->getIdList());
		$requiredAgreements = DataObject::get('UserAgreement', "GroupID IN ($groupIDs)");

		if(!$requiredAgreements){
			return false;
		}

		// check the required agreements have been signed
		$signedAgreements = $this->owner->SignedAgreements();

		if(!$signedAgreements->exists()){
			return true;
		}

		foreach($requiredAgreements as $required) {
			if(!$signedAgreements->find('ID', $required->ID)){
				return true;
			}
		}	

		return false;
	}


	/**
	 * updateCMSFields
	 **/
	public function updateCMSFields($fields){
		$fields->removeByName('SignedAgreements');
	}
}
