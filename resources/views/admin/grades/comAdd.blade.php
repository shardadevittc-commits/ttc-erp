@extends('layouts.app')
@section('title')
    {{ isset($gradeCategory) ? 'Edit' : 'Add' }} Grade Composition | TTC Robotronics - Steel Industry ERP
@endsection
@section('content')
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">
                        Dashboard
                    </a>
                </li>

                <li class="breadcrumb-item">
                    <a href="{{ route('grades.composition') }}" class="text-decoration-none text-muted">
                        Grade Composition
                    </a>
                </li>

                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">
                    {{ isset($gradeCategory) ? 'Edit Grade Composition' : 'Add Grade Composition' }}
                </li>
            </ol>
        </nav>
    </div>

    <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Grade Composition</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('grades.composition') }}"
                    class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <form action="{{ route('grades.composition.add', request()->query()) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                            <label class="form-label">
                                Grade Composition
                                <span class="text-danger">*</span>
                            </label>

                            <input type="text" name="c_name" placeholder="Enter Grade Composition"
                                class="form-control @error('c_name') is-invalid @enderror"
                                value="{{ old('c_name', $gradeCategory->name ?? '') }}" maxlength="50" required>

                            @error('c_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror

                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($gradeCategory) ? 'Update' : 'Save' }}
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
