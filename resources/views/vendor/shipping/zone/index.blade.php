@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')

@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Shipping Zones</li>
    </ol>
</nav>
<div class="card p-4 mt-4">
    <div class="box-header with-border">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <h3 class="box-title" style="display: inline-block;"><i class="fa fa-truck"></i> Shipping Zones</h3>
            <div class="box-tools pull-right" style="display: inline-block;">
                <a href="{{route('vendor.zones.add_new')}}" class="btn btn-primary">Add Zones</a>
            </div>
        </div>
    </div>
    <hr>
    <table id="rate_table" class="table table-striped dt-responsive nowrap w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Zone Name</th>
                <th>Tax</th>
                <th>Country </th>
                <th>State </th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
</div>

<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2">Warning</h4>
                    <p class="mt-3">Are you sure you want to delete</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
@endsection


@section('script')
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap-switch-button@1.1.0/dist/bootstrap-switch-button.min.js"></script>
<script>
    $('body').on("click", ".deleteTypes", function(e) {
          var id = $(this).data('id');
          var name = $(this).data('name');
          let fd = new FormData();
          fd.append('id', id);
          fd.append('_token', '{{ csrf_token() }}');
          $("#warning-alert-modal-text").text(name);
          $('#warning-alert-modal').modal('show');
          $('#warning-alert-modal').on('click', '.btn', function() {
              $.ajax({
                      url: "{{route('vendor.carrier.shipping_zone_delete')}}",
                      type: 'POST',
                      data: fd,
                      dataType: "JSON",
                      contentType: false,
                      processData: false,
                  })
                  .done(function(result) {
                      if (result.status) {
                          $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                          setTimeout(function() {
                              window.location.href = result.location;
                          }, 1000);
                      } else {
                          $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
                      }
                  })
                  .fail(function(jqXHR, exception) {
                      console.log(jqXHR.responseText);
                  });
          });
      });
</script>
<script>
    $(function() {
        $.fn.tableload = function() {
            $('#rate_table').dataTable({
                "scrollX": true,
                "processing": true,
                pageLength: 10,
                "serverSide": true,
                "bDestroy": true,
                'checkboxes': {
                    'selectRow': true
                },
                "ajax": {
                    url: "{{ route('vendor.zones.list_zones') }}",
                    "type": "POST",
                    "data": function(d) {
                        d._token = "{{ csrf_token() }}";
                    },
                    dataFilter: function(data) {
                        var json = jQuery.parseJSON(data);
                        json.recordsTotal = json.recordsTotal;
                        json.recordsFiltered = json.recordsFiltered;
                        json.data = json.data;

                        // console.log(json.data);
                        return JSON.stringify(json);
                    }
                },
                "order": [
                    [0, 'DESC']
                ],
                "columns": [{
                        "width": "2%",
                        "targets": 0,
                        "name": "S_no",
                        'searchable': true,
                        'orderable': true
                    },
                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "zone_name",
                        'searchable': true,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "tax",
                        'searchable': false,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "country",
                        'searchable': false,
                        'orderable': true
                    },

                    {
                        "width": "10%",
                        "targets": 1,
                        "name": "state",
                        'searchable': false,
                        'orderable': true
                    },


                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "created_by",
                        'searchable': true,
                        'orderable': true
                    },
                  
                    {
                        "width": "10%",
                        "targets": 2,
                        "name": "state",
                        'searchable': true,
                        'orderable': true
                    }
                ]
            });
        };

        $.fn.tableload();
     
     

    });
</script>
    
<script>


 $('body').on('click', '.ChangeStatus', function(e) {
    e.preventDefault();

    var id = $(this).attr('data-id');
    var currentValue = $(this).attr('my-value'); // Get the current value of my-value attribute

    if (currentValue === '1') {
        $(this).attr('my-value', '0'); 
        $('#switch2_' + id).prop('checked', true); // Check the checkbox
    } else {
        $(this).attr('my-value', '1'); 
        $('#switch2_' + id).prop('checked', false); 
    }
    var id = $(this).attr('data-id');
    let fd = new FormData();
    fd.append('_token', "{{ csrf_token() }}");
    fd.append('id', id);

    $.ajax({
        url: "{{ route('vendor.carrier.zone_status_update_data') }}",
        type: "POST",
        data: fd,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function() {
            $("#load").show();
        },
        success: function(result) {
            if (result.status) {
                $.NotificationApp.send("Success", result.msg, "top-right", "rgba(0,0,0,0.2)", "success");
                
            } else {
                $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)", "error");
            }
        },
        complete: function() {
            $("#load").hide();
        },
        error: function(jqXHR, exception) {
            console.log(jqXHR.responseText);
        }
    });
});

</script>
@endsection