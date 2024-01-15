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
            <a href="<?php echo URLROOT;?>/deacons" class="btn btn-dark btn-sm mt-2"><i class="fas fa-backward"></i> Back</a>
          </div>
          <div class="col-sm-6"></div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <?php if(count($data['errors']) > 0) : ?>
                    <div class="alert custom-danger alert-dismissible fade show" role="alert" id="alertBox">
                        <?php foreach($data['errors'] as $msg): ?>
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
                        <form action="<?php echo URLROOT;?>/deacons/createupdate" autocomplete="off" method="post" name="form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="district">District</label>
                                        <select name="district" id="district" class="form-control form-control-sm mandatory">
                                            <option value="">Select district</option>
                                            <?php foreach($data['districts'] as $district) : ?>
                                                <option value="<?php echo $district->ID;?>" <?php selectdCheck($data['district'],$district->ID);?>><?php echo ucwords($district->districtName);?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="deacon">Deacon Name</label>
                                        <select name="deacon" id="deacon" class="form-control form-control-sm mandatory">
                                            <option value="">Select deacon</option>
                                            <?php if(count($data['members']) > 0) : ?>
                                                <?php foreach($data['members'] as $member) : ?>
                                                    <option value="<?php echo $member->ID;?>" <?php selectdCheck($data['deacon'],$member->ID);?>><?php echo ucwords($member->memberName);?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="year">Year</label>
                                        <select name="year" id="year" class="form-control form-control-sm mandatory">
                                            <option value="" disabled>Select year</option>
                                            <?php foreach($data['years'] as $year) : ?>
                                                <option value="<?php echo $year->ID;?>" <?php selectdCheck($data['year'],$year->ID);?>><?php echo $year->yearName;?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="zone">Zone</label>
                                        <input type="text" name="zone" id="zone" 
                                               class="form-control form-control-sm"
                                               value="<?php echo $data['zone'];?>"
                                               placeholder="Enter zone if any...">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="zone">Role</label>
                                    <div class="col-sm-6 d-flex align-items-center" style="gap: 2rem;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" value="none"  <?php echo $data['role'] === 'none' ? 'checked' : '';?>>
                                            <label class="form-check-label">None</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" value="treasurer" <?php echo $data['role'] === 'treasurer' ? 'checked' : '';?>>
                                            <label class="form-check-label">Treasurer</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="role" value="secretary" <?php echo $data['role'] === 'secretary' ? 'checked' : '';?>>
                                            <label class="form-check-label">Secretary</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 mt-2">
                                    <input type="hidden" name="id" value="<?php echo $data['id'];?>">
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
<script type="module" src="<?php echo URLROOT;?>/dist/js/pages/deacons/index.js"></script>
</body>
</html>  