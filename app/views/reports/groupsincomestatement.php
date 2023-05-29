<?php require APPROOT . '/views/inc/header.php';?>
<?php require APPROOT . '/views/inc/topNav.php';?>
<?php require APPROOT . '/views/inc/sideNav.php';?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-9 mx-auto mt-2">
                <div class="card bg-light">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="group">Group</label>
                                   <select name="group" id="group" class="form-control form-control-sm">
                                     <option value="" selected>Select group</option>
                                     <?php foreach($data['groups'] as $group) : ?>
                                        <option value="<?php echo $group->ID;?>"><?php echo $group->groupName;?></option>
                                     <?php endforeach; ?>
                                   </select>
                                    <span class="invalid-feedback" id="group_err"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="start">Start Date</label>
                                    <input type="date" name="start" id="start" class="form-control form-control-sm">
                                    <span class="invalid-feedback" id="start_err"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="end">End Date</label>
                                    <input type="date" name="end" id="end" class="form-control form-control-sm">
                                    <span class="invalid-feedback" id="end_err"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <button class="btn btn-sm btn-primary custom-font" id="preview">Preview</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- data -->
        <div class="row">
            <div class="col-md-9 mx-auto">
                <div id="results" class="table-responsive">
                        
                </div>
            </div>
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<?php require APPROOT . '/views/inc/footer.php'?>
<script>
    $(function(){
        $('#preview').click(function(){
            var table = $('#table').DataTable();
            //validate'
            var start = $('#start').val();
            var end = $('#end').val();
            var group = $('#group').val();
            var start_err = '';
            var end_err = '';
            var group_err = '';

            if($('#start').val() == ''){
                start_err = 'Select Start Date';
                $('#start_err').text(start_err);
                $('#start').addClass('is-invalid');
                
            }else{
                start_err = '';
                $('#start_err').text(start_err);
                $('#start').removeClass('is-invalid');
            }

            if($('#end').val() == ''){
                end_err = 'Select End Date';
                $('#end_err').text(end_err);
                $('#end').addClass('is-invalid');
                end = $('#end').val();
            }else{
                end_err = '';
                $('#end_err').text(end_err);
                $('#end').removeClass('is-invalid');
            }

            if($('#group').val() == ''){
                group_err = 'Select group';
                $('#group_err').text(group_err);
                $('#group').addClass('is-invalid');
                group = $('#group').val();
            }else{
                group_err = '';
                $('#group_err').text(group_err);
                $('#group').removeClass('is-invalid');
            }

            if($('#start').val() !== '' && $('#end').val() !== ''){
                if($('#start').val() > $('#end').val()){
                    start_err = 'Start Date Cannot Be Greater Than End Date';
                    $('#start_err').text(start_err);
                    $('#start').addClass('is-invalid');
                }else{
                    start_err = '';
                    $('#start_err').text(start_err);
                    $('#start').removeClass('is-invalid');
                }
            }

            if(start_err !== '' || end_err !== '') return;
            

            $.ajax({
                url : '<?php echo URLROOT;?>/reports/groupincomestatementrpt',
                method : 'GET',
                data : {start : start, end : end,group : group},
                success : function(data){
                    // console.log(data);
                    $('#results').html(data);
                    table.destroy();
                    table = $('#table').DataTable({
                        pageLength : 100,
                        fixedHeader : true,
                        ordering : false,
                        searching : false,
                        "responsive" : true,
                        "buttons": ["excel", "pdf","print"],
                    }).buttons().container().appendTo('#table_wrapper .col-md-6:eq(0)');
                }
            });
        });
    });
</script>
</body>
</html>  