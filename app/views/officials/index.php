<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
<div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Delete Officials</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="<?php echo URLROOT;?>/officials/delete" method="post">
            <div class="row">
            <div class="col-md-9">
                <label for="">Delete Selected Officials?</label>
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="groupname" id="groupname">
                <input type="hidden" name="yearname" id="yearname">
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger">Yes</button>
            </div>
        </form>
      </div>
     
    </div>
  </div>
</div>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
      <?php flash('official_msg');?>
        <div class="row mb-2">
          <div class="col-sm-6">
            <a href="<?php echo URLROOT;?>/officials/add" class="btn btn-sm btn-success custom-font">Add New</a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered table-striped table-sm" id="officialsTable">
                    <thead class="bg-navy">
                        <tr>
                            <th>ID</th>
                            <th>Year</th>
                            <th>Group</th>
                            <th>Chairman</th>
                            <th>Secretary</th>
                            <th>Treasurer</th>
                            <?php if($_SESSION['userType'] <= 2) : ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['officials'] as $official) : ?>
                            <tr>
                                <td><?php echo $official->ID;?></td>
                                <td><?php echo $official->yearName;?></td>
                                <td><?php echo $official->groupName;?></td>
                                <td><?php echo $official->chairman;?></td>
                                <td><?php echo $official->treasurer;?></td>
                                <td><?php echo $official->secretary;?></td>
                                <?php if($_SESSION['userType'] <= 2) : ?>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo URLROOT;?>/officials/edit/<?php echo $official->ID;?>" class="btn btn-sm bg-olive custom-font">Edit</a>
                                            <button class="btn btn-danger custom-font btn-sm btndel">Delete</button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>  
                    </tbody>
                </table>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php require APPROOT . '/views/inc/footer.php'?>
<script>
    $(function(){
        var table = $('#officialsTable').DataTable({
            'columnDefs' : [
                {"visible" : false, "targets": 0},
                {"width" : "10%" , "targets": 1}
                <?php if ($_SESSION['userType'] <= 2) : ?>
                ,{"width" : "15%" , "targets": 6},
                <?php endif;?>
            ]
        });
        $('#officialsTable').on('click','.btndel',function(){
          $('#deleteModalCenter').modal('show');
          $tr = $(this).closest('tr');

          let data = $tr.children('td').map(function(){
              return $(this).text();
          }).get();
          
          $('#yearname').val(data[0]);
          $('#groupname').val(data[1]);
          var currentRow = $(this).closest("tr");
          var data1 = $('#officialsTable').DataTable().row(currentRow).data();
          $('#id').val(data1[0]);
      });
    });
</script>
</body>
</html> 