@extends('layouts.app')

@section('title', 'Edit Grades | ' . $grade->grade_name)

@section('content')
    
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('grades') }}" class="text-decoration-none text-muted">Grades</a></li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Edit Grade</li>
            </ol>
        </nav>
    </div> 

    <div class="page-header">
        
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Grades</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('grades') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 py-2.5 mb-4 border-0" role="alert" style="background-color: var(--red-subtle); color: var(--primary-red); border-left: 4px solid var(--primary-red) !important;">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation fs-5 me-2"></i>
                <div>
                    <strong class="d-block mb-1">Please correct the following errors:</strong>
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <div class="content_area">
        <div class=" flex-grow-1 container-p-y">
            <!--!! FLAST MESSAGES !!-->
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    <div class="card">
                        <h5 class="card-header">Update Grades Details Here.</h5>
                        <hr class="my-0" />
                        <div class="card-body">
                            <form action="{{ route('grades.edit',['id' => $grade->id]) }}" method="POST" enctype="multipart/form-data">
                                <!--!! CSRF FIELD !!-->
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label"> Grade Name <span class="text-danger">*</span> </label>
                                            <input type="text" name="grade_name" class="form-control @error('grade_name') is-invalid @enderror" value="{{ old('grade_name', $grade->grade_name) }}" placeholder="e.g. Rahul" required style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                            @error('grade_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                                        <div class="form-group">
                                            <label class="form-label">Publish or Unpublish Grades</label>
                                            <div class="form-check form-switch mt-2">
                                                <input type="hidden" name="status" value="2">
                                                <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status', $grade->status) == 1 ? 'checked' : '' }}/>
                                                <label class="form-check-label" for="status">Do you want to publish this grade ?</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group mt-2 clearfix">
                                    <button type="submit" class="btn btn-primary float-end">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection