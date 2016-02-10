<?php
/**
 * ModelAdmin for managing UserAgreements
 *
 * @package silverstripe-useragreement
 * @author shea@silverstirpe.com.au
 **/
class UserAgreementAdmin extends ModelAdmin {
	private static $managed_models = array(
		'UserAgreement'
	);
	
	private static $url_segment = 'user-agreements';
	private static $menu_title = "User Agreements";
	
	public function getEditForm($id = null, $fields = null) {
		$form = parent::getEditForm($id, $fields);
		
		/**
		 * In the instance that the editing results in the UserAgreement switching between GridField's, we need to
		 * load all agreements into both GridFields to permit the Save action to succeed.
		 * (ie. the archive boolean is toggled)
		 * Use the EditForm action to detect when editing vs viewing
		 */
		$action = $this->request->param('Action');
		if ($action && $action == 'EditForm') {
			$archived = UserAgreement::get();
			$liveAgreements = $archived;
		} else {
			$archived = UserAgreement::get()->filter('Archived',true);
			$liveAgreements = UserAgreement::get()->filter('Archived',false);
		}
		
		
		// Show Archived agreements separate to current agreements
		$fieldName = 'ArchivedAgreements';

		// Archived Agreements GridField Config
		$config = new GridFieldConfig_Base();
		$config->addComponent(new GridFieldEditButton());
		$config->addComponent(new GridFieldDetailForm());
		$config->addComponent(new GridFieldDeleteAction());
		$config->addComponent(new GridFieldExportButton());
		$config->addComponent(new GridFieldPrintButton());

		$config->getComponentByType('GridFieldPaginator')->setItemsPerPage(5);

		if($archived->count()) {
			$formFieldArchive = GridField::create(
				$fieldName,
				_t(
					'UserAgreements.ArchivedAgreements',
					'Archived Agreements'
					),
				$archived,
				$config
			);

			$formFieldArchive->setForm($form);
			$form->Fields()->insertAfter($formFieldArchive, 'UserAgreement');
		}
		
		// Omit Archived Agreements from main gridfield
		$grid = $form->Fields()->dataFieldByName('UserAgreement');
		if ($grid) {		
			$grid->setList($liveAgreements);
			$grid->setTitle(
				_t(
					'UserAgreements.CurrentAgreements',
					'Current Agreements'
					)
			);
		}
		
		return $form;
	}
	
}