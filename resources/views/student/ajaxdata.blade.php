<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Datatables Server Side Processing</title>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/css/dataTables.bootstrap4.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.16/js/dataTables.bootstrap4.min.js"></script>  
    
    
    {{-- <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js"></script> --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css"> --}}
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script> --}}
    {{-- <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script> --}}

</head>
<body>
    <div class="container">
        <br>
        <h3 align="center">Datatables Server Side Processing in Laravel</h3>
        <br>
        <div align="right">
            <button type="button" name="add" id="add_data" class="btn btn-success btn-sm">Add</button>
        </div>
        <br>
        <table id="student_table" class="table table-bordered" style="width:100%;">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Action</th>
                    <th><button type="button" name="bulk_delete" id="bulk_delete" class="btn btn-danger btn-xs">Delete many</button></th>
                </tr>
            </thead>
        </table>
    </div>

    <div class="modal fade" id="studentModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="" method="post" id="student_form">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                       <h4 class="modal-title">Add Data</h4>
                    </div>
                    <div class="modal-body">
                        @csrf
                        <span id="form_output"></span>
                        <div class="form-group">
                            <label for="">Enter First Name</label>
                            <input type="text" name="first_name" id="first_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Enter Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" name="student_id" id="student_id" value="update" />
                        <input type="hidden" name="button_action" id="button_action" value="insert">
                        <input type="submit" name="submit" id="action" value="Add" class="btn btn-info">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(){
            $('#student_table').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": "{{ route('ajaxdata.getdata') }}",
                "columns": [
                    {"data": "first_name"},
                    {"data": "last_name"},
                    {"data": "action", orderable:false, searchable: false},
                    {"data": "checkbox", orderable:false, searchable:false}
                ]
            });

            $('#add_data').click(function(){
                $("#studentModal").modal();
                $('#student_form')[0].reset();
                $('#form_output').html('');
                $('#button_action').val('insert');
                $('#action').val('Add');
            });
            $('#student_form').on('submit', function(e){
                e.preventDefault();
                var form_data = $(this).serialize();

                $.ajax({
                    url: "{{ route('ajaxdata.postdata') }}",
                    method: "POST",
                    data: form_data,
                    dataType: "json",
                    success: function(data){
                        if(data.error.length > 0 ){
                            var error_html = '';
                            for(var count =0; count < data.error.length; count++){
                                error_html += '<div class="alert alert-danger">' + data.error[count] + '</div>';
                            }
                            $('#form_output').html(error_html);
                        }else{
                            success_html = '<div class="alert alert-success">' + data.success + '</div>';
                            $('#form_output').html(success_html);
                            $('#student_form')[0].reset();
                            $('#action').val('Add');
                            $('.modal-title').text('Add Data');
                            $('#button_action').val('insert');
                            $('#student_table').DataTable().ajax.reload();

                        }
                    }
                });
            });

            $(document).on('click', '.edit', function(){
                var id = $(this).attr("id");
                $.ajax({
                    url: "{{ route('ajaxdata.fetchdata') }}",
                    method: 'get',
                    data: {id:id},
                    dataType: 'json',
                    success: function(data){
                        $('#first_name').val(data.first_name);
                        $('#last_name').val(data.last_name);
                        $('#student_id').val(id);
                        $('#studentModal').modal('show');
                        $('#action').val('Edit');
                        $('.modal-title').text('Edit Data');
                        $('#button_action').val('update');
                    }
                });
            });
            $(document).on('click', '.delete', function(){
                var id = $(this).attr('id');
                if(confirm("Are you sure you want to delete this data?")){
                    $.ajax({
                        url: "{{ route('ajaxdata.removedata') }}",
                        method: "get",
                        data:{id:id},
                        success:function(data){
                            alert(data);
                            $('#student_table').DataTable().ajax.reload();
                        }
                    })
                }else{
                    return false;
                }
            });
             $(document).on('click', '#bulk_delete', function(){
                 var id=[];
                 if(confirm("Are you sure you want to Delete this data?")){
                    //  return true;
                    $('.student_checkbox:checked').each(function(){
                        id.push($(this).val());
                    });
                    if(id.length >0){

                        $.ajax({
                            url: "{{ route('ajaxdata.massremove') }}",
                            method: "get",
                            data:{id:id},
                            success: function(data){
                                alert(data);
                                $('#student_table').DataTable().ajax.reload();

                            }

                        });

                    }else{
                        alert("Please select atleast one checkbox");
                    }
                 }else{
                     return false;
                 }
             });

        });
    </script>
    
</body>
</html>