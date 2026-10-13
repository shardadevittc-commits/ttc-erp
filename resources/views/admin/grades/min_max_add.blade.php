@extends('layouts.app')

@section('title')
    {{ isset($gradeReaction) && $gradeReaction ? 'Edit' : 'Add' }} Grade Min Max | TTC Robotronics - Steel Industry ERP
@endsection

@section('content')
    <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>
                        {{ isset($gradeReaction) && $gradeReaction ? 'Edit' : 'Add' }}
                        Grade Reaction
                    </h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('grade_min_max') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">

                <form
                    action="{{ route('grade_add', isset($gradeReaction) && $gradeReaction ? ['id' => $gradeReaction->id] : []) }}"
                    method="POST">

                    @csrf

                    <div class="row">

                        {{-- Grade --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Grade <span class="text-danger">*</span>
                            </label>

                            <select name="grade_id" class="form-select @error('grade_id') is-invalid @enderror" required>

                                <option value="">Select Grade</option>

                                @foreach ($grades as $grade)
                                    <option value="{{ $grade->id }}"
                                        {{ old('grade_id', $gradeReaction->grade_id ?? '') == $grade->id ? 'selected' : '' }}>
                                        {{ $grade->grade_name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('grade_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Grade Category --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Composition <span class="text-danger">*</span>
                            </label>

                            <select name="grade_category_id"
                                class="form-select @error('grade_category_id') is-invalid @enderror" required>

                                <option value="">Select Grade Category</option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('grade_category_id', $gradeReaction->grade_category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('grade_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Property --}}
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                Property <span class="text-danger">*</span>
                            </label>

                            <select name="property_id" class="form-select @error('property_id') is-invalid @enderror"
                                required>

                                <option value="">Select Property</option>

                                @foreach ($properties as $property)
                                    <option value="{{ $property->id }}"
                                        {{ old('property_id', $gradeReaction->property_id ?? '') == $property->id ? 'selected' : '' }}>
                                        {{ $property->name }}
                                        @if ($property->unit)
                                            ({{ $property->unit }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('property_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Minimum Value --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Minimum Value <span class="text-danger">*</span>
                            </label>

                            <input type="number" name="min_value" step="any"
                                class="form-control @error('min_value') is-invalid @enderror"
                                placeholder="Enter Minimum Value"
                                value="{{ old('min_value', isset($gradeReaction) && $gradeReaction ? $gradeReaction->getRawOriginal('min_value') : '') }}"
                                required>

                            @error('min_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Maximum Value --}}
                        <div class="col-md-3 mb-3">
                            <label class="form-label">
                                Maximum Value <span class="text-danger">*</span>
                            </label>

                            <input type="number" name="max_value" step="any"
                                class="form-control @error('max_value') is-invalid @enderror"
                                placeholder="Enter Maximum Value"
                                value="{{ old('max_value', isset($gradeReaction) && $gradeReaction ? $gradeReaction->getRawOriginal('max_value') : '') }}"
                                required>

                            @error('max_value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">
                            {{ isset($gradeReaction) && $gradeReaction ? 'Update' : 'Save' }}
                        </button>

                        <a href="{{ route('grade_min_max') }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection
