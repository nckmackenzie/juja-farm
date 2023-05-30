<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
<div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Delete Request</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="<?php echo URLROOT;?>/elders/delete" method="post">
              <div class="row">
                <div class="col-md-9">
                  <label for="">Are You Sure You Want To Delete Selected elder?</label>
                  <input type="hidden" name="id" id="id">
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
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <?php flash('elder_msg');?>
        <div class="row mb-2">
          <div class="col-sm-6">
            <a href="<?php echo URLROOT;?>/elders/add" class="btn btn-sm btn-success custom-font">Add New</a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-striped table-bordered table-sm" id="elderTable">
                    <thead class="bg-navy">
                        <th class="d-none">ID</th>
                        <th>Elder Name</th>
                        <th>Current Congregation</th>
                        <th>Current District</th>
                        <th>Actions</th>
                    </thead>
                    <tbody>
                        <?php foreach($data['elders'] as $elder) :?>
                            <tr>
                                <td class="d-none"><?php echo $elder->ID;?></td>
                                <td><?php echo $elder->ElderName;?></td>
                                <td><?php echo $elder->Congregation;?></td>
                                <td><?php echo $elder->District;?></td>
                                <td>
                                    <?php if($_SESSION['userType'])  : ?>
                                        <div class="btn-group">
                                            <a href="<?php echo URLROOT;?>/elders/edit/<?php echo $elder->ID;?>" class="btn btn-sm bg-olive custom-font">Edit</a>
                                            <a href="<?php echo URLROOT;?>/elders/transfer/<?php echo $elder->ID;?>" class="btn btn-sm btn-info custom-font">Transfer</a>
                                            <button type="button" class="btn btn-sm btn-danger custom-font btndel">Delete</button>
                                        </div>
                                    <?php endif; ?>
                                </td>     
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
      $('#reqTable').DataTable({
          'ordering' : false,
          'columnDefs' : [
            {"width" : "15%" , "targets": 4}
          ]
      });

      $('#reqTable').on('click','.btndel',function(){
          $('#deleteModalCenter').modal('show');
          $tr = $(this).closest('tr');

          let data = $tr.children('td').map(function(){
              return $(this).text();
          }).get();
          $('#id').val(data[0]);
      });
    });
</script>
</body>
</html>