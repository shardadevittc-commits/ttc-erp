@extends('layouts.app')

@section('title', 'Manage Grades Composition | TTC Robotronics - Steel Industry ERP')

@push('styles')
@endpush

@section('content')

    <div class="page-header">
        <div class="page-header__inner">
            <div class="page-header__content">
                <div class="page-header__title">
                    <h6>Manage Grade Composition</h6>
                </div>
            </div>

            <div class="page-header__actions">
                <a href="{{ route('grades.composition.add') }}" class="header-btn header-btn--primary"><i
                        class="fas fa-plus"></i> New Grade Composition</a>
            </div>
        </div>
    </div>

    <div class="content_area">
        <div class="flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                    {{-- @include('admin.partials.flash_messages') --}}
                    <!--!!!!! DO NOT REMOVE listing-block CLASS. INCLUDE THIS IN PARENT DIV OF TABLE ON LISTING USERS !!!!!-->
                    <div class="card listing-block ajax-listing-loop">
                        <div class="card-header">
                            <div class="heading">
                                <h5 class="mb-0">Here Is Your Grade Compositions Listing!</h5>
                            </div>
                            <div class="actions">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                                    <input type="search" class="form-control listing-search" placeholder="Search..."
                                        value="{{ request('search', '') }}" aria-label="Search Grade Compositions">
                                </div>
                            </div>
                        </div>
                        <!--!!!!! DO NOT REMOVE listing-table, mark_all  CLASSES. INCLUDE THIS IN ALL TABLES LISTING USERS !!!!!-->
                        <div class="card-body p-0">
                            <div class="listing-loading d-none px-3 py-2" role="status" aria-live="polite">
                                <span class="spinner-border spinner-border-sm" aria-hidden="true"></span> Loading grade
                                compositions...
                            </div>
                            <div class="listing-error d-none alert alert-danger m-3" role="alert"></div>
                            <div class="table-responsive text-nowrap">
                                <table class="table listing-table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody class="list table_listing_body">
                                        @forelse ($composition as $grade)
                                            <tr>
                                                <td>{{ $grade->id }}</td>
                                                <td>{{ $grade->name }}</td>
                                                <td class="text-end">
                                                    <a href="{{ route('grades.composition.add', ['id' => $grade->id]) }}"
                                                        class="btn btn-sm btn-primary" title="Edit"> <i
                                                            class="fas fa-edit"></i> </a>


                                                    {{-- Delete Button --}}
                                                    <form action="" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Are you sure you want to delete this Grade Composition?');">
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
                                                <td colspan="6" class="text-center">
                                                    No Grade Compositions Found.
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
