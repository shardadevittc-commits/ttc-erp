@extends('layouts.app')

@section('content')

    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('grades') }}" class="text-decoration-none text-muted">Grades</a></li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Add Grade</li>
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
<div class="container-fluid">
    <div class="card">

        <div class="card-body">

            <form action="{{ route('grades.add') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <label class="form-label"> Grade <span class="text-danger">*</span></label>
                        <input type="text" name="grade_name" placeholder="Enter Grades " class="form-control @error('grade_name') is-invalid @enderror" value="{{ old('grade_name') }}"  maxlength="50" required>
                        @error('grade_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            <label class="form-label">Publish/Unpublish Grades</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="status" value="2">
                                <input type="checkbox" name="status" class="form-check-input" id="status" value="1" {{ old('status') != '2' ? 'checked' : '' }}/>
                                <label class="form-check-label" for="status">Do you wish to publish this grade ?</label>
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
