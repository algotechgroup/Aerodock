<?php $this->set('title_for_layout', "Aerodock | All users");?>
<h1>
<?php if(Authcomponent::user('type') == 'admin')
				echo "All users:"; 
			else 
				echo "All students:"; ?></h1>
<?php if(Authcomponent::user('type') == 'admin'){
	echo $this->Html->link(
		'Add user',
		array('controller' => 'users', 'action' => 'add'));
	}?>
<table class="table">
	<tr>
		<th><?php echo $this->Html->link(
			'First Name',
			array('controller' => 'users', 'action' => 'index', $page, "firstname",$dirArray["firstname"]));
			if($sort == "firstname"){
				echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["firstname"] == "") echo "-alt";
				echo '""></span>';}?></th>
		<th><?php echo $this->Html->link(
			'Last Name',
			array('controller' => 'users', 'action' => 'index', $page, "lastname",$dirArray["lastname"]));
			if($sort == "lastname"){
				echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["lastname"] == "") echo "-alt";
				echo '""></span>';}?></th>
		<th><?php echo $this->Html->link(
			'Pipeline ID',
			array('controller' => 'users', 'action' => 'index', $page, "username",$dirArray["username"]));
			if($sort == "username"){
				echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["username"] == "") echo "-alt";
				echo '""></span>';}?></th>
		<?php if(Authcomponent::user('type') != 'teacher'):?>
		<th><?php echo $this->Html->link(
			'Type',
			array('controller' => 'users', 'action' => 'index', $page, "type",$dirArray["type"]));
			if($sort == "type"){
				echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["type"] == "") echo "-alt";
				echo '""></span>';}?></th>
		<?php endif ?>
		<th>View Flights</th>
		<th>Edit</th>
		<?php if(Authcomponent::user('type') == 'admin'):?>
		<th>Delete</th>
		<?php endif ?>
		<th><th>
	</tr>
	<?php foreach ($users as $user): ?>
		<tr>
		<td><?php echo $user['User']['firstname']; ?></td>
		<td><?php echo $user['User']['lastname']; ?></td>
		<td><?php echo $user['User']['username']; ?></td>
		<?php if(Authcomponent::user('type') != 'teacher'):?>
		<td><?php echo $user['User']['type']; ?></td>
		<?php endif ?>
		<td><?php echo $this->Html->link('View Flights', array('controller' => 'flights', 'action' => 'index', '1', 'date', 'desc', $user['User']['username']))?></td>
		<td><i class="customBLUE"><i class="fa fa-pencil-square-o"></i></i><?php echo $this->Html->link(
			' Edit User',
			array('controller' => 'users', 'action' => 'edit', $user['User']['id']));
			?></td>
		<td><i class="customRED"><i class="fa fa-times-circle"></i></i><?php echo $this->Form->postLink(
			' Delete User',
			array('controller' => 'users', 'action' => 'delete', $user['User']['id']),
			array('confirm' => 'Are you sure?'));
			?>
		</td>
	</tr>
	<?php endforeach; ?>
	<?php unset($flight); ?>
</table>