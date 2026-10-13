<div class="flash-message">
    @if(Session::has('success'))
    	<p class="alert alert-success">
    		{{ Session::get('success') }}
    		<button type="button" class="btn close" data-dismiss="alert" aria-label="Close" style="cursor:pointer;">
		    	<span aria-hidden="true">&times;</span>
		  	</button>
    	</p>
    	{{ Session::forget('success') }}
    @endif
    @if(Session::has('error'))
    	<p class="alert alert-danger">
    		{{ Session::get('error') }}
    		<button type="button" class="btn close" data-dismiss="alert" aria-label="Close" style="cursor:pointer;">
		    	<span aria-hidden="true">&times;</span>
		  	</button>
    	</p>
    	{{ Session::forget('error') }}
    @endif
</div>