<?php
/**
 * UserAgreementController extension class
 *
 * checks if there are any agreements that need to be signed before proceeding
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementController extends Extension{

	/**
	 * Check to see if the current user needs to agree to the terms and conditions 
	 * of their group before proceeding
	 **/
	public function onAfterInit(){
		$member = $this->owner->CurrentMember();
		if(	$member && Session::get('RequiresAgreement')){ // <-- set on user login
			if($this->owner->class != 'Security' && $this->owner->class != 'RestrictedSecurityController' && $this->owner->ClassName != 'UserAgreementPage'){
				$agreementPage = DataObject::get_one('UserAgreementPage');
				return $this->owner->redirect($agreementPage->Link());
			}	
		}
		
	}
}