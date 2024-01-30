<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
<div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Delete Membership</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <form action="<?php echo URLROOT;?>/groups/deletemembership" method="post">
              <div class="row">
                <div class="col-md-12">
                  <label for="">Are You Sure You Want To Delete Selected Membership?</label>
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
<div class="modal fade" id="addModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add Group Membership</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <div id="alertBox" class="d-none">
            <div class="alert custom-danger alert-dismissible fade show" role="alert">
                <div class="error-message"></div>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
          </div>
          <form action="<?php echo URLROOT;?>/groups/addmembership" method="post" class="add-membership">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="">Group</label>
                    <select class="form-control" name="group" id="group">
                        <option value="" selected disabled>Select group...</option>
                        <?php foreach($data['groups'] as $group) : ?>
                            <option value="<?php echo $group->ID;?>"><?php echo $group->groupName;?></option>
                        <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="">Member</label>
                        <select class="form-control" name="members[]" id="members" multiple>
                            
                        </select>
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn bg-navy custom-font btnsave">Add Membership(s)</button>
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
      <?php flash('groupmember_msg');?>
        <div class="row mb-2">
          <div class="col-sm-6">
           <button class="btn btn-sm btn-success">Add New Membership</button>
          </div>
          <div class="col-sm-6">
           <Select class="form-control form-control-sm" id="filter">
                <option value="">Filter by group...</option>
           </Select>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12 table-responsive">
                <table id="membersTable" class="table table-bordered table-striped table-sm">
                    <thead class="bg-navy">
                        <tr>
                            <th>ID</th>
                            <th>Member Name</th>
                            <th>Group</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($data['members'] as $member) : ?>
                            <tr>
                                <td><?php echo $member->ID;?></td> 
                                <td><?php echo $member->MemberName;?></td> 
                                <td><?php echo $member->GroupName;?></td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-danger custom-font btn-sm btndel">Remove</button>
                                    </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/1.1.2/js/bootstrap-multiselect.min.js"></script>
<script type="module" src="<?php echo URLROOT; ?>/dist/js/pages/groups/index.js"></script>
<script>
  $(function(){
      var urlParams = new URLSearchParams(window.location.search);
      var isRedirect = !!urlParams.get('redirect') || false; 
      var pageNumber = localStorage.getItem('membershipPage')
       
      var table = $('#membersTable').DataTable({
          'pageLength' : 25,
          'ordering' : false,
          "responsive": true,
          'columnDefs' : [
            { "visible" : false, "targets": 0},
            {"width" : "10%" , "targets": 3}
          ],
          "initComplete": function () {
          this.api().columns([2]).every(function () {
            var column = this;
            var select = $('#filter');

            select.on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex(
                    $(this).val()
                );
                column.search(val ? '^' + val + '$' : '', true, false).draw();
            });

            column.data().unique().sort().each(function (d, j) {
                select.append('<option value="' + d + '">' + d + '</option>')
            });
          });
        }
      });

      if(isRedirect && pageNumber){
        table.page(parseInt()).draw( 'page' );
      }

      $(document).on('click', '#membersTable_next', function () {
        localStorage.setItem('membershipPage',table.page.info().page)
      });

      $('#membersTable').on('click','.btndel',function(){
          $('#deleteModalCenter').modal('show');
          $tr = $(this).closest('tr');

          let data = $tr.children('td').map(function(){
              return $(this).text();
          }).get();
          
          $('#membername').val(data[0]);
          var currentRow = $(this).closest("tr");
          var data1 = $('#membersTable').DataTable().row(currentRow).data();
          $('#id').val(data1[0]);
      }); 
  });
</script>
</body>
</html>  