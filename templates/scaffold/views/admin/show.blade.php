@extends('admin.layouts.admin')
@section('title')
	$MODEL_NAME_CAMEL$
@endsection
@section('per-page-stylesheets')
@endsection
@section('header-page-script')
@endsection
{{-- start: PAGE HEADER --}}
	 @section('parent-breadcrumb')
	 <a href="{{  url("admin") }}">Dashboard</a>
	 @endsection
	 @section('active-breadcrumb')
		My Account
	 @endsection
	 @section('header-title')
		$user->username !!} <small>User Name</small> /  $user->display_name !!} <small>Accout Profile</small>
	 @endsection
	 @section('header-title-small')
	view edit or update <a class="btn btn-xs btn-primary "  href="{!! route('admin.$MODEL_NAME_PLURAL_CAMEL$.create') !!}">Add New</a>
	 @endsection
{{-- end: PAGE HEADER --}}
@section('content')
 <div class="row">
	<div class="col-sm-12">
		<div class="tabbable">
			<ul class="nav nav-tabs tab-padding tab-space-3 tab-blue" id="myTab4">
				<li class="active"> <a data-toggle="tab" href="#panel_overview"> Overview </a> </li>
				<li> <a data-toggle="tab" href="#panel_edit_account"> Edit Account </a> </li>
				<li> <a data-toggle="tab" href="#panel_projects"> Projects </a> </li>
			</ul>
			<div class="tab-content">
				<div id="panel_overview" class="tab-pane in active">
						  @include('admin.$MODEL_NAME_PLURAL_CAMEL$.show_fields')
				</div>
				<div id="panel_edit_account" class="tab-pane">
				      {!! Form::model($MODEL_NAME_CAMEL$, ['route' => ['admin.$MODEL_NAME_PLURAL_CAMEL$.update', $MODEL_NAME_CAMEL$->id], 'method' => 'patch']) !!}
				      @include('admin.$MODEL_NAME_PLURAL_CAMEL$.fields')
				      {!! Form::close() !!}
				</div>
				<div id="panel_projects" class="tab-pane">
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('admin-modal-config')@endsection
@section('per-page-javascripts')@endsection
@section('footer-page-script')@endsection
@section('custom-in-script')@endsection
