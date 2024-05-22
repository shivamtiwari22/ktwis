@extends('admin.layout.app')

@section('meta_tags')
@endsection


@section('title')
@endsection


@section('css')
@endsection


@section('main_content')

<!-- Warning Alert Modal -->
<div id="warning-alert-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body p-4">
                <div class="text-center">
                    <i class="dripicons-warning h1 text-warning"></i>
                    <h4 class="mt-2">Warning</h4>
                    <p class="mt-3">Are you sure you want to delete<br> <b>[<span id="warning-alert-modal-text"></span>]</b>.</p>
                    <button type="button" class="btn btn-warning my-2" data-bs-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<nav aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Wallet</li>
    </ol>
</nav>

    <div class="mt-2">
        <h3>Wallet Amount</h3>
        <strong style="font-size: 50px ; " class="ms-2">
            $ {{$totalAmount}}
        </strong>
    </div>
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-end mb-2">
            <a href="{{route('admin.wallet.transaction')}}" class="btn btn-warning">Payout Requests</a>
        </div>
        <table id="wallet_table" class="table table-striped dt-responsive nowrap w-100">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Currency</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($balance as $item)
                <tr>
                    <td>{{$loop->iteration}}</td>
                    <td>{{$item->currency->symbol}}</td>
                    <td>{{$item->balance_amount}}</td>
                    <td>{{$item->created_at}}</td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
    </div>
@endsection


