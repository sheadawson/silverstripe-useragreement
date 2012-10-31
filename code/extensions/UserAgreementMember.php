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
			'has_many' => array(
				'SignedAgreements' => 'UserAgreementSignature'
			)
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
		return $this->unsignedAgreements()->exists();
	}
	
	/**
	 * returns sorted User Agreements to be signed
	 * @return DataObjectSet UserAgreement's required
	 **/
	public function unsignedAgreements() {
		// are there any required agreements for this users groups?
		$groups 	= $this->owner->Groups();
		$groupIDs 	= implode(',', $groups->getIdList());
		$requiredAgreements = DataObject::get('UserAgreement', "GroupID IN ($groupIDs)");
		
		$signedAgreements = $this->owner->SignedAgreements();
		
		$agreementsRemaining = new DataObjectSet();
				
		// collect agreements to be signed - checking agreement type (one off vs session)
		foreach($requiredAgreements as $required) {
			if(!$signedAgreements->find('UserAgreementID', $required->ID)){
				$agreementsRemaining->push($required);
			} else {
				if ($required->Type == 'Every Login') {
					$signings = $this->owner->SignedAgreements("UserAgreementID='".$required->ID."'");
					if (!$signings->find('SessionID',session_id())) {
						$agreementsRemaining->push($required);
					}
				}
			}
		}
		$agreementsRemaining->sort('Sort','ASC');
		
		return $agreementsRemaining;
	}


	/**
	 * updateCMSFields
	 **/
	public function updateCMSFields($fields){
		$fields->removeByName('SignedAgreements');
	}
}
