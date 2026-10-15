@extends('layouts.app')

@section('title', 'Manage Grades Composition Properties Min/Max Values | TTC Robotronics - Steel Industry ERP')

@section('content')

    <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Grade Composition Properties Min/Max Values</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('grade_add') }}" class="header-btn header-btn--primary">
                    <i class="fas fa-plus"></i>
                    New Grade Composition Properties Min/Max Values
                </a>
            </div>
        </div>
    </div>

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-12">

                    <div class="card listing-block ajax-listing-loop">

                        <div class="card-header">
                            <div class="heading">
                                <h5 class="mb-0">
                                    Here Is Your Grade Composition Properties Listing!
                                </h5>
                            </div>

                            <div class="actions">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>

                                    <input type="search" class="form-control listing-search" placeholder="Search..."
                                        value="{{ request('search', '') }}" aria-label="Search Grade Compositions">
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-0">

                            <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                                Loading grade compositions...
                            </div>

                            <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>

                            <div class="table-responsive text-nowrap">
                                <table class="table listing-table">

                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Grade</th>
                                            <th>Grade Composition</th>
                                            <th>Property</th>
                                            <th>Min Value</th>
                                            <th>Max Value</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody class="list table_listing_body">

                                        @forelse ($gradeReactions as $reaction)
                                            <tr>
                                                <td>{{ $reaction->id }}</td>

                                                {{-- Grade Name --}}
                                                <td>
                                                    {{ $reaction->grade->grade_name ?? '-' }}
                                                </td>

                                                {{-- Grade Category --}}
                                                <td>
                                                    {{ $reaction->category->name ?? '-' }}
                                                </td>

                                                {{-- Property Name and Unit --}}
                                                <td>
                                                    {{ $reaction->property->name ?? '-' }}

                                                    @if ($reaction->property && $reaction->property->unit)
                                                        ({{ $reaction->property->unit }})
                                                    @endif
                                                </td>

                                                {{-- Minimum Value --}}
                                                <td>
                                                    {{ $reaction->min_value }}
                                                </td>

                                                {{-- Maximum Value --}}
                                                <td>
                                                    {{ $reaction->max_value }}
                                                </td>

                                                {{-- Actions --}}
                                                <td class="text-end">
                                                    <a href="{{ route('grade_add', ['id' => $reaction->id]) }}"
                                                        class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <form action="" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this Grade Reaction?');">

                                                        @csrf
                                                        @method('DELETE')

                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    No Grade Composition Properties Found.
                                                </td>
                                            </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection
