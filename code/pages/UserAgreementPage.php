<?php
/**
 * Page to display user agreement form
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementPage extends Page {

	private static $singular_name 	= "User Agreement Page";
	private static $plural_name 	= "User Agreement Pages";

	/**
	 * Create default blog setup
	 */
	function requireDefaultRecords() {
		parent::requireDefaultRecords();
		
		if(!DataObject::get_one('UserAgreementPage')) {
			if(class_exists('Site') && ($sites = Site::get())) {
				if($sites->first()) {
					foreach($sites as $site) {
						$page = new UserAgreementPage();
						$page->Title 		= "User Agreement";
						$page->URLSegment 	= "user-agreement";
						$page->Status 		= "Published";
						$page->ShowInMenus 	= 0;
						$page->ParentID = $site->ID;
						$page->write();
						$page->publish("Stage", "Live");
						DB::alteration_message("User Agreement Page Created","created");
					}
				}
			}
			else {
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

	private static $allowed_actions = array(
		'Form',
		'agree'
	);

	public function index() {
		if($this->getAgreement()) {
			return array();
		}
		else {
			return $this->redirect(Director::absoluteBaseURL());
		}
	}

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
			$member = Member::currentUser();
			$this->CurrentAgreement = $member->unsignedAgreements()->First();
		}
		return $this->CurrentAgreement;
	}


	/**
     * agreement content
     */
	public function Content(){
		return $this->getAgreement()->Content;
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
		$fields 	= new FieldList(array(
			new CheckboxField('Agree', $this->getAgreement()->AgreeText),
			new HiddenField('AgreementID', 'AgreementID', $this->getAgreement()->ID)
		));
        $validator 	= new RequiredFields('Agree');
        $actions 	= new FieldList(new FormAction('agree', 'Submit'));
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

		$member = Member::currentUser();
		
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
			$redirect = Director::absoluteBaseURL();
			$this->extend('updateAgreeSuccess', $redirect);
			return $this->redirect($redirect);
		}
    }
}
