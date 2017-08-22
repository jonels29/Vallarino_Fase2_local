<div id="footer" class="footer  col-xs-12">

<div    class="crop col-xs-6">
<?php echo  '    '.$this->model->ConexionSage(); ?>
</div>


<div style="float: right; text-align:right;" class="crop col-xs-6">
<img width="15px" src="img/Database.png" /><?php  echo $this->model->TestConexion(); ?>

</div>


</div>

</div>

</body>
</html>

<?php 

/*date_default_timezone_set('America/Panama');


$now = new DateTime();
$mins = $now->getOffset() / 60;

$sgn = ($mins < 0 ? -1 : 1);
$mins = abs($mins);
$hrs = floor($mins / 60);
$mins -= $hrs * 60;

$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);

echo $offset;*/


?>