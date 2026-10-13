@extends('layouts.app')

@section('title')
    {{ isset($gradeProperty) && $gradeProperty ? 'Edit' : 'Add' }} Grade Composition | TTC Robotronics - Steel Industry ERP
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>
                        {{ isset($gradeProperty) && $gradeProperty ? 'Edit' : 'Add' }}
                        Grade Property
                    </h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('grade_properties') }}"
                   class="btn btn-outline-secondary px-3 py-2 rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <form
                    action="{{ route('grade_properties.add', isset($gradeProperty) && $gradeProperty ? ['id' => $gradeProperty->id] : []) }}"
                    method="POST">

                    @csrf

                    <div class="row">

                        {{-- Grade Category --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Grade Composition
                                <span class="text-danger">*</span>
                            </label>

                            <select
                                name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                                required>

                                <option value="">Select Grade Composition</option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        {{ old('category_id', $gradeProperty->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Property Name --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Property Name
                                <span class="text-danger">*</span>
                            </label>

                            <input
                                type="text"
                                name="name"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Enter Property Name"
                                value="{{ old('name', $gradeProperty->name ?? '') }}"
                                maxlength="50"
                                required>

                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Unit --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Unit
                            </label>

                            <input
                                type="text"
                                name="unit"
                                class="form-control @error('unit') is-invalid @enderror"
                                placeholder="Enter Unit (e.g. %, mm, kg)"
                                value="{{ old('unit', $gradeProperty->unit ?? '') }}"
                                maxlength="20">

                            @error('unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($gradeProperty) && $gradeProperty ? 'Update' : 'Save' }}
                        </button>

                        <a href="{{ route('grade_properties') }}"
                           class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
