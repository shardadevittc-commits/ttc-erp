@extends('layouts.adminlayout')
@section('content')

<div class="header-body">
	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-6 col-7">
				<div class="left_area">
					<h6>Manage Countries</h6>
				</div>
			</div>
			<div class="col-lg-6 col-5">
				<div class="right_area text-right filter-dropdown">
					@if(Permission::hasPermission('countries', 'create'))
					<a href="{{ route('admin.countries.add') }}" class="btn btn-default me-1">New</a>
					@endif
					
					@include('admin.countries.filters')
				</div>
			</div>
		</div>
		@if(@$trashCount > 0)
		<div class="row">
			<div class="col-lg-12 col-12">
				<div class="mt-2">
					@if(Permission::hasPermission('countries', 'listing'))
					<a href="{{ route('admin.countries') }}" class="btn btn-default {{ @$type ? '' : 'active' }} me-1">All</a>
					<a href="{{ route('admin.countries.trash') }}" class="btn btn-default {{ @$type ? 'active' : '' }} me-1">Trash</a>
					@endif

					@if(Permission::hasPermission('countries', 'delete'))
					<a href="{{ route('admin.countries.emptyTrash') }}" class="btn btn-danger delete_confirm me-1">Empty Trash</a>
					@endif
				</div>
			</div>
		</div>
		@endif
	</div>
</div>

<div class="content_area">
	<div class="container-xxl flex-grow-1 container-p-y">
		<div class="row">
			<div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				@include('admin.partials.flash_messages')
				<!--!!!!! DO NOT REMOVE listing-block CLASS. INCLUDE THIS IN PARENT DIV OF TABLE ON LISTING COUNTRIES !!!!!-->
				<div class="card listing-block">
			        <div class="card-header">
			        	<div class="heading">
							<h5 class="mb-0">Here Is Your Countries Listing!</h5>
						</div>
						<div class="actions">
							<div class="input-group input-group-merge">
		                    	<span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></span>
		                    	<input type="text" class="form-control listing-search" placeholder="Search..." value="{{ (isset($_GET['search']) && $_GET['search'] ? $_GET['search'] : '') }}">
		                    </div>
		                    @if(Permission::hasPermission('countries', 'update') || Permission::hasPermission('countries', 'delete'))
		                    <div class="action_dropdown btn-group">
		                    	<a href="javascript:;" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		                    		<i class="fas fa-ellipsis-v"></i>
		                    	</a>
		                    	<ul class="dropdown-menu dropdown-menu-end">
		                    		@if(@$type == 'trash')
		                    			@if(Permission::hasPermission('countries', 'update'))
	                    				<li>
	                    					<a class="dropdown-item" href="javascript:;" 
	                    		        	onclick="bulk_actions('{{ route('admin.countries.bulkActions', ['action' => 'restore']) }}', 'restore');">
	                    						<i class="fas fa-trash-restore text-info"></i>
	                    						<span class="status">Restore</span>
	                    					</a>
	                    				</li>
	                    				@endif

	                    				@if(Permission::hasPermission('countries', 'delete'))
	            						<li>
	            							<a class="dropdown-item" href="javascript:;" 
	            				        	onclick="bulk_actions('{{ route('admin.countries.bulkActions', ['action' => 'trash_delete']) }}', 'trash_delete');">
	            								<i class="fas fa-times text-danger"></i>
	            								<span class="status">Delete</span>
	            							</a>
	            						</li>
	            						@endif
            						@else
			                    		@if(Permission::hasPermission('countries', 'update'))
			                    		<li>
			                    			<a class="dropdown-item" href="javascript:;" 
			                            	onclick="bulk_actions('{{ route('admin.countries.bulkActions', ['action' => 'active']) }}', 'active');">
			                    				<i class="fas fa-circle text-success"></i>
			                    				<span class="status">Publish</span>
			                    			</a>
			                    		</li>
			                    		<li>
			                    			<a class="dropdown-item" href="javascript:;" 
			                            	onclick="bulk_actions('{{ route('admin.countries.bulkActions', ['action' => 'inactive']) }}', 'inactive');">
			                    				<i class="fas fa-circle text-danger"></i>
			                    				<span class="status">Unpublish</span>
			                    			</a>
			                    		</li>
			                    		@endif

			                    		@if(Permission::hasPermission('countries', 'delete'))
			                    		<div class="dropdown-divider"></div>
			                    		<li>
			                    			<a class="dropdown-item" href="javascript:;" 
			                            	onclick="bulk_actions('{{ route('admin.countries.bulkActions', ['action' => 'delete']) }}', 'delete');">
			                    				<i class="fas fa-times text-danger"></i>
			                    				<span class="status">Delete</span>
			                    			</a>
			                    		</li>
			                    		@endif
			                    	@endif
		                    	</ul>
		                    </div>
		                    @endif
						</div>
					</div>
			        <!--!!!!! DO NOT REMOVE listing-table, mark_all  CLASSES. INCLUDE THIS IN ALL TABLES LISTING COUNTRIES !!!!!-->
			        <div class="card-body p-0">
				        <div class="table-responsive text-nowrap">
				          	<table class="table listing-table">
				            	<thead class="thead-light">
					              	<tr>
					              		<th width="5%">
			              					<div class="form-check">
			              			        	<input type="checkbox" class="form-check-input mark_all" id="mark_all">
			              			        	<label class="form-check-label" for="mark_all"></label>
			              			      	</div>
					              		</th>
						                <th class="sort">
											<!--- MAKE SURE TO USE PROPOER FIELD IN data-field AND PROPOER DIRECTION IN data-sort -->
											Id
											@if(isset($_GET['sort']) && $_GET['sort'] == 'countries.id' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
											<i class="fas fa-sort-down active" data-field="countries.id" data-sort="asc"></i>
											@elseif(isset($_GET['sort']) && $_GET['sort'] == 'countries.id' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
											<i class="fas fa-sort-up active" data-field="countries.id" data-sort="desc"></i>
											@else
											<i class="fas fa-sort" data-field="countries.id" data-sort="asc"></i>
											@endif
										</th>
										<th class="sort">
											Title
											@if(isset($_GET['sort']) && $_GET['sort'] == 'countries.name' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
											<i class="fas fa-sort-down active" data-field="countries.name" data-sort="asc"></i>
											@elseif(isset($_GET['sort']) && $_GET['sort'] == 'countries.name' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
											<i class="fas fa-sort-up active" data-field="countries.name" data-sort="desc"></i>
											@else
											<i class="fas fa-sort" data-field="countries.name"></i>
											@endif
										</th>
										@if($type != 'trash')
										<th class="sort">
											Status
											@if(isset($_GET['sort']) && $_GET['sort'] == 'countries.status' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
											<i class="fas fa-sort-down active" data-field="countries.status" data-sort="asc"></i>
											@elseif(isset($_GET['sort']) && $_GET['sort'] == 'countries.status' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
											<i class="fas fa-sort-up active" data-field="countries.status" data-sort="desc"></i>
											@else
											<i class="fas fa-sort" data-field="countries.status"></i>
											@endif
										</th>
										@endif
										<th class="sort">
											Created ON
											@if(isset($_GET['sort']) && $_GET['sort'] == 'countries.created_at' && isset($_GET['direction']) && $_GET['direction'] == 'asc')
											<i class="fas fa-sort-down active" data-field="countries.created_at" data-sort="asc"></i>
											@elseif(isset($_GET['sort']) && $_GET['sort'] == 'countries.created_at' && isset($_GET['direction']) && $_GET['direction'] == 'desc')
											<i class="fas fa-sort-up active" data-field="countries.created_at" data-sort="desc"></i>
											@else
											<i class="fas fa-sort" data-field="countries.created_at"></i>
											@endif
										</th>
										<th>
											Actions
										</th>
					              	</tr>
				            	</thead>
				            	<tbody class="list">
				              		@if(!empty($listing->items()))
										@include('admin.countries.listingLoop')
									@else
									<td align="left" colspan="7">
			                        	No records found!
			                        </td>
									@endif
				            	</tbody>
								
								<tfoot>
			                        <tr>
			                            <th align="left" colspan="20">
			                            	@include('admin.partials.pagination', ["pagination" => $listing])
			                            </th>
			                        </tr>
			                    </tfoot>
			                    
							</table>
						</div>
					</div>
		    	</div>
			</div>
		</div>
	</div>
</div>

@endsection