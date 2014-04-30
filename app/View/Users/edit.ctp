<?php $this->set('title_for_layout', "Aerodock | ". $user['User']['firstname'] ." " .$user['User']['lastname']);?>
<h1><?php echo $user['User']['firstname']?> <?php echo $user['User']['lastname'];?></h1>
<br>
<div class="row">
	<div class="col-md-6">
	<h2>Stats:</h2>
		<p>Number of flights as pilot: <?php echo floor($durationSum/60)?> minutes</p>
		<p>Flight time as pilot: <?php echo $count?> flights</p>
		<?php if($user['User']['type'] != 'student') {
			echo "<p>Number of flights as instructor: ".$instructCount."</p>";
			echo "<p>Time in the air as instructor: ".floor($instructionSum/60)." minutes</p>";
		}
		if(Authcomponent::user('type') == 'admin'){
			echo $this->Form->postLink(
				'Purge last year\'s flights',
				array('controller' => 'flights', 'action' => 'purge'),
				array('confirm' => 'Are you sure? You can not undo this action. All flights before this caledar year will be deleted.')
				);
		}?>

	</div>
	<div class="col-md-6">
		<h2>Update <?php if(Authcomponent::user('username') == $user['User']['username']){
											echo 'your';
										} else { echo 'this user\'s';}?> information:</h2>

		<?php echo $this->Form->create('User', array(
																		'inputDefaluts' => array(
																			'div' => 'form-group',
																			'class' => 'form-control')));
					echo $this->Form->input('firstname',array('value' => $user['User']['firstname']));
					echo $this->Form->input('lastname' ,array('value' => $user['User']['lastname']));
					if(Authcomponent::user('type')== 'admin'){
					echo $this->Form->input('type', array(
																	'options' => array(
																		'Student', 'Teacher', 'Maintenance', 'Administrator'),
																	'selected' => $type));
					}
					if(Authcomponent::user('type') == 'admin' || 
							(Authcomponent::user('type') == 'teacher' && $user['User']['type'] == 'student') || 
							 Authcomponent::user('username') == $user['User']['username'] ){
						echo $this->Form->input('newPassword', array('type' => 'password'));
						echo $this->Form->input('passwordConfirmation', array('type' => 'password'));
					}
						echo $this->Form->end('Submit changes');?>
	</div>
</div>