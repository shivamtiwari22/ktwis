@extends('vendor.layout.app')

@section('meta_tags')
@endsection


@section('title')

    <head>
        <link rel="stylesheet" href="path-to-fontawesome/css/all.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    </head>
@endsection


@section('css')
    <style>
        .icon-x {
            margin-top: -9%l
        }

        .border_button {
            border-radius: 37%;
            background-color: #777;
        }

        .modal-dialog {
    max-width: 700px;
    margin: 1.75rem auto;
    position: center;
    margin-top:6%;
}   



.my-data {
    max-width: 350px;
    margin: 1.75rem auto;
    position: center;
    margin-top: 6%;}
    </style>
@endsection

@section('main_content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('vendor.dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"> Shipping Zones</li>
    </ol>
</nav>
    <div class="card p-4 mt-2">
        <div class="box-header with-border">
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <h3 class="box-title" style="display: inline-block;"><i class="fa fa-truck"></i> Shipping Zones</h3>
                <div class="box-tools pull-right" style="display: inline-block;">
                    {{-- <a href="{{ route('vendor.zones.add_new') }}" class="btn btn-primary">Add Zones</a> --}}
                </div>
            </div>
        </div>
        <hr>
        <div class="col-xs-12">
            <span class="lead text-muted indent10"><i class="fa fa-map-marker"></i> Domestic</span>
            <span class="indent50">- No tax -</span>
            <div class="pull-right" style="text-align: right;">
                <div class="btn-group">
                
                    <button type="button" class="btn btn-light" data-toggle="modal" data-target="#exampleModalCenter">
                        <i class="fa fa-plus-square-o"></i>
                        Add rate 
                      </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="indent10">Countries</label>
                </div>
                <ul class="list-group">

                    <li class="list-group-item">

                        <h4 class="list-group-item-heading inline">
                            <img src="https://www.zcart.incevio.cloud/images/flags/BD.png" alt="BD"> <span
                                class="indent5">Bangladesh</span>
                        </h4>

                        <a type="submit" class="confirm ajax-silent small text-muted pull-right icon-x" title="Delete"
                            data-toggle="tooltip" data-placement="top"
                            style="
                                margin-top: -9%;
                            "><i
                                class="fa fa-times-circle"></i></a>



                        <p class="list-group-item-text">
                            <span class="indent40">
                                8 of 8 states
                            </span>
                            <small class="pull-right">
                                <a data-link="https://www.zcart.incevio.cloud/admin/shipping/shippingZone/1/editStates/840"
                                    class="ajax-modal-btn" style="cursor: pointer;"><i class="fa fa-edit"></i> Edit</a>
                            </small>

                        </p>

                    </li>

                </ul>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="indent10">Shipping rates</label>
                </div>
                <ul class="list-group">
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading">
                            itaque

                            <small class="indent20"> By FedEx And takes 9-21 days </small>

                            <a type="submit" class="confirm ajax-silent small text-muted pull-right icon-x" title="Delete"
                                data-toggle="tooltip" data-placement="top" id="delete_attri"
                                style="
                                margin-top: 0%;
                            "><i
                                    class="fa fa-times-circle"></i></a>

                        </h4>

                        <p class="list-group-item-text">
                            0 g - 2,000 g
                            <span class="btn btn-secondary border_button btn-sm">
                                $51.00
                            </span>
                            <small class="pull-right">
                                <a data-link="https://www.zcart.incevio.cloud/admin/shipping/shippingZone/1/editStates/840"
                                    class="ajax-modal-btn" style="cursor: pointer;"><i class="fa fa-edit"></i> Edit</a>
                            </small>
                        </p>

                    </li>



                </ul>
                <ul class="list-group">
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading">
                            itaque

                            <small class="indent20"> By FedEx And takes 9-21 days </small>

                            <a type="submit" class="confirm ajax-silent small text-muted pull-right icon-x" title="Delete"
                                data-toggle="tooltip" data-placement="top" id="delete_attri"
                                style="
                                    margin-top: 0%;
                                "><i
                                    class="fa fa-times-circle"></i></a>
                        </h4>

                        <p class="list-group-item-text">
                            0 g - 2,000 g
                            <span class="btn btn-secondary border_button btn-sm ">
                                $51.00
                            </span>
                            <small class="pull-right">
                                <a data-link="https://www.zcart.incevio.cloud/admin/shipping/shippingZone/1/editStates/840"
                                    class="ajax-modal-btn" style="cursor: pointer;"><i class="fa fa-edit"></i> Edit</a>
                            </small>
                        </p>

                    </li>



                </ul>
            </div>

        </div>


        {{-- <hr class="style3"> --}}
    </div>



    </div>

    {{-- edit modal --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="name" class="with-help">Name</label>
                               
                                <input class="form-control" placeholder="Name" required="" name="name"
                                    type="text" value="Domestic" id="name">
                                <div class="help-block with-errors">Customer will not see this</div>
                            </div>

                        </div>
                    
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="with-help">Shipping Carrier</label>
                               
                                    <select name="shipping" class="form-control" id="customerDropdown">
                                        <option value="">Select Shipping Carrier</option>
        
                                        <option value="DHL">DHL</option>
                                        <option value="UHL">UHL</option>
                                    </select>
                                </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="nopadding-left">
                                <div class="form-group">
                                    <label for="active" class="with-help"> 
                                     Delivery Takes</label>
                                     <input class="form-control" placeholder="2-5 day" required="" name="delivery"
                                     type="text"  id="delivery">                                   
                                      <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="with-help">Minimum Order Price</label>
                            
                                <input class="form-control" placeholder="" required="" name="minimum_order"
                                    type="number" value="Domestic" id="minimum_order">
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="nopadding-left">
                                <div class="form-group">
                                    <label for="active" class="with-help"> 
                            
                                          Maximum Order Price</label>
                                          <input class="form-control" placeholder="" required="" name="maximun_order"
                                          type="number" value="Domestic"  id="maximun_order">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="name" class="with-help">Rate</label>
                             
                                <input class="form-control" placeholder="" required="" name="rate"
                                    type="number" value="Domestic" id="rate">
                            </div>

                        </div>
                        <div class="col-sm-6">
                            <div class="nopadding-left">
                                <div class="form-group">
                                    <label for="active" class="with-help"> 
                            
                                          </label>
                                          <input class="form-control" placeholder="" required="" name="maximun_order"
                                          type="number" value="Domestic"  id="maximun_order">
                                    <div class="help-block with-errors"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- add Rate --}}
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">FORM</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name" class="with-help">Name*</label>
                        {{-- <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="Give a meaningful name. Customer will see this name at checkout. e. g. 'standard shipping'"></i> --}}
                        <input class="form-control" placeholder="Name" required="" name="name" type="text" id="name">
                        <div class="help-block with-errors">Customers will see this</div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-sm-6"> <div class="form-group">
                            <label for="active" class="with-help">Status*</label>
                            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="Give a meaningful name. Customer will see this name at checkout. e. g. 'standard shipping'"></i>

                            <select name="category" class="form-control" id="customerDropdown">
                                <option value="">Select Category </option>

                                <option value="">Active</option>
                                <option value="">INActive</option>
                            </select>
                            <div class="help-block with-errors"></div>
                        </div></div>
                        <div class="col-sm-6">
                            <label for="">Delivery Takes *</label>
                            
                            <input class="form-control" placeholder="2-3 days" required="" name="name" type="text" id="name">            
                                    </div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-sm-6">
                            <label for="">Minimun Order Weight *</label>
                            <input class="form-control" placeholder="0" required="" name="name" type="number" id="name">            
                                    </div>
                        <div class="col-sm-6">
                            <label for="">Maximum Order Weight *</label>
                            <input class="form-control" placeholder="100" required="" name="name" type="number" id="name">            
                                    </div>
                      </div>
                      <div class="row mt-2">
                        <div class="col-sm-6">
                            <label for="">Rate *</label>
                            <input class="form-control" placeholder="0" required="" name="name" type="number" id="name">            
                                    </div>
                        <div class="col-sm-6">
                            <label for=""></label>
                            <select name="category" class="form-control" id="customerDropdown">
                                <option value="">Select Shipping </option>

                                <option value="">Free shipping</option>
                                <option value="">No Free shipping</option>
                            </select>          
                                    </div>
                      </div>
                      <p class="help-block mt-2">* Required fields.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm my-data">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Confirm</h4>
                        <p class="mt-3">Are you sure you want to delete this Data?</p>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger my-2" id="confirm-delete">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
    <script src="path-to-fontawesome/js/all.js"></script>
    <script>
        $('body').on("click", "#delete_attri", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('name');
    
            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');
    
            $("#warning-alert-modal-text").text(name);
            $('#warning-alert-modal').modal('show');
    
            $('#confirm-delete').on('click', function() {
                $.ajax({
                        url: "#",
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
                            $.fn.tableload();
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
@endsection
