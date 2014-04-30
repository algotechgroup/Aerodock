<?php
/**
 *
 *
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Pages
 * @since         CakePHP(tm) v 0.10.0.1076
 */
$this->set('title_for_layout', "Aerodock at MTSU");
?>


      <div class="jumbotronCUSTOM">
        <style> h3{color:#00097F; font-size:65px; text-shadow:1px 1px #FFF;} h2{color:#262D7F;font-size:24px;text-shadow:1px 1px #FFF;}</style>
        <h3><b>Aerodock</b><br>
        <small>MTSU Aerospace Student Flight Tracking</small></h1>
        <p><?php echo $this->Html->link('Sign in using your Pipeline ID and password',
        																array(
        																		'controller' => 'users',
        																		'action' => 'login'),
        																		array('class' => 'btnBLUE',
        																				  'role' => 'button'));?>
      </div>


      <div class="row marketing">
        <div class="col-md-6">
          <h4>Logging in</h4>
          <p>Aerodock requires the user to be enrolled in an MTSU Aerospace class to gain access. When your instructor uploads your first flight, you can begin logging in at home.</p>
        </div>
      </div>