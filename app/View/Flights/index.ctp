<?php $this->set('title_for_layout', "Aerodock | View flights");?>
<h1><?php if ($username != "")
						echo "All ".$username."'s flights:";
			else echo "All flights:";?></h1>
<?php if(Authcomponent::user('type') != 'student'){
	echo $this->Html->link(
		'Add Flight',
		array('controller' => 'flights', 'action' => 'add'));
	}?>
<table class="table">
	<tr>
		<?php if(Authcomponent::user('type') != 'student' && $username == ""): ?>
		<th><?php
			echo $this->Html->link(
			'Student',
			array('controller' => 'flights', 'action' => 'index', $page, "studentid", $dirArray["studentid"], $username));
			if($sort == "studentid"){
				echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["studentid"] == "") echo "-alt";
				echo '""></span>';}?></th>
			<?php endif ?>
		<th><?php echo $this->Html->link(
		'Instructor',
		array('controller' => 'flights', 'action' => 'index', $page, "instructorID",$dirArray["instructorID"], $username));
		if($sort == "instructorID"){
			echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["instructorID"] == "") echo "-alt";
				echo '""></span>';}?></th>
				<?php /*
		<th><?php echo $this->Html->link(
		'Tail No',
		array('controller' => 'flights', 'action' => 'index', $page, "tailNo",$dirArray["tailNo"]));
		if($sort == "tailNo"){
			echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["tailNo"] == "") echo "-alt";
				echo '""></span>';}?></th>*/?>
		<th><?php echo $this->Html->link(
		'Date',
		array('controller' => 'flights', 'action' => 'index', $page, "date",$dirArray["date"], $username));
		if($sort == "date"){
			echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["date"] == "") echo "-alt";
				echo '""></span>';}?></th>
		<th><?php echo $this->Html->link(
		'Flight Length',
		array('controller' => 'flights', 'action' => 'index', $page, "duration",$dirArray["duration"], $username));
		if($sort == "duration"){
			echo '<span class="glyphicon glyphicon-sort-by-attributes';
				if($dirArray["duration"] == "") echo "-alt";
				echo '""></span>';}?></th>
		<th></th>
		<th></th>
	</tr>

	<?php foreach ($flights as $flight): ?>
	<tr>
		<?php if(Authcomponent::user('type') != 'student' && $username == ""): ?>
		<td><?php echo $flight['Flight']['studentid']; ?></td>
		<?php endif ?>
		<td><?php echo $flight['Flight']['instructorID']; ?></td>
		<?php /*<td><?php echo $flight['Flight']['tailNo']; ?></td>*/?>
		<td><?php echo date('M d, Y', strtotime($flight['Flight']['date'])); ?></td>
		<td><?php echo number_format(((int)$flight['Flight']['duration'])/60, 0)." min" ?>
		<td><?php echo $this->Html->link(
			'View Flight',
			array('controller' => 'flights', 'action' => 'view', $flight['Flight']['id']));
			?>
		</td>
		<?php if(Authcomponent::user('type') != 'student'):?>
		<td><?php 
				echo $this->Form->postLink(
				'Delete Flight',
				array('action' => 'delete', $flight['Flight']['id'] ),
				array('confirm' => 'Are you sure?')
				);
			?>
		</td>	
		<?php endif; ?>
	</tr>
	<?php endforeach; ?>
	<?php unset($flight); ?>
</table>
	<ul class="pagination">
	<?php for ($i=1; $i <= $count; $i++) { 
		echo "<li ";
		if($i == $page) echo "class=active";
		echo ">";
		echo $this->Html->link($i, array('controller' => 'flights', 'action' => 'index', $i, $sort, $dir));
		echo "</li>";
	}?>
	</ul>
