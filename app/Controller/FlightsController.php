<?php
class FlightsController extends AppController {
	public $helpers = array('Html', 'Form', 'Session', 'Js');
	public $components = array('Session','RequestHandler');

	public function beforeFilter() {
	    parent::beforeFilter();
	}

	public function index($page = null, $sort = null, $dir = null, $studentid = null){

		if($page == null){
			$page = 1;
		}
		if($sort == null){
			$sort = "date";
		}
		if($dir == null){
			$dir = "desc";
		}
		$dirArray = array("studentid" => "desc", 
								 "instructorID" => "desc",
								 "tailNo" => "desc", 
								 "date" => "desc", 
								 "duration" => "desc");
		if($dir == "desc"){
			$dirArray[$sort] = "asc";
		}
		$this->set('dir', $dir);
		$this->set('dirArray', $dirArray);
		$this->set('page', $page);
		$this->set('sort', $sort);
		$user = $this->Auth->user();
		if($user['type'] != 'student'){
			if($studentid == null){

				$this->set('username', "");
				$this->set('count', ceil($this->Flight->find('count')/10));
				$this->set('flights', $this->Flight->find('all', array(
	        								'order' => array($sort => $dir),
	        								'limit' => 10,
	        								'page' => $page)));
			} else {
				$this->set('username', $studentid);
				$this->set('count', ceil($this->Flight->find('count',
																			array('conditions' => 
																				array('studentid' => $studentid)))/10));
				$this->set('flights', $this->Flight->find('all', array(
													'conditions' =>	array('studentid' => $studentid),
	        								'order' => array($sort => $dir),
	        								'limit' => 10,
	        								'page' => $page)));
			}
		}
	}
		
	
  
	public function add(){
		ClassRegistry::init('User');
		$user = new User(); 
		if($this->request->is('post')) {
			if($this->request->data['Flight']['csvPath']['size'] == 0){
				$this->Session->setFlash('Attach CSV to upload flight.', 'fail');
				return $this->redirect(array('action' => 'add'));
			}
			if($this->request->data['Flight']['csvPath']['type'] != 'text/csv'){
				$this->Session->setFlash('Only attach CSVs for upload.', 'fail');
				return $this->redirect(array('action' => 'add'));
			}
			if(count($user->findByUsername($this->request->data['Flight']['studentid']))==0){
				$this->Session->setFlash('User with this ID is not in the system.', 'fail');
				return $this->redirect(array('action' => 'add'));
			}
			$this->Flight->create();
			$data = $this->request->data;
			$csvData = $data['Flight']['csvPath'];
			unset($data['Flight']['csvPath']);
			$this->Flight->set('instructorID', $this->Auth->user('username'));

			if($this->Flight->save($data)) {

				$loadCSVArray = $this->Flight->uploadFile($csvData, $this->Flight->id);
				$this->Flight->save($loadCSVArray);

				$this->Session->setFlash('Your flight has been saved.', 'success');
				return $this->redirect(
					array('controller' => 'flights', 'action' => 'view', $this->Flight->id)
					);
			}
			$this->Session->setFlash('Unable to add your flight.', 'fail');
		}
	}

	public function delete($id)
	{
		
		if( $this->request->is('get') )
		{
			$this->Session->setFlash('You can not delete flights.', 'fail');
			return $this->redirect(array('action' => 'index'));
		}
		
		if($this->Auth->user('type') == 'admin' && $this->Flight->deleteFlight($id))
		{
			if($this->Flight->delete($id) && $log->deleteLog($id))
			{
				$this->Session->setFlash("The flight has been deleted", 'success');
			}
			else
			{
				$this->Session->setFlash('Attempt to delete flight failed.', 'fail');
			}
		} 	
		else
		{
			$this->Session->SetFlash('Only an Administrator may delete flights.', 'fail');
		}
		return $this->redirect(array('contoller' => 'flights', 'action' => 'index'));

	}


	public function view($id = null) {
		$this->Flight->events(1);
		if(!$id) {
			throw new NotFoundException(__('Invalid flight'));
		}		
		$flight = $this->Flight->findById($id);	
		if(!$flight){
			throw new NotFoundException(__('Invalid flight'));
		}	
		if($flight['Flight']['studentid'] != $this->Auth->user('username') && 
				$this->Auth->user('type') == 'student' ){
			$this->Session->setFlash('Not authorized to view this flight.','fail');
			return $this->redirect(
					array('controller' => 'flights', 'action' => 'index'));
		}

		ClassRegistry::init('User');
		$user = new User();
		$user = $user->findByUsername($flight['Flight']['studentid']);
		$this->set('hasPassword', $user['User']['password'] != "");
		if($user['User']['firstname'] == ""){
			$this->set('title', "New flight");
		} else {
			$this->set('title', (($user['User']['firstname']."'s " . date('M d', strtotime($flight['Flight']['date'])). " flight")));
		}
		$this->set('flight', $flight);
		$flightInfo = $this->Flight->getLatLong($flight['Flight']['id']);
		$this->set('zoomLevel', array_shift($flightInfo));
		$events = $this->Flight->events($id);
		$this->set('events',  $events[0]);
		$this->Session->write('events', $events[1]);

	}
	/*
	public function maintenance(){
		if($this->Auth->user('type') != 'maint' && $this->Auth->user('type') != 'admin'){
			$this->Session->setFlash('Not authorized to view maintenance logs.','fail');
			return $this->redirect(array('controller' => 'flights',
																	 'action' => 'index'));
		}
		$this->set('flights',
			 $this->Flight->find('all', array('conditions' => array('maintenance' => 1), 
			 	'order' => 'Date DESC')));
	}*/

	public function getData(){
		$this->autoRender = false;
		$this->layout = 'ajax';
		if($this->request->data['studentid'] != $this->Auth->user('username') && 
				$this->Auth->user('type') == 'student' ){
			$this->Session->setFlash('Not authorized to view this flight.','fail');
			return $this->redirect(
					array('controller' => 'flights', 'action' => 'index'));
		}
		return $this->Flight->generateJsArray($this->request->data);

	}
	public function getEvents(){
		$this->autoRender = false;
		$this->layout = 'ajax';
		if($this->request->data['studentid'] != $this->Auth->user('username') && 
			$this->Auth->user('type') == 'student' ){
			$this->Session->setFlash('Not authorized to view this flight.','fail');
			return $this->redirect(
					array('controller' => 'flights', 'action' => 'index'));
		}
		return $this->Session->read('events');
	}

	public function getCoords(){
		$this->autoRender = false;
		$this->layout = 'ajax';
		if($this->request->data['studentid'] != $this->Auth->user('username') && 
				$this->Auth->user('type') == 'student' ){
			$this->Session->setFlash('Not authorized to view this flight.','fail');
			return $this->redirect(
					array('controller' => 'flights', 'action' => 'index'));
		}
		return $this->Flight->generateCoords($this->request->data);
	}

	public function changePassword(){
		$this->autoRender = false;
		$this->layout = 'ajax';
    App::uses('SimplePasswordHasher', 'Controller/Component/Auth');
		ClassRegistry::init('User');
		$user = new User();
		if(!($this->Auth->user('type') == 'student')){
			$toUpdate = $user->findByUsername($this->request->data['studentid'])['User'];
			$passwordHasher = new SimplePasswordHasher();
			$toUpdate['password'] = $passwordHasher->hash($this->request->data['password']);
			$toUpdate['firstname'] = $this->request->data['firstname'];
			$toUpdate['lastname'] = $this->request->data['lastname'];
			$user->save($toUpdate);
			return 1;
		} else {
			return 0;
		}
	}

	public function purge(){
		$this->Auth->user();
		if($this->Auth->user('type') != 'admin'){
			$this->Session->setFlash('Not authorized to purge flights.','fail');
			return $this->redirect(
					array('controller' => 'flights', 'action' => 'index'));
		}
		$currentYear = date("Y");
		$completed = true;
		for ($i=$currentYear-1; $i >= 2013; $i--) { 
			$flightsToDelete = $this->Flight->find('all', array(
																				'conditions' => array('date LIKE' => ($i."%"))));
			if(count($flightsToDelete) > 0){
				for($i = 0; $i < count($flightsToDelete); $i++){
					$completed &= $this->Flight->delete($flightsToDelete[$i]['Flight']['id']);
				}
			}
		}
		if($completed){
			$this->Session->setFlash('All flights from '.($currentYear-1).' and before have been successfully deleted','success');
		} else {
			$this->Session->setFlash('One or more flights were not deleted successfully','fail');
		}
		return $this->redirect(
					array('controller' => 'flights', 'action' => 'index'));
	}
	public function isAuthorized($user) {
	    // Admin can access every action
	    if ($user) {
	        return true;
	    }

	    // Default deny
	    return false;
	}
} 
