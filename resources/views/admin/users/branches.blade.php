
@extends('layouts.app')

@section('title', 'Company Management | TTC Robotronics')

@section('content')

<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-md-12">
            <h3>Company Management</h3>
            <p class="text-muted">Manage your company details</p>
        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close"
                data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VALIDATION ERRORS --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ADD / EDIT FORM --}}
    <div class="card shadow-sm mb-4">

        <div class="card-header">
            <h5 class="mb-0">
                {{ $editBranch ? 'Edit Company' : 'Add New Company' }}
            </h5>
        </div>

        <div class="card-body">

            <form action="{{ route('users.branches') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @csrf

                {{-- ID FOR UPDATE --}}
                <input type="hidden" name="id"
                    value="{{ old('id', $editBranch->id ?? '') }}">

                <div class="row">

                    {{-- COMPANY NAME --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Company Name <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="company_name"
                               class="form-control"
                               placeholder="Enter Company Name"
                               value="{{ old('company_name', $editBranch->company_name ?? '') }}"
                               required>
                    </div>

                    {{-- GST --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">GST Number</label>

                        <input type="text"
                               name="gst"
                               class="form-control"
                               placeholder="Enter GST Number"
                               value="{{ old('gst', $editBranch->gst ?? '') }}">
                    </div>

                    {{-- PAN --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">PAN Number</label>

                        <input type="text"
                               name="pan"
                               class="form-control"
                               placeholder="Enter PAN Number"
                               value="{{ old('pan', $editBranch->pan ?? '') }}">
                    </div>

                    {{-- LOGO --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Company Logo</label>

                        <input type="file"
                               name="logo"
                               class="form-control"
                               accept="image/*">

                        @if($editBranch && $editBranch->logo)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$editBranch->logo) }}"
                                     width="100"
                                     height="70"
                                     style="object-fit:contain;">

                                <small class="text-muted d-block">
                                    Current Logo
                                </small>
                            </div>
                        @endif
                    </div>

                    {{-- FAVICON --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Favicon</label>

                        <input type="file"
                               name="favicon"
                               class="form-control"
                               accept="image/*">

                        @if($editBranch && $editBranch->favicon)
                            <div class="mt-2">
                                <img src="{{ asset('storage/'.$editBranch->favicon) }}"
                                     width="50"
                                     height="50"
                                     style="object-fit:contain;">

                                <small class="text-muted d-block">
                                    Current Favicon
                                </small>
                            </div>
                        @endif
                    </div>

                    {{-- EMAIL --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>

                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="Enter Email"
                               value="{{ old('email', $editBranch->email ?? '') }}">
                    </div>

                    {{-- WEBSITE --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Website</label>

                        <input type="url"
                               name="website"
                               class="form-control"
                               placeholder="https://example.com"
                               value="{{ old('website', $editBranch->website ?? '') }}">
                    </div>

                    {{-- PHONE --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone Number</label>

                        <input type="text"
                               name="phone"
                               class="form-control"
                               placeholder="Enter Phone Number"
                               value="{{ old('phone', $editBranch->phone ?? '') }}">
                    </div>

                    {{-- ADDRESS --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Address</label>

                        <textarea name="address"
                                  class="form-control"
                                  rows="3"
                                  placeholder="Enter Company Address">{{ old('address', $editBranch->address ?? '') }}</textarea>
                    </div>

                    {{-- CERTIFICATE DESCRIPTION --}}
                    <div class="col-md-12 mb-3">
                        <label class="form-label">
                            Certificate Description
                        </label>

                        <textarea name="certificate_desc"
                                  class="form-control"
                                  rows="4"
                                  placeholder="Enter Certificate Description">{{ old('certificate_desc', $editBranch->certificate_desc ?? '') }}</textarea>
                    </div>

                    {{-- BUTTONS --}}
                    <div class="col-md-12 mt-3">

                        <button type="submit" class="btn btn-primary">
                            {{ $editBranch ? 'Update Company' : 'Save Company' }}
                        </button>

                        @if($editBranch)
                            <a href="{{ route('users.branches') }}"
                               class="btn btn-secondary">
                                Cancel
                            </a>
                        @else
                            <button type="reset" class="btn btn-secondary">
                                Reset
                            </button>
                        @endif

                    </div>

                </div>

            </form>

        </div>
    </div>


    {{-- COMPANY LIST --}}
    <div class="card shadow-sm">

        <div class="card-header">
            <h5 class="mb-0">Company List</h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Sr.</th>
                            <th>Logo</th>
                            <th>Company Name</th>
                            <th>GST</th>
                            <th>PAN</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Website</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($branches as $key => $branch)

                            <tr>
                                <td>{{ $key + 1 }}</td>

                                <td>
                                    @if($branch->logo)
                                        <img src="{{ asset('storage/'.$branch->logo) }}"
                                             width="55"
                                             height="45"
                                             style="object-fit:contain;">
                                    @else
                                        <span class="text-muted">No Logo</span>
                                    @endif
                                </td>

                                <td>{{ $branch->company_name }}</td>

                                <td>{{ $branch->gst ?? '-' }}</td>

                                <td>{{ $branch->pan ?? '-' }}</td>

                                <td>{{ $branch->email ?? '-' }}</td>

                                <td>{{ $branch->phone ?? '-' }}</td>

                                <td>
                                    @if($branch->website)
                                        <a href="{{ $branch->website }}"
                                           target="_blank">
                                            Visit Website
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <div class="d-flex gap-2">

                                        {{-- EDIT --}}
                                        <a href="{{ route('users.branches', ['id' => $branch->id]) }}"
                                           class="btn btn-sm btn-warning">
                                            Edit
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('users.branches') }}"
                                              method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this company?')">

                                            @csrf
                                            @method('DELETE')

                                            <input type="hidden"
                                                   name="id"
                                                   value="{{ $branch->id }}">

                                            {{-- <button type="submit"
                                                    class="btn btn-sm btn-danger">
                                                Delete
                                            </button> --}}

                                        </form>

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="9"
                                    class="text-center text-muted">
                                    No Company Found
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

@endsection
