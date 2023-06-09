<?php require APPROOT . '/views/inc/header.php';?>
<body class="hold-transition login-page">
<div class="login-box">
<?php flash('user_msg');?>
  <!-- /.login-logo -->
  <div class="card card-outline card-navy">
    <div class="card-header text-center">
      <a href="https://pceajujafarmparish.or.ke" target="_blank" class="h2"><b><?php echo SITENAME;?></b></a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="<?php echo URLROOT;?>/users/login" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control form-control-sm
                <?php echo (!empty($data['userid_err'])) ? 'is-invalid' : ''?>"
                 name="userid" 
                 value="<?php echo $data['userid'];?>"
                 placeholder="User ID">
          <span class="invalid-feedback"><?php echo $data['userid_err'];?></span>       
          
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" 
                 class="form-control form-control-sm 
                 <?php echo (!empty($data['password_err'])) ? 'is-invalid' : ''?>"
                 placeholder="Password">
          <span class="invalid-feedback"><?php echo $data['password_err'];?></span>       
          
        </div>
        <div class="input-group mb-3">
          <select class="form-control form-control-sm" name="congregation">
              <?php foreach($data['congregations'] as $congregation) : ?>
                    <option value="<?php echo $congregation->ID;?>"><?php echo $congregation->CongregationName;?></option>
              <?php endforeach; ?>  
          </select>    
          
        </div>
        <div class="row">
          <!-- <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div> -->
          <!-- /.col -->
          <div class="col-4 mb-2">
            <button type="submit" class="btn bg-navy btn-block ">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <p class="mb-1">
        <a href="<?php echo URLROOT;?>/users/forgotpassword">I forgot my password</a>
      </p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?php echo URLROOT;?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?php echo URLROOT;?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo URLROOT;?>/dist/js/adminlte.min.js"></script>
</body>
</html>