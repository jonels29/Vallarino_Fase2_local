<?php

////////////////////////////////
 $check_sage = $this->model->ConSage(); 

if($check_sage=='0'){

echo "<script> alert('SageConnect no se encuentra activo o no esta debidamente conectado al sistema.'); </script> ";

}
////////////////////////////////

if(isset($_REQUEST['flag']))
{

	//inicio variables de session
	$user = $_REQUEST['user'];
	$pass = md5($_REQUEST['pass']);
	$this->model->login_in($user,$pass,$temp_url);

}

if(isset($_GET['user']))
{

	//inicio variables de session
	$user = $_GET['user'];
	$pass = md5($_GET['pass']);
	$this->model->login_in($user,$pass,$temp_url);

}

?>

<div  class="col-lg-3"></div>
<div  class="page login col-lg-5">

			<div class="col-lg-12">
			
			
				<!-- Add class "fade-in-effect" for login form effect -->
				<form action="" method="POST" role="form" id="login" >
					<input type="hidden" name='flag' value="1"/>
                                        
					<div class="col-lg-12 login-header">
					<div class="separador col-lg-12"></div>
						<a href="#" class="logo">
							<center><img src="img/logo.jpg" alt="" width="250" /></center>
							
						</a>
						
						
					</div>
                                        
                       
	               <div class="separador col-lg-12"></div>

					     <div class="col-lg-12">
<div class="form-group col-lg-12">
<h3 class="login_title" >Log in</h3>
</div>
						<div class="form-group col-lg-12">
							<label class="control-label" for="username">Usuario</label>
							<input type="text" class="form-control" id="user" name="user"  autocomplete="off" />
						</div>						
						<div class="form-group col-lg-12">
							<label class="control-label" for="passwd">Password</label>
							<input type="password" class="form-control" name="pass" id="pass" autocomplete="off" />
						</div>

						<div class="separador col-lg-12"></div>

						
						
                        <div class="separador col-lg-12"></div>

						<div class="form-group col-lg-4">
							<button type="submit" class="btn btn-primary  btn-block text-left">
							<i style="color: white;" class="fa fa-lock"></i> Entrar
							</button>
						</div>
						
								
								


					</div>
					
				
					
				</form>
				

			
			
		</div>
		
	</div>

	