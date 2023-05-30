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
                        <form action="<?php echo URLROOT;?>/elders/createtransfer" autocomplete="off" method="post" name="form">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="name">Elder Name</label>
                                        <input type="text" name="name" id="name" 
                                               class="form-control form-control-sm"
                                               value="<?php echo $data['name'];?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fromcongregation">From Congregation</label>
                                        <input type="text" name="fromcongregation" id="fromcongregation" 
                                               class="form-control form-control-sm"
                                               value="<?php echo $data['oldcongregationname'];?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="fromdistrict">From District</label>
                                        <input type="text" name="fromdistrict" id="fromdistrict" 
                                               class="form-control form-control-sm"
                                               value="<?php echo $data['olddistrictname'];?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="congregation">New Congregation</label>
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
                                        <label for="district">New District</label>
                                        <select name="district" id="district" class="form-control form-control-sm mandatory">
                                            <option value="">Select district</option>
                                        </select>
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Transfer Date</label>
                                        <input type="date" name="date" id="date" 
                                               class="form-control form-control-sm mandatory"
                                               value="<?php echo $data['date'];?>">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reason">Reason</label>
                                        <input type="text" name="reason" id="reason" 
                                               class="form-control form-control-sm mandatory"
                                               value="<?php echo $data['reason'];?>"
                                               placeholder="Reason for transfer...">
                                        <span class="invalid-feedback"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2 mt-2">
                                    <input type="hidden" name="elderid" value="<?php echo $data['elderid'];?>">
                                    <input type="hidden" name="oldcongregation" value="<?php echo $data['oldcongregation'];?>">
                                    <input type="hidden" name="olddistrict" id="olddistrict" value="<?php echo $data['olddistrict'];?>">
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
<script type="module" src="<?php echo URLROOT;?>/dist/js/pages/elders/transfer.js"></script>
</body>
</html>  