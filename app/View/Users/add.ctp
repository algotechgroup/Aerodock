<?php $this->set('title_for_layout', "Aerodock | Add users");?>
<h1>Add new Users</h1>

<?php
echo $this->Form->create('User', array('type' => 'file'));
echo $this->Form->input('csvPath', array('type' => 'file', 'class'=> 'form-control', 'label' => 'Choose file with user emails'));
echo $this->Form->end('Create Users');
?>