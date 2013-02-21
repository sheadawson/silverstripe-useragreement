<?php
/**
 * Page to display user agreement form
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementPage extends Page {

	static $singular_name 	= "User Agreement Page";
	static $plural_name 	= "User Agreement Pages";

	/**
	 * Create default blog setup
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		
		if(!DataObject::get_one('UserAgreementPage')) {
			$page = new UserAgreementPage();
			$page->Title 		= "User Agreement";
			$page->URLSegment 	= "user-agreement";
			$page->Status 		= "Published";
			$page->ShowInMenus 	= 0;
			$page->write();
			$page->publish("Stage", "Live");
			DB::alteration_message("User Agreement Page Created","created");
		}
	}


	/**
	 * the page content is replaced by the current agreement content
	 */
	public function getCMSFields(){
		$fields = parent::getCMSFields();
		$fields->removeByName('Content'); 
		return $fields;
	}
}

class UserAgreementPage_Controller extends Page_Controller {
	

	/**
	 * the current agreement to be signed / reviewed
	 * @var UserAgreement object
	 */
	public $CurrentAgreement;


	/**
	 * get's an agreement that is waiting to be signed by the user
	 * - based on sort order 
	 * @return UserAgreement object
	 */
	public function getAgreement(){
		if(!$this->CurrentAgreement){
			$member = $this->CurrentMember();
			$this->CurrentAgreement = $member->unsignedAgreements()->First();
		}
		return $this->CurrentAgreement;
	}


	/**
     * agreement content
     */
	public function Content(){
		if($agreement = $this->getAgreement()){
			return $agreement->Content;
		}
	}


	/**
     * agreement title
     */
	public function Title(){
		if($agreement = $this->getAgreement()){
			return $agreement->Title;
		}
	}


	/**
     * agreement form
     */
	public function Form(){
		$fields 	= new Fieldset(array(
			new CheckboxField('Agree', 'I Agree to the terms and conditions'),
			new HiddenField('AgreementID', 'AgreementID', $this->getAgreement()->ID)
		));
        $validator 	= new RequiredFields('Agree');
        $actions 	= new FieldSet(new FormAction('agree', 'Submit'));
        $form 		= new Form($this, 'Form', $fields, $actions, $validator);
        return $form;
	}


	/**
     * agreement form handler
     */
	function agree($data, $form){
		if(!isset($data['Agree'])){
			if($this->hasExtension('ZenMessageExtension')){
				$this->setMessage('Error', 'You must agree to the terms and conditions to continue');
			}
			return $this->redirectBack();
		}

		$member = $this->CurrentMember();
		
		if ($agreement = DataObject::get_by_id('UserAgreement',(int)$data['AgreementID'])) {
			$agreement->addSignature($member);
		}
			
		// there may be other agreements to sign for other groups this user is a member of
		if($member->needsToSignAgreement()){
			return $this->redirect($this->Link());
		// else let the user begin using the application
		}else{
			Session::clear('RequiresAgreement');
			if($this->hasExtension('ZenMessageExtension')){
				$this->setMessage('Success', 'Thank you, the agreement has been saved and you can now continue.');	
			}
			return $this->redirect(Director::baseURL() . Security::default_login_dest());
		}
    }
}