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
            border-radius: 10px;
            background-color: #777;
            min-width: 10px;
            padding: 3px 7px;
            font-size: 12px;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
        }

        .modal-dialog {
            max-width: 700px;
            margin: 1.75rem auto;
            position: center;
            margin-top: 6%;
        }

        #warning-alert-modal  .modal-dialog {
            max-width: 300px;
        }



        .modal-dialoges {

            margin-top: -4%;
            overflow-y: hidden !important;
        }



        .my-data {
            max-width: 350px;
            margin: 1.75rem auto;
            position: center;
            margin-top: 6%;
        }

        .text-color {}

        .fa {
            font-family: 'FontAwesome';
        }

        .select2-container {
            z-index: 10000;
        }

        .modal-dialog {
            max-width: 545px;
        }

        .no-border {
            height: 32px;
            border: none;
        }

        .icheckbox_line-pink {
            position: relative;
            display: block;
            margin: 0;
            padding: 1px 7px 2px 32px;
            font-size: 13px;
            line-height: 17px;
            color: #fff;
            background: #a77a94;
            border: none;
            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
            cursor: pointer;
        }


        .table>:not(caption)>*>* {
            padding: 0.4rem 0.1rem;
        }
    </style>
@endsection

@section('main_content')
    {{-- delete Model --}}

    <div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm ">
            <div class="modal-content">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="dripicons-warning h1 text-warning"></i>
                        <h4 class="mt-2">Confirm</h4>
                        <p class="mt-3">Are you sure you want to delete this Data?</p>
                        <input type="hidden" id="data-id" name="id">
                        <button type="button" class="btn btn-danger my-2" id="confirm-delete">Confirm</button>
                        <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="{{ route('vendor.zones.index') }}"> Shipping
                    Zones</a></li>
            <li class="breadcrumb-item active" aria-current="page"> Shipping Rates</li>
        </ol>
    </nav>
    <div class="card p-4 mt-1">
        <div class="box-header with-border">
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <h3 class="box-title" style="display: inline-block;"><i class="fa fa-truck"></i> Shipping Zones</h3>
                <div class="box-tools pull-right" style="display: inline-block;">
                    <a href="javascript::void(0)" class="btn btn-secondary" data-toggle="modal" data-target="#zoneModal">Add
                        New Zone</a>
                </div>
            </div>
        </div>
        <hr>

        @foreach ($zone as $item)
            <div class="col-xs-12 mt-2 mb-2">
                <span class="lead text-muted indent10"><i class="fa fa-map-marker" style="font-size: x-large"></i><span class="ms-1" style="">
                        {{ $item->name }}</span> </span>
                <span class="indent50 ms-2"> {{ $item->status == 1 ? "(Active)" : "(Inactive)"}}</span>
                <div class="pull-right" style="text-align: right;">
                    <div class="btn-group">

                        <button type="button" class="btn btn-light btn-sm ms-1 add-rate" data-toggle="modal"
                            data-id="{{ $item->id }}" data-target="#exampleModalCenter">
                            <i class="fa fa-plus-square-o"></i>
                            Add rate
                        </button>

                        <button type="button" class="btn btn-light btn-sm ms-1 " data-toggle="modal"
                            data-target="#zoneEdit_{{ $item->id }}" data-id="{{ $item->id }}">
                            <i class="fa fa-plus-square-o"></i>
                            Add Country
                        </button>

                        <button type="button" class="btn btn-light btn-sm ms-1" data-toggle="modal"
                            data-target="#zoneEdit_{{ $item->id }}" data-id="{{ $item->id }}">
                            <i class="fa fa-edit"></i>
                            Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm ms-1 delete-zone" data-id="{{ $item->id }}">
                            <i class="fa fa-trash"></i>
                            Delete
                        </button>

                        </form>
                    </div>
                </div>
            </div>


            <div class="modal fade" id="zoneEdit_{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby=""
                aria-hidden="true">
                <div class="modal-dialog" role="">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Zone</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="zone_edit">
                            <div class="modal-body">
                                <div class="row mt-2">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name" class="with-help">Name <span
                                                    class="text-danger">*</span></label>
                                            <input class="form-control" placeholder="Name" name="name" type="text"
                                                value="{{ $item->name }}" required id="name">
                                            <input type="hidden" name="id" value="{{ $item->id }}">
                                            <div class="help-block with-errors">Customers will not see this</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="name" class="with-help">Status <span
                                                    class="text-danger">*</span></label>
                                            <select name="status" id="" class="form-select" required>
                                                <option value="" selected disabled>Select</option>
                                                <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>

                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2 mb-2">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="name" class="with-help">Country <span
                                                    class="text-danger">*</span></label>
                                            <select name="country[]" id="" class="select2-multiple"
                                                data-toggle="select2" multiple="multiple" data-placeholder="Select">

                                                @foreach ($countries as $key => $country)
                                                    <option value="{{ $country->id }}"
                                                        {{ in_array($country->id, $item->shippingCountryId) ? 'selected' : '' }}>
                                                        {{ $country->country_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-secondary mt-n1">Save</button>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>
            </div>



            <div class="row mb-3">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="indent10">Countries</label>
                    </div>
                    <ul class="list-group">

                        @if ($item->shipping_countries->count() > 0)
                            @foreach ($item->shipping_countries as $country)
                                <li class="list-group-item">
                                    <h4 class="list-group-item-heading inline" style="margin-top:3px">
                                        {{-- <img src="https://www.zcart.incevio.cloud/images/flags/BD.png" alt="BD"> --}}
                                        <span class="indent5">{{ $country->country_name }}</span>
                                    </h4>

                                    <a href="javascript::void(0)"
                                        class="confirm ajax-silent small text-muted pull-right icon-x delete-country"
                                        title="Delete" data-toggle="tooltip" data-placement="top"
                                        data-id="{{ $country->id }}" style="margin-top: -9%;"><i
                                            class="fa fa-times-circle"></i></a>

                                    <p class="list-group-item-text" style="margin-bottom:0px">
                                        <span class="indent40">
                                            {{ $country->stateCount }} of {{ $country->totalStateCount }} states
                                        </span>
                                        <small class="pull-right">
                                            <a data-link="https://www.zcart.incevio.cloud/admin/shipping/shippingZone/1/editStates/840"
                                                class="ajax-modal-btn edit_state" style="cursor: pointer;"
                                                data-id="{{ $country->id }}"><i class="fa fa-edit"></i>
                                                Edit</a>
                                        </small>
                                    </p>
                                </li>
                            @endforeach
                        @else
                            <li class="list-group-item">
                                <h5 class="list-group-item-heading inline" style="margin-top:3px">
                                    <span class="indent5">You need to add at least one country to accept orders from
                                        customers in this shipping zone.</span>
                                </h5>
                            </li>
                        @endif
                    </ul>



                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label class="indent10">Shipping rates</label>
                    </div>

                    <ul class="list-group">
                        @if ($item->rates->count() > 0)
                            @foreach ($item->rates as $rate)
                                <li class="list-group-item">
                                    <h4 class="list-group-item-heading">
                                        {{ $rate->name }}

                                        <small class="indent20"> By {{ $carrier_name[$rate->carrier_id] }} And takes
                                            {{ $rate->delivery_time }} days </small>

                                        <a type="submit" class="confirm ajax-silent small text-muted pull-right icon-x"
                                            title="Delete" data-toggle="tooltip" data-placement="top" id="delete_attri"
                                            data-id="{{ $rate->id }}"
                                            style="
                                margin-top: 0%;
                            "><i
                                                class="fa fa-times-circle"></i></a>
                                    </h4>

                                    <p class="list-group-item-text">
                                        {{ $rate->minimum_order_weight }} g - {{ $rate->max_order_weight }} g
                                        <span class="btn btn-secondary border_button btn-sm">
                                            $ {{ $rate->rate }}
                                        </span>
                                        <small class="pull-right">
                                            <a class="ajax-modal-btn" data-toggle="modal"
                                                data-target="#exampleModalLabel_{{ $rate->id }}"
                                                style="cursor: pointer;"><i class="fa fa-edit"></i> Edit</a>
                                        </small>
                                    </p>

                                </li>


                                <div class="modal fade" id="exampleModalLabel_{{ $rate->id }}" tabindex="-1"
                                    role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Edit </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                    aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="edit-rate">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <input type="hidden" name="id"
                                                                value="{{ $rate->id }}">
                                                            <div class="form-group">
                                                                <label for="name" class="with-help">Name</label>

                                                                <input class="form-control" placeholder="Name"
                                                                    required="" value="{{ $rate->name }}"
                                                                    name="name" type="text" value="Domestic"
                                                                    id="name">
                                                                <div class="help-block with-errors">Customer will not see
                                                                    this</div>
                                                            </div>

                                                        </div>

                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="name" class="with-help">Shipping
                                                                    Carrier</label>

                                                                <select name="carrier" class="form-control" required
                                                                    id="customerDropdown">
                                                                    <option value="" selected disabled >Select
                                                                        Shipping Carrier</option>
                                                                    @foreach ($Carriers as $carrier)
                                                                        <option value="{{ $carrier->id }}"
                                                                            {{ $carrier->id == $rate->carrier_id ? 'selected' : '' }}>
                                                                            {{ $carrier->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="nopadding-left">
                                                                <div class="form-group">
                                                                    <label for="active" class="with-help">
                                                                        Delivery Takes</label>
                                                                    <input class="form-control" placeholder="2-5 day"
                                                                        value="{{ $rate->delivery_time }}" required min="1"
                                                                        name="delivery_time" type="text"
                                                                        id="delivery">
                                                                    <div class="help-block with-errors"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="name" class="with-help">Minimum Order
                                                                    Weight (g)</label>

                                                                <input class="form-control" placeholder="" required min="1"
                                                                    value="{{ $rate->minimum_order_weight }}"
                                                                    name="mini_order" type="number" id="minimum_order">
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="nopadding-left">
                                                                <div class="form-group">
                                                                    <label for="active" class="with-help">

                                                                        Maximum Order Weight(g)</label>
                                                                    <input class="form-control" placeholder=""
                                                                        required name="max_order" type="number" min="1"
                                                                        id="maximun_order"
                                                                        value="{{ $rate->max_order_weight }}">
                                                                    <div class="help-block with-errors"></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-2">
                                                        <div class="col-sm-6">
                                                            <div class="form-group">
                                                                <label for="name" class="with-help">Rate</label>

                                                                <input class="form-control" placeholder="" required min="0"
                                                                    name="rate" type="number"
                                                                    value="{{ $rate->rate }}"
                                                                    {{ $rate->rate == 0 ? 'readonly' : '' }}
                                                                    id="rate_{{ $rate->id }}">
                                                            </div>

                                                        </div>
                                                        <div class="col-sm-6">
                                                            <div class="col-sm-6">
                                                                <label for="">Free Shipping</label>
                                                                <div class="mb-3">
                                                                    <input type="hidden" name="free_shipping"
                                                                        id="free_shipping_{{ $rate->id }}"
                                                                        value="{{ $rate->is_free ? 1 : 0 }}">
                                                                    <input type="checkbox"
                                                                        id="switch_{{ $rate->id }}"
                                                                        data-id={{ $rate->id }} data-switch="primary"
                                                                        {{ $rate->is_free ? 'checked' : '' }}
                                                                        class="switch_btn">
                                                                    <label for="switch_{{ $rate->id }}"
                                                                        data-on-label="Free"
                                                                        data-off-label="Paid"></label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">

                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="sumit" class="btn btn-secondary">Update</button>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <li class="list-group-item">
                                <h5 class="list-group-item-heading">
                                    <span>
                                        You need to add at least one shipping rate to accept orders from customers in this
                                        shipping zone.
                                    </span>
                                </h5>
                            </li>
                        @endif

                    </ul>

                </div>
            </div>
            <hr>
        @endforeach
    </div>



    {{-- add Rate --}}

    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Rate </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="rate-form">
                        <div class="row ">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name" class="with-help">Name <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Name" name="name" type="text"
                                        id="name">
                                    <div class="help-block with-errors">Customers will see this</div>
                                    <input type="hidden" name="zone_id" id="zone-id">
                                </div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="active" class="with-help">Shipping Carrier<span
                                        class="text-danger">*</span></label>
                                <select name="carrier" class="form-control" id="customerDropdown">
                                    <option value="" selected disabled>Select Carrier</option>
                                    @foreach ($Carriers as $carrier)
                                        <option value="{{ $carrier->id }}">{{ $carrier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label for="">Delivery Takes <span class="text-danger">*</span></label>

                                <input class="form-control" placeholder="3 days" name="delivery_time" min="1" type="number"
                                    id="delivery_time">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">Minimun Order Weight (g)<span class="text-danger">*</span></label>
                                <input class="form-control" placeholder="0" name="mini_order" min="1" type="number"
                                    id="mini_order">
                            </div>
                            <div class="col-sm-6">
                                <label for="">Maximum Order Weight (g)<span class="text-danger">*</span></label>
                                <input class="form-control" placeholder="100" name="max_order" min="1" type="number"
                                    id="max_order">
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <label for="">Rate <span class="text-danger">*</span></label>
                                <input class="form-control" placeholder="0"  name="rate"  min="0"
                                    type="number" id="rate">
                            </div>
                            <div class="col-sm-6">
                                <label for="">Free Shipping</label>
                                <div class="mb-3">
                                    <input type="hidden" name="free_shipping" id="free_shipping" value="0">
                                    <input type="checkbox" id="switch2" data-switch="primary" value="1"
                                        onclick="updateCheckboxValue(this)">
                                    <label for="switch2" data-on-label="Free" data-off-label="Paid"></label>
                                </div>
                            </div>
                        </div>
                        {{-- <p class="help-block mt-2"><span class="text-danger">*</span> Required fields.</p> --}}

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-secondary mt-n1">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




    {{-- Add Zone Modal --}}
    <div class="modal fade" id="zoneModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Add Zone</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="add-zone">
                    <div class="modal-body">
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="with-help">Name <span
                                            class="text-danger">*</span></label>
                                    <input class="form-control" placeholder="Name" name="name" type="text"
                                        required id="name">
                                    <div class="help-block with-errors">Customers will not see this</div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="name" class="with-help">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="" class="form-select" required>
                                        <option value="" selected disabled>Select</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row mt-2 mb-2">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="name" class="with-help">Country <span
                                            class="text-danger">*</span></label>
                                    <select name="country[]" id="" class="select2-multiple"
                                        data-toggle="select2" multiple="multiple" data-placeholder="Select">
                                        @foreach ($countries as $item)
                                            <option value="{{ $item->id }}">{{ $item->country_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-secondary mt-n1">Save</button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
    </div>



    {{-- Edit Zone Modal  --}}
    <div class="modal fade" id="editZone" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Zone</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="edit-zone">
                    <div class="modal-body" id="modal_content">


                    </div>

                </form>

            </div>
        </div>
    </div>


    {{-- edit state  --}}

    <div class="modal fade" id="edit-states" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="edit-statesTitle">STATES</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="close-edit-zone">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="update_state">
                        {{-- <div class="form-group">
                        <div class="input-group input-group-lg">
                            <span class="input-group-addon no-border"><i class="fa fa-search text-muted"
                                    style="font-size: 32px;"></i></span>
                            <input type="text" id="searchState" class="form-control no-border" placeholder="search">
                        </div>
                    </div> --}}
                        <input type="hidden" id="country_id" name="country">

                        <table class="table" id="table-ex">

                        </table>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-secondary">Update</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@section('script')
    {{-- <script src="path-to-fontawesome/js/all.js"></script> --}}
    <script>
        $(document).on('click', '.add-rate', function() {
            var id = $(this).attr('data-id');
            $('#zone-id').val(id);
        })

        $('body').on("click", "#delete_attri", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var name = $(this).data('data-id');

            let fd = new FormData();
            fd.append('id', id);
            fd.append('_token', '{{ csrf_token() }}');

            if (confirm('Are you sure you want to delete this data ? ')) {
                $.ajax({
                        url: "{{ route('vendor.carrier.delete_shipping') }}",
                        type: 'POST',
                        data: fd,
                        dataType: "JSON",
                        contentType: false,
                        processData: false,
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            window.location.reload();
                            // setTimeout(function() {
                            //     window.location.href = result.location;
                            // }, 1000);
                            $.fn.tableload();
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });
            }
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#rate-form').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 36,

                    },
                    // status: {
                    //     required: true,
                    // },

                    carrier: {
                        required: true,
                    },
                    delivery_time: {
                        required: true,
                        number: true,
                        maxlength: 256,

                    },
                    mini_order: {
                        required: true,
                        number: true,
                        maxlength: 256,
                    },
                    max_order: {
                        required: true,
                        number: true,
                        maxlength: 256,
                    },
                    rate: {
                        required: function(element) {
                        return $("#free_shipping").val() == 0;
                    },
                        number: true,
                    }

                },
            })
        })
    </script>

    <script>
        $(document).on('submit', '#rate-form', function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: "{{ route('vendor.shipping.rates.store') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success")
                        window.location.reload();
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });

        })
    </script>

    <script>
        function updateCheckboxValue(checkbox) {
            var hiddenInput = document.getElementById('free_shipping');
            hiddenInput.value = checkbox.checked ? 1 : 0;

            var rateInput = $('#rate');
            if (checkbox.checked) {
                rateInput.val('0');
                rateInput.attr('readonly', true);
            } else {
                rateInput.attr('readonly', false);

            }
        }

        updateCheckboxValue(document.getElementById('switch2'));
    </script>



    <script>
        $(document).on('change', '.switch_btn', function() {
            var id = $(this).attr('data-id');
            var check = $(this).prop('checked');
            var rate = $('#rate_' + id);
            if (check) {
                $('#free_shipping_' + id).val('1');
                rate.val('0');
                rate.attr('readonly', true);
            } else {
                rate.attr('readonly', false);
                $('#free_shipping_' + id).val('0');
            }
        })
    </script>



    <script>
        $(document).on('submit', '#edit-rate', function(e) {
            e.preventDefault();

            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");

            $.ajax({
                url: "{{ route('vendor.shipping.rates.update') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success")
                        window.location.reload();
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });

        })

        $(document).on('submit', '#add-zone', function(e) {
            e.preventDefault();

            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('vendor.zones.store') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success")
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });

        })

        $('body').on("click", ".delete-zone", function(e) {
            e.preventDefault();
            $('#data-id').val( $(this).data('id'));
            $('#warning-alert-modal').modal('show');

            $('#warning-alert-modal').on('click', '#confirm-delete', function() {
                var token = '{{ csrf_token() }}'
                var  id = $('#data-id').val();
                $.ajax({
                        url: "{{ route('vendor.zones.delete') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            _token: token
                        },
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });

            });
        });



        $('body').on("click", ".delete-country", function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this data ? ')) {
                var id = $(this).data('id');
                var token = '{{ csrf_token() }}'
                $.ajax({
                        url: "{{ route('vendor.zone-country.delete') }}",
                        type: 'POST',
                        data: {
                            id: id,
                            _token: token
                        },
                    })
                    .done(function(result) {
                        if (result.status) {
                            $.NotificationApp.send("Success", result.msg, "top-right",
                                "rgba(0,0,0,0.2)", "success");
                            setTimeout(function() {
                                window.location.reload();
                            }, 1000);
                        } else {
                            $.NotificationApp.send("Error", result.msg, "top-right", "rgba(0,0,0,0.2)",
                                "error");
                        }
                    })
                    .fail(function(jqXHR, exception) {
                        console.log(jqXHR.responseText);
                    });

            }
        });



        // Edit Zone 
        $(document).on('submit', '#zone_edit', function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('vendor.zones.update_zone') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success")

                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });

        })



        // update State 
        $(document).on('submit', '#update_state', function(e) {
            e.preventDefault();
            let fd = new FormData(this);
            fd.append('_token', "{{ csrf_token() }}");
            $.ajax({
                url: "{{ route('vendor.zone.stateUpdate') }}",
                type: "POST",
                data: fd,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status) {
                        $.NotificationApp.send("Success", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "success")

                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    } else {
                        $.NotificationApp.send("Error", result.msg, "top-right",
                            "rgba(0,0,0,0.2)", "error")
                    }
                },
            });

        })


        $(document).on('click', '#close-edit-zone', function() {
            $('#edit-states').modal('hide');
        })



        $(document).on("click", ".edit_state", function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            var token = '{{ csrf_token() }}'
            $.ajax({
                    url: "{{ route('vendor.zone-states.edit') }}",
                    type: 'POST',
                    data: {
                        id: id,
                        _token: token
                    },
                })
                .done(function(result) {
                    console.log(result.data);

                    $('#country_id').val(result.country_id);
                    $('#table-ex').html('');
                    $.each(result.current_states, function(key, val) {
                        console.log(val.id);
                        $('#table-ex').append(`
                                <tbody>
                            <tr>
                                <td>
                                    <div class="icheckbox_line-pink checked">
                                        <h4> <input type="checkbox" name="states[]"   ${$.inArray(val.id, result.data) !== -1 ? 'checked' : ''} value="${val.id}"><span> ${val.state_name}</span> </h4>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        `);
                    });

                    $('#edit-states').modal('show');

                })
                .fail(function(jqXHR, exception) {
                    console.log(jqXHR.responseText);
                });

        });
    </script>
@endsection
