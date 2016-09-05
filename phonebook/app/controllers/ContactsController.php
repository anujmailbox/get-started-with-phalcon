<?php 

use Phalcon\Mvc\Controller;

class ContactsController extends Controller 
{
	public function indexAction()
	{
		$this->view->contacts = Contacts::find(array("order" => "name"));
		$this->view->title = "My Contacts" ;
	}

	// for rendering the new contact form
	public function newAction()
	{
	}

	// for rendering the edit contact form
	public function editAction($id)
	{
		// The following if block is commented because
		// we use dispatcher->forward() method in Update Action
		// and forward method will trigger this check and
		// consider it as an invalid request.
		// So we don't need it in this case.


		// if (!$this->request->isPost()) {

		$contact = Contacts::findFirst($id);
		if(!$contact){
			$this->flash->error("Don’t try be smart and edit an invalid contact.");
			$this->dispatcher->forward(['action' => 'index']);
		}
		else {
			$this->tag->displayTo("id", $contact->id);
			$this->tag->displayTo("name", $contact->name);
			$this->tag->displayTo("email", $contact->email);
			$this->tag->displayTo("phone", $contact->phone);
		}

		// }
		// else {
		// 	$this->flash->error("Invalid Request!!!");
		// 	$this->dispatcher->forward(['action' => 'index']);
		// }
	}

	// for creating a new contact in db
	public function createAction()
	{
		$contact = new Contacts();
		$success = $contact->save($this->request->getPost(), array('name', 'phone', 'email'));

		if ($success) {
			$this->flash->success("Contact Successfully Saved!");
			$this->dispatcher->forward(['action' => 'index']);
		}
		else {
			$this->flash->error("Following Errors occurred: <br/>");

			foreach ($contact->getMessages() as $message) {
				$this->flash->error($message);
			}

			$this->dispatcher->forward(['action' => 'new']);
		}
	}

	// for updating
	public function updateAction()
	{
		if(!$this->request->isPost()){

			$this->flash->error("Invalid Request!!!");
		}
		
		else {

			$id = $this->request->getPost("id");
			$contact = Contacts::findFirst($id);

			if(!$contact){
				$this->flash->error("No such record found");
			}
			else {
				$success = $contact->save($this->request->getPost(), array('name', 'phone', 'email'));

				if (!$success) {
					$this->flash->error("Following Errors occurred: <br/>");

					foreach ($contact->getMessages() as $message) {
						$this->flash->error($message);
					}

					return $this->dispatcher->forward(array(
				        "action" => "edit",
				        "params" => array($contact->id)
				    ));
				}

				$this->flash->success("Contact Successfully Updated!");
			}
		}

		$this->dispatcher->forward(['action' => 'index']);						
	}	

	// for removing a contact
	public function deleteAction($id)
	{
		$contact = Contacts::findFirst($id);

		if(!$contact){
			$this->flash->error("Don’t try to remove a contact that doesn’t even exist in the first please.");
		}
		else {
			if(!$contact->delete()){
				
				foreach ($contact->getMessages() as $message) {
					$this->flash->error($message);
				}
			}
			else{
				$this->flash->success("The Contact R.I.P successful!!!");
			}

		}

		$this->dispatcher->forward(['action' => 'index']);
	}
}