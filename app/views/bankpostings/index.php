<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
<div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Delete Posting</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="<?php echo URLROOT;?>/bankpostings/delete" method="post">
            <div class="row">
            <div class="col-md-9">
                <label for="">Delete Selected Posting?</label>
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
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
      <?php flash('postings_msg');?>
        <div class="row mb-2">
          <div class="col-sm-6">
            <a href="<?php echo URLROOT;?>/bankpostings/add" class="btn btn-sm btn-success custom-font">Add New</a>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered table-striped table-sm" id="postingsTable">
                    <thead class="bg-navy">
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Bank</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <?php if($_SESSION['userType'] <= 2) : ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['postings'] as $posting) : ?>
                            <tr>
                                <td><?php echo $posting->ID;?></td>
                                <td><?php echo date('d-m-Y',strtotime($posting->transactionDate));?></td>
                                <td><?php echo $posting->Bank;?></td>
                                <td><?php echo number_format($posting->Amount,2);?></td>
                                <td><?php echo $posting->reference;?></td>
                                <?php if($_SESSION['userType'] <= 2 || $_SESSION['userType'] === 6) : ?>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?php echo URLROOT;?>/bankpostings/edit/<?php echo encryptId($posting->ID)?>" class="btn btn-sm bg-olive custom-font">Edit</a>
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
        var table = $('#postingsTable').DataTable({
            'ordering' : false,
            'columnDefs' : [
                {"visible" : false, "targets": 0},
                {"width" : "15%" , "targets": 1},
                {"width" : "15%" , "targets": 3},
                {"width" : "15%" , "targets": 4}
                <?php if ($_SESSION['userType'] <= 2 || $_SESSION['userType'] === 6) : ?>
                ,{"width" : "15%" , "targets": 5},
                <?php endif;?>
            ]
        });
        $('#postingsTable').on('click','.btndel',function(){
          $('#deleteModalCenter').modal('show');
          $tr = $(this).closest('tr');

          let data = $tr.children('td').map(function(){
              return $(this).text();
          }).get();
          
         
          var currentRow = $(this).closest("tr");
          var data1 = $('#postingsTable').DataTable().row(currentRow).data();
          $('#id').val(data1[0]);
      });
    });
</script>
</body>
</html> 