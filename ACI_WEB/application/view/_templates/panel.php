<script type="text/javascript">
$(window).load(function(){
$('[data-submenu]').submenupicker();
});
</script> 

<div  class='menu_header col-xs-12'>

<nav id='menu' class="navbar navbar-default">
  <div class="navbar-header">
    <button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse">
      <span class="sr-only"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>

   <a  class="navbar-brand" onClick="history.go(-1); return true;" ><img title="Atras" class='icon' src="img/Arrow Left.png" /></a>
   <a  class="navbar-brand" onClick="history.go(+1); return true;" ><img title="Adelante" class='icon' src="img/Arrow Right.png" /></a>
   <a  class="navbar-brand" onClick="location.reload();" ><img title="Actualizar" class='icon' src="img/Button White Load.png" /></a>
   <a  class="navbar-brand" href="<?PHP ECHO URL; ?>index.php?url=home/index"><img title="Dashboard"  class='icon' src="img/Dashboard.png" /></a>
  </div>


<div class="collapse navbar-collapse">

<ul class="nav navbar-nav">

<!-- <li class="dropdown">
        <a tabindex="0"  data-toggle="dropdown" data-submenu="" aria-expanded="false">
          <img class='icon' src="img/Chart Bar.png" />Gestion de Ventas<span class="caret"></span>
        </a>

<ul class="dropdown-menu">
  <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_ventas/ges_orden_ventas"><img class='icon' src="img/Document Checklist.png" />Orden de Ventas</a></li>
   <li><a tabindex="0"  href="<?PHP ECHO URL; ?>index.php?url=ges_ventas/ges_pro_ventas"><img class='icon' src="img/Money.png" />Factura de Ventas</a></li>

</ul> -->

<?php 
if($this->model->rol_campo=='1'){ ?>
<li class="dropdown">
        <a tabindex="0"  data-toggle="dropdown" data-submenu="" aria-expanded="false">
          <img class='icon' src="img/Products.png" />Gestion de Inventario<span class="caret"></span>
        </a>

<ul class="dropdown-menu">
<!--   <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_inventario/inv_list"><img class='icon' src="img/Box.png" />Inventario</a></li>  -->

  <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_requisiciones/req_crear"><img class='icon' src="img/Box Add.png" />Requisición</a></li>
  <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_requisiciones/req_reception/0"><img class='icon' src="img/Box Down.png" />Recepción</a></li>
<!-- 
  <li><a href="<?PHP ECHO URL; ?>index.php?url=ges_consignaciones/con_crear"><img class='icon' src="img/Consignacion.png" />Consignaciones</a></li>

   <li><a tabindex="0"  href="<?PHP ECHO URL; ?>index.php?url=ges_ventas/ges_sal_merc"><img class='icon' src="img/Box Up.png" />Salida de mercancia</a></li>  -->
</ul>
<?php } ?>

<!-- <li class="dropdown">
        <a tabindex="0"  data-toggle="dropdown" data-submenu="" aria-expanded="false">
          <img class='icon' src="img/Stock.png" />Gestion de Almacen<span class="caret"></span>
        </a>

<ul class="dropdown-menu">

  <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_ubicaciones/location"><img class='icon' src="img/Maps.png" />Ubicaciones</a></li>

</ul> -->



<!--MODULO DE REPORTES-->
<li class="dropdown">
        <a tabindex="0" data-toggle="dropdown" data-submenu="">
         <img class='icon' src="img/Chart Pie.png" />Reportes<span class="caret"></span>
        </a>

  <ul class="dropdown-menu" > 
  <!-- <li ><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_ventas/ges_hist_ventas"><img class='icon' src="img/News.png" />Historial de ordenes</a></li>
    <li ><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_ventas/ges_hist_sal_merc"><img class='icon' src="img/News.png" />Historial salida de mercancia</a></li> 
    <li ><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_ventas/ges_pro_hist_ventas"><img class='icon' src="img/invoice.png" />Facturas de ventas</a></li>  
    <li class="divider"></li>-->
    <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_reportes/rep_reportes"><img class='icon' src="img/List.png" />Visualizar reportes</a></li> 
  </ul>
</li>

<!--MODULO DE GESTION DE PROYECTOS (JOBS Y PHASES)-->
<?php if($this->model->active_user_role=='admin'){?>
<li class="dropdown">
        <a tabindex="0" data-toggle="dropdown" data-submenu="">
         <img class='icon' src="img/Document Blueprint.png" />Proyectos<span class="caret"></span>
        </a>

  <ul class="dropdown-menu" > 
 
    <li ><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_proyectos/ges_proyecto"><img class='icon' src="img/Document Checklist.png" />Proyectos</a></li>
    <li><a tabindex="0" href="<?PHP ECHO URL; ?>index.php?url=ges_fases/ges_fase"><img class='icon' src="img/Document Checklist.png" />Fases</a></li> 
  </ul>
<?php } ?>




</ul>


<ul class="nav navbar-nav navbar-right">
   <li class="dropdown">
        <a tabindex="0" data-toggle="dropdown" data-submenu="" aria-expanded="false">
        <img class='icon' src="img/options.png" />Opciones<span class="caret"></span>
        </a>

<ul class="dropdown-menu">
  
<li><a tabindex="0" title="Ir al perfil de usuario"  href="<?PHP ECHO URL; ?>index.php?url=home/edit_account/<?php echo $this->model->active_user_id; ?>"><img class='icon' src="img/Contact.png" /><?php echo $this->model->active_user_name.' '.$this->model->active_user_lastname; ?>&nbsp;&nbsp;</a></li>

<?php if($this->model->active_user_role=='admin'){?>
<li><a tabindex="0" title="Administrar Usuarios" href="<?PHP ECHO URL; ?>index.php?url=home/accounts" ><img class='icon' src="img/Users.png" />Usuarios</a></li>
<li><a tabindex="0" title="Configuracion"  href="<?PHP ECHO URL; ?>index.php?url=home/config_sys" ><img  class='icon' src="img/Cog.png" />Configuracion</a></li>
<?php } ?>
         
<li class="divider"></li>

<li><a  title="Salir del sistema" href="<?PHP ECHO URL; ?>index.php?url=login/login_out/" ><img  class='icon' src="img/Shut.png" />Salir</a></li>

</ul>
      </li>
    </ul>
  </div>
</nav>

</div>