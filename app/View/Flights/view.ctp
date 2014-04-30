<?php $this->set('title_for_layout', "Aerodock | ". $title);?>

<?php $this->Html->css('viewTemplate', array('inline' => false));?>
<?php echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'); ?>
<?php echo $this->Html->script('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js')?>
<?php echo $this->Html->css('//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css', array('inline' => false))?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyDY0kkJiTPVd2U7aTOAwhc9ySH6oHxOIYM&sensor=false"></script>
<?php if(!$hasPassword): ?>
  <div class="alert alert-warning" id="updateInfo"><b>This user first needs a password:</b>
    <div class="input-group">
      <div class="row">
  <?php   echo $this->Form->input('firstname', array('label' =>'First Name:','class'=> 'form-control',
            'div' => 'col-md-6 first' ));
          echo $this->Form->input('lastname', array('label' =>'Last Name:', 'class'=> 'form-control', 
            'div' => 'col-md-6 last')); ?>
          </div>
          <div class="row"><?php
          echo $this->Form->input('newPassword', array('type' => 'password', 'label' => 'Password:', 'class'=> 'form-control', 'div' => 'col-md-6 password'));
          echo $this->Form->input('passwordConfirmation', array('type' => 'password', 'label' => 'Confirm Password:', 'class'=> 'form-control', 'div' => 'col-md-6 confirmation'));?></div>
          <div class="row"><?php
          echo $this->Form->button('Submit changes', array('id' => 'updatePassword', 'class' => 'btn btn-default'));?></div>
    </div>
  </div>
<?php endif ?>
<div class="row">
  <div  class="col-md-7">
    <div id="googleMap" style="width:550px;height:380px;"></div>
  </div>
  <div class="col-md-4 turn-table">
    <table class="table table-striped">

       <?php 
      if(count($events)>=1){
        foreach ($events as $key => $turn){
          $name = "".($key+1);
          echo $this->Html->tableCells(array(
            array(
              array(($key+1).". ".$turn['name'],
                array('id'=>'turn'.$key)))));
          echo "\n";}
          }
              ?>
    </table>
  </div>
</div>
<div class="row text-center graph-selector">
  <div class="btn-group">
    <button type="button" class="btn btn-default" id="Altitude">Alt/Airspeed</button>
    <button type="button" class="btn btn-default" id="Engine">Engine Temps</button>
    <button type="button" class="btn btn-default" id="RPM">Manifold/RPM</button>
    <button type="button" class="btn btn-default" id="Oil">Oil Temp/Pres</button>
    <button type="button" class="btn btn-default" id="Fuel">Fuel Flow/Pres</button>
  </div>
</div>
<div class="row">
  <div class="col-md-9">
    <div id="chart_div"></div>
    </div>
  </div>
</div>
<script>
var coordsUrl = '<?php echo Router::url(array('controller' => 'flights', 'action' => 'getCoords')); ?>';
var flightid = <?php echo $flight['Flight']['id'] ?>;
var studentid = <?php echo '"'.$flight['Flight']['studentid'].'"' ?>;
var passwordUrl = '<?php echo Router::url(array('controller' => 'flights', 'action' => 'changePassword')); ?>'
var dataUrl = '<?php echo Router::url(array('controller' => 'flights', 'action' => 'getData')); ?>';
var eventsUrl = '<?php echo Router::url(array('controller' => 'flights', 'action' => 'getEvents')); ?>';
var zoomLevel = <?php echo $zoomLevel ?>;
</script>
<?php echo $this->Html->script('flightView')?>