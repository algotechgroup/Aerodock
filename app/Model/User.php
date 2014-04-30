<?php

App::uses('AppModel', 'Model');

class User extends AppModel {

    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->read(null, $id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The user could not be saved. Please, try again.')
            );
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->read(null, $id);
        }
    }

    public function deleteUser($id)
    {    
        if($this->delete($id))
        {
            return true;
        }
        else
        {
            return false;
        }
        
    }
    

    public function uploadUsers($uploadData){

        $handle = fopen($uploadData['tmp_name'], 'r');
        $i = 0;
  
        while($names = fgetcsv($handle)){
            $row['firstname'] = "";
            $row['lastname'] = "";
            $row['username'] = trim(substr($names[0], 0, strpos($names[0], "@")));
            $row['type'] = "student";
            if($row['username'] != ""){
                $data[$i] = $row;
                $i++;
            }
        }
        $this->saveMany($data);
        return true; 
    }
}