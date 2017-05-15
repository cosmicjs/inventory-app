@extends('master')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div style="float:left">
            <h1>Inventory Management</h1>
        </div>
        <div style="float:right;padding-top: 20px">
            <a class="btn btn-default" href="https://github.com/inventory-app" target="_blank"><i class="fa fa-github"></i> View on GitHub</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div style="float: right; margin-bottom: 15px;"><a href="https://cosmicjs.com" target="_blank" style="text-decoration: none;"><img class="pull-left" src="https://cosmicjs.com/images/logo.svg" width="28" height="28" style="margin-right: 10px;"><span style="color: rgb(102, 102, 102); position: relative; top: 3px;">Proudly powered by Cosmic JS</span></a></div>
    </div>
</div>

<div class="row" style="font-size: 16px">
    <!-- Display vue component and set props from given data  -->
    <inventory message="{{Session::get('status')}}" :initial-locations="{{ json_encode($locations) }}" slug="{{ $bucket_slug }}" location-slug="{{ $location_slug }}"></inventory>
</div>
@endsection

@section('scripts')
<script>
</script>
@endsection

