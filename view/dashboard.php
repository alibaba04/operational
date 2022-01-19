<?php
defined( 'validSession' ) or die( 'Restricted access' ); 
?>
<section class="content-header">
  <h1>
    Dashboard
    <small></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Dashboard</li>
  </ol>
</section>
<br>
<div class="box-body">
  <input type="hidden" name="kodeuser" id="kodeuser" value="<?php echo $_SESSION["my"]->id ?>">
  <div class="alert alert-info alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <h4><i class="icon fa fa-info"></i> Alert!</h4>
    Welcome to PT AKI &#x1F609;
  </div>
  <div class="box-header">
    <div class="box box-solid">
      <div class="box-body">
        
      </div>
    </div>
  </div>
</div>

<script src="./plugins/jQuery/jquery-2.2.3.min.js"></script>
<script src="./plugins/chartjs/Chart.min.js"></script>