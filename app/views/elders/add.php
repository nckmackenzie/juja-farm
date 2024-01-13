<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <a href="<?php echo URLROOT;?>/elders" class="btn btn-dark btn-sm mt-2"><i class="fas fa-backward"></i> Back</a>
          </div>
          <div class="col-sm-6"></div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <?php if(count($data['errmsg']) > 0) : ?>
                    <div class="alert custom-danger alert-dismissible fade show" role="alert">
                        <?php foreach($data['errmsg'] as $msg): ?>
                            <p style="margin:0;margin-bottom:4px; font-weight:bold">- <?php echo $msg;?></p>
                        <?php endforeach; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif;?>
                <div class="card card-light">
                    <div class="card-header"><?php echo $data['title'];?></div>
                    <div class="card-body">
                        <form action="<?php echo URLROOT;?>/elders/createupdate" autocomplete="off" method="post" name="form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Elder Name</label>
                                        <input type="text" name="name" id="name" 
                                               class="form-control form-control-sm mandatory"
                                               value="<?php echo $data['name'];?>" placeholder="enter elder name...">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="congregation">Congregation</label>
                                        <select name="congregation" id="congregation" class="form-control form-control-sm mandatory">
                                            <option value="">Select congregation</option>
                                            <?php foreach($data['congregations'] as $congregation) : ?>
                                                <option value="<?php echo $congregation->ID;?>" <?php selectdCheck($data['congregation'],$congregation->ID);?>><?php echo ucwords($congregation->CongregationName);?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="district">District</label>
                                        <select name="district" id="district" class="form-control form-control-sm mandatory">
                                            <option value="" disabled>Select district</option>
                                            <?php if($data['isedit']) : ?>
                                                <?php foreach($data['districts'] as $district) : ?>
                                                    <option value="<?php echo $district->ID;?>" <?php selectdCheck($data['district'],$district->ID);?>><?php echo $district->fieldName;?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Role</label>
                                        <select name="role" id="role" class="form-control form-control-sm mandatory">
                                            <option value="">Select role</option>
                                            <?php foreach($data['roles'] as $role) : ?>
                                                <option value="<?php echo $role->ID;?>" <?php selectdCheck($data['role'],$role->ID);?>><?php echo ucwords($role->RoleName);?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Starting Date</label>
                                        <input type="date" name="date" id="date" 
                                               class="form-control form-control-sm mandatory"
                                               value="<?php echo $data['date'];?>">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact">Contact</label>
                                        <input type="text" name="contact" id="contact" 
                                               class="form-control form-control-sm mandatory"
                                               value="<?php echo $data['contact'];?>" maxlength="10">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 mt-2">
                                    <input type="hidden" name="id" value="<?php echo $data['id'];?>">
                                    <?php if($data['isedit']) : ?>
                                        <input type="hidden" name="memberid" value="<?php echo $data['memberid'];?>">
                                        <input type="hidden" name="userid" value="<?php echo $data['userid'];?>">
                                    <?php endif; ?>
                                    <input type="hidden" name="isedit" value="<?php echo $data['isedit'];?>">
                                    <button type="submit" class="btn btn-block btn-sm bg-navy custom-font">Save</button>
                                </div>
                            </div>   
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php require APPROOT . '/views/inc/footer.php'?>
<script type="module" src="<?php echo URLROOT;?>/dist/js/pages/elders/index.js"></script>
</body>
</html>  