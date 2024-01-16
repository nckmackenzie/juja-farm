<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
 <!-- Content Wrapper. Contains page content -->
 <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <a href="<?php echo URLROOT;?>/groups" class="btn btn-dark btn-sm mt-2"><i class="fas fa-backward"></i> Back</a>
        <div class="row mt-4">
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
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card card-body bg-light mt-5">
                    <h5>Create Group</h5>
                <hr>
                    <form action="<?php echo URLROOT;?>/groups/create" method="post">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="groupName">Group Name</label>
                                    <input type="text" class="form-control mandatory form-control-sm
                                    <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''?>"
                                        id="groupName" 
                                        value="<?php echo $data['name'];?>"
                                        name="groupname"
                                        placeholder="Enter Group Name eg PCMF"
                                        autocomplete="off">
                                </div>    
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="chairuserid">Group Chair UserID</label>
                                <input type="text" class="form-control mandatory form-control-sm
                                    <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''?>"
                                        id="chairuserid" 
                                        value="<?php echo $data['chairuserid'];?>"
                                        name="chairuserid"
                                        placeholder="eg pcfmchair"
                                        autocomplete="off">
                            </div>
                            <div class="col-sm-4">
                                <label for="treasureruserid">Treasurer UserID</label>
                                <input type="text" class="form-control mandatory form-control-sm
                                    <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''?>"
                                        id="treasureruserid" 
                                        value="<?php echo $data['treasureruserid'];?>"
                                        name="treasureruserid"
                                        placeholder="eg pcfmtreasurer"
                                        autocomplete="off">
                            </div>
                            <div class="col-sm-4">
                                <label for="secretaryuserid">Secretary UserID</label>
                                <input type="text" class="form-control mandatory form-control-sm
                                    <?php echo (!empty($data['name_err'])) ? 'is-invalid' : ''?>"
                                        id="secretaryuserid" 
                                        value="<?php echo $data['secretaryuserid'];?>"
                                        name="secretaryuserid"
                                        placeholder="eg pcmfsec"
                                        autocomplete="off">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-12">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="active" id="active" checked>
                                    <label class="form-check-label" for="active">Active</label>
                                </div>
                            </div>        
                        </div>
                        <div class="row">
                            <div class="col-md-2 mt-2">
                                <input type="hidden" name="isedit" value="<?php echo $data['isedit'];?>">
                                <input type="hidden" name="id" value="<?php echo $data['id'];?>">
                                <button type="submit" class="btn btn-block btn-sm bg-navy custom-font">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<?php require APPROOT . '/views/inc/footer.php'?>
</body>
</html>