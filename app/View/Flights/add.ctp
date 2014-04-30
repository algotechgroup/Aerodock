<?php $this->set('title_for_layout', "Aerodock | Add flight");?>
<h1>Add new flight:</h1>

<?php
echo $this->Form->create('Flight', array('type' => 'file', 'div' => 'input-group'));
echo $this->Form->input('studentid', array('class'=> 'form-control', 'label' => 'Student\'s Pipeline ID: '));
echo $this->Form->input('csvPath', array('type' => 'file', 'class'=> 'form-control', 'label' => 'Flight CSV'));
echo $this->Form->end(array ('label' => 'Create Flight', 'id' => 'submitBTN', 'class'=>'btn btn-default', 'onClick' => 'spinMeRightRound();'));
?>

<?php echo $this->Html->script('spin.min'); ?>
<?php echo $this->Html->script('spin.commands'); ?>
<?php echo '<script type="text/javascript">'
   , '</script>'; ?>