<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

use App\Models\Admin\Permission;
use App\Models\Admin\Country;

class CountriesController extends AppController
{
	function __construct()
	{
		parent::__construct();
	}

    function index(Request $request, $type = null)
    {
    	$trashCount = Country::onlyTrashed()->count();

    	if(!Permission::hasPermission('countries', 'listing'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$where = self::searchAndFilters($request);

    	$listing = Country::getListing($request, $where, $type);

    	if($request->ajax())
    	{
		    $html = view(
	    		"admin/countries/listingLoop", 
	    		[
	    			'listing' => $listing,
	    			'type' => $type
	    		]
	    	)->render();

		    return Response()->json([
		    	'status' => 'success',
	            'html' => $html,
	            'page' => $listing->currentPage(),
	            'counter' => $listing->perPage(),
	            'count' => $listing->total(),
	            'pagination_counter' => $listing->currentPage() * $listing->perPage()
	        ], 200);
		}
		else
		{
	    	return view(
	    		"admin/countries/index", 
	    		[
	    			'listing' => $listing,
	    			'type' => $type,
	    			'trashCount' => $trashCount
	    		]
	    	);
	    }
    }

    protected function searchAndFilters($request, $where = [])
    {
    	$where = [];

    	if($request->get('search'))
    	{
    		$search = $request->get('search');
    		$search = '%' . $search . '%';
    		$where['(countries.name LIKE ? )'] = [$search];
    	}

    	if($request->get('status') !== "" && $request->get('status') !== null)
    	{    		
    		$where['countries.status'] = $request->get('status');
    	}

    	if($request->get('created_on'))
    	{
    		$createdOn = $request->get('created_on');
    		if(isset($createdOn[0]) && !empty($createdOn[0]))
    			$where['countries.created_at >= ?'] = [
    				date('Y-m-d 00:00:00', strtotime($createdOn[0]))
    			];
    		if(isset($createdOn[1]) && !empty($createdOn[1]))
    			$where['countries.created_at <= ?'] = [
    				date('Y-m-d 23:59:59', strtotime($createdOn[1]))
    			];
    	}

    	return $where;
    }

    function add(Request $request)
    {
    	if(!Permission::hasPermission('countries', 'create'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	if($request->isMethod('post'))
    	{
    		$data = $request->toArray();
    		unset($data['_token']);

    		$validator = Validator::make(
	            $request->toArray(),
	            [
	                'name' => 'required'
	            ]
	        );

	        if(!$validator->fails())
	        {
	        	$countries = Country::create($data);
	        	if($countries)
	        	{
	        		$request->session()->flash('success', 'Country created successfully.');
	        		return redirect()->route('admin.countries');
	        	}
	        	else
	        	{
	        		$request->session()->flash('error', 'Country could not be save. Please try again.');
		    		return redirect()->back()->withErrors($validator)->withInput();
	        	}
		    }
		    else
		    {
		    	$request->session()->flash('error', 'Please provide valid inputs.');
		    	return redirect()->back()->withErrors($validator)->withInput();
		    }
		}

	    return view("admin/countries/add", [
	    ]);
    }

    function edit(Request $request, $id)
    {
    	if(!Permission::hasPermission('countries', 'update'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$countries = Country::get($id);

    	if($countries)
    	{
	    	if($request->isMethod('post'))
	    	{
	    		$data = $request->toArray();
	    		$validator = Validator::make(
		            $request->toArray(),
		            [
		                'name' => [
		                	'required'
		                ],		                
		            ]
		        );

		        if(!$validator->fails())
		        {
		        	unset($data['_token']);

		        	if(Country::modify($id, $data))
		        	{
		        		$request->session()->flash('success', 'Country updated successfully.');
		        		return redirect()->route('admin.countries');
		        	}
		        	else
		        	{
		        		$request->session()->flash('error', 'Country could not be save. Please try again.');
			    		return redirect()->back()->withErrors($validator)->withInput();
		        	}
			    }
			    else
			    {
			    	$request->session()->flash('error', 'Please provide valid inputs.');
			    	return redirect()->back()->withErrors($validator)->withInput();
			    }
			}

			return view("admin/countries/edit", [
    			'countries' => $countries
    		]);
		}
		else
		{
			abort(404);
		}
    }

    function view(Request $request, $id)
    {
    	if(!Permission::hasPermission('countries', 'listing'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$countries = Country::get($id);
    	if($countries)
    	{
	    	return view("admin/countries/view", [
    			'countries' => $countries
    		]);
		}
		else
		{
			abort(404);
		}
    }

    function delete(Request $request, $id)
    {
    	if(!Permission::hasPermission('countries', 'delete'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$admin = Country::find($id);
    	if($admin->delete())
    	{
    		$request->session()->flash('success', 'Country deleted successfully.');
    		return redirect()->route('admin.countries');
    	}
    	else
    	{
    		$request->session()->flash('error', 'Country could not be delete.');
    		return redirect()->route('admin.countries');
    	}
    }

    function bulkActions(Request $request, $action)
    {
    	if( ($action != 'delete' && !Permission::hasPermission('countries', 'update')) || ($action == 'delete' && !Permission::hasPermission('countries', 'delete')) )
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$trashCount = null;
    	$ids = $request->get('ids');
    	if(is_array($ids) && !empty($ids))
    	{
    		switch ($action) {
    			case 'active':
    				Country::modifyAll($ids, [
    					'status' => 1
    				]);
    				$message = count($ids) . ' records has been published.';
    			break;
    			case 'inactive':
    				Country::modifyAll($ids, [
    					'status' => 0
    				]);
    				$message = count($ids) . ' records has been unpublished.';
    			break;
    			case 'delete':
    				Country::removeAll($ids);
    				$message = count($ids) . ' records has been deleted.';
    			break;
    			case 'restore':
    				Country::restoreAll($ids);
    				$message = count($ids) . ' records has been restored.';
    				$trashCount = Country::onlyTrashed()->count();
    			break;
    			case 'trash_delete':
    				Country::trashDeleteAll($ids);
    				$message = count($ids) . ' records has been deleted.';
    				$trashCount = Country::onlyTrashed()->count();
    			break;
    		}

    		$request->session()->flash('success', $message);

    		return Response()->json([
    			'status' => 'success',
    			'redirect_url' => !is_null($trashCount) && $trashCount == 0 ? route('admin.countries') : '',
	            'message' => $message,
	        ], 200);		
    	}
    	else
    	{
    		return Response()->json([
    			'status' => 'error',
	            'message' => 'Please select atleast one record.',
	        ], 200);	
    	}
    }

    function trash(Request $request)
    {
    	$trash = $request->segment(3);

    	return self::index($request, $trash);
    }

    function trashDelete(Request $request, $id)
    {
    	if(!Permission::hasPermission('countries', 'delete'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}
    	
    	$trashDelete = Country::trashDelete($id);
    	
    	if($trashDelete)
    	{
    		$request->session()->flash('success', 'Country deleted successfully.');

    		$trashCount = Country::onlyTrashed()->count();

    		if($trashCount > 0)
    		{
    			return redirect()->route('admin.countries.trash');
    		}
    		else
    		{
    			return redirect()->route('admin.countries');
    		}
    	}
    	else
    	{
    		$request->session()->flash('error', 'Country could not be delete.');
    		return redirect()->route('admin.countries.trash');
    	}
    }

    function restore(Request $request, $id)
    {
    	if(!Permission::hasPermission('countries', 'update'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}

    	$trashedRecord = Country::withTrashed()->find($id);
    	
    	if($trashedRecord)
    	{	
        	if($trashedRecord->restore())
        	{
        		$request->session()->flash('success', 'Country restored successfully.');

        		$trashCount = Country::onlyTrashed()->count();
        		
        		if($trashCount > 0)
        		{
        			return redirect()->route('admin.countries.trash');
        		}
        		else
        		{
        			return redirect()->route('admin.countries');
        		}
        	}
        	else
        	{
        		$request->session()->flash('error', 'Country could not be restore. Please try again.');
	    		return redirect()->route('admin.countries.trash');
        	}
		}
		else
		{
			abort(404);
		}
    }

    function emptyTrash(Request $request)
    {
    	if(!Permission::hasPermission('countries', 'delete'))
    	{
    		$request->session()->flash('error', 'Permission denied.');
    		return redirect()->route('admin.dashboard');
    	}
    	
    	$deletedCount = Country::onlyTrashed()->forceDelete();

    	$request->session()->flash('success', 'All trash records has been removed successfully.');
    	return redirect()->route('admin.countries');
    }
}