@extends('layouts.app')

@section('content')

    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('brands') }}" class="text-decoration-none text-muted">Brands</a></li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Edit Brand</li>
            </ol>
        </nav>
    </div> 

    <div class="page-header">
        
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Brands</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('brands') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>
<div class="container-fluid">
    <div class="card">

        <div class="card-body">

            <form action="{{ route('brands.add') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <label class="form-label"> Brand Name <span class="text-danger">*</span></label>
                        <input type="text" name="brand_name" class="form-control @error('brand_name') is-invalid @enderror" value="{{ old('brand_name') }}"  maxlength="50" required>
                        @error('brand_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="form-label">Publish/Unpublish Brand</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="status" value="0">
                                <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status') != '0' ? 'checked' : '' }}/>
                                <label class="form-check-label" for="status">Do you wish to publish this Brand ?</label>
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
