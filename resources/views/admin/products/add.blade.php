@extends('layouts.app')

@section('content')

    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products') }}" class="text-decoration-none text-muted">Products</a></li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Add Products</li>
            </ol>
        </nav>
    </div> 

    <div class="page-header">
        
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Products</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('products') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.add') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <label class="form-label"> Products <span class="text-danger">*</span></label>
                        <input type="text" name="product_name" placeholder="Enter Products " class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}"  maxlength="50" required>
                        @error('product_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                    <label class="form-label"> Product Category <span class="text-danger">*</span></label>

                    <div class="mt-2">
                        <div class="form-check form-check-inline">
                            <input type="radio" name="product_category" id="finished_material" class="form-check-input @error('product_category') is-invalid @enderror" value="finished_material"
                                {{ old('product_category', 'finished_material') == 'finished_material' ? 'checked' : '' }}
                                required>
                            <label class="form-check-label" for="finished_material"> Finished Material </label>
                        </div>

                        <div class="form-check form-check-inline">
                            <input type="radio" name="product_category" id="raw_material" class="form-check-input @error('product_category') is-invalid @enderror" value="raw_material"
                                {{ old('product_category') == 'raw_material' ? 'checked' : '' }} required>
                            <label class="form-check-label" for="raw_material"> Raw Material </label>
                        </div>
                    </div>
                    @error('product_category')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="form-label">Publish/Unpublish Products</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="status" value="2">
                                <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status') != '2' ? 'checked' : '' }}/>
                                <label class="form-check-label" for="status">Do you wish to publish this products ?</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary"> Save </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
