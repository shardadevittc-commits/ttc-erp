@extends('layouts.app')

@section('title', 'Add New User & Assign Role | TTC Robotronics')

@section('content')
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1" style="font-size: 0.8rem;">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}" class="text-decoration-none text-muted">Users & Roles</a></li>
                <li class="breadcrumb-item active text-danger fw-semibold" aria-current="page">Add User</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h2 class="fs-4 fw-bold m-0" style="color: var(--text-heading); letter-spacing: -0.3px;">
                    <i class="fa-solid fa-user-plus me-2 text-danger"></i>Add New Operator / Role User
                </h2>
                <p class="text-muted small m-0 mt-1">Create credentials, personal profile, and assign departmental role permissions.</p>
            </div>
            <div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-3 py-2 fw-semibold rounded-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Users List
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

    <div class="card border rounded-3 overflow-hidden shadow-sm" style="background: var(--bg-card); border-color: var(--border-card) !important;">
        <div class="card-header py-3 px-4 border-bottom" style="background: var(--bg-card); border-color: var(--border-color) !important;">
            <div class="d-flex align-items-center gap-2">
                <span class="badge rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 28px; height: 28px; background: var(--red-subtle); color: var(--primary-red);">
                    <i class="fa-solid fa-id-card"></i>
                </span>
                <h5 class="m-0 fs-6 fw-bold" style="color: var(--text-heading);">User Credentials & Departmental Role Assignment</h5>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <!-- Left Column: Profile Photo Upload -->
                    <div class="col-12 col-lg-3 text-center border-end-lg pe-lg-4" style="border-color: var(--border-color) !important;">
                        <label class="form-label fw-semibold small text-uppercase text-muted d-block mb-2">Profile Avatar / Photo</label>
                        
                        <div class="d-inline-block position-relative mb-3">
                            <img id="imagePreview" src="https://ui-avatars.com/api/?name=User&background=0B1220&color=ffffff&bold=true" alt="Preview" class="rounded-circle border shadow-sm" style="width: 130px; height: 130px; object-fit: cover; border-color: var(--border-color) !important;">
                            <label for="imageUpload" class="btn btn-sm text-white position-absolute bottom-0 end-0 rounded-circle shadow" style="background: var(--primary-red); width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; cursor: pointer;" title="Upload Image">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                        </div>
                        
                        <input type="file" name="image" id="imageUpload" class="d-none" accept="image/*" onchange="previewImage(this)">
                        
                        <p class="text-muted small mb-3">Allowed JPG, PNG, WEBP.<br>Max file size: 2MB</p>
                        
                        <!-- Role Selection Box in Left Column -->
                        <div class="p-3 rounded-3 text-start" style="background: var(--bg-input); border: 1px solid var(--border-color);">
                            <label class="form-label fw-bold small" style="color: var(--text-heading);">
                                <i class="fa-solid fa-shield-halved text-danger me-1"></i> Department Role <span class="text-danger">*</span>
                            </label>
                            <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required style="background: var(--bg-card); border-color: var(--border-color); color: var(--text-heading);">
                                <option value="">-- Choose Role --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->name }} ({{ ucfirst($role->short_name) }})
                                    </option>
                                @endforeach
                            </select>
                            @error('role_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text small text-muted mt-1">
                                Determines module access (e.g. Sale, Gate, Weight, Lab, Production, Dispatch, Account).
                            </div>
                        </div>

                        <!-- Status Selection -->
                        <div class="p-3 rounded-3 text-start mt-3" style="background: var(--bg-input); border: 1px solid var(--border-color);">
                            <label class="form-label fw-bold small" style="color: var(--text-heading);">
                                <i class="fa-solid fa-toggle-on text-success me-1"></i> Account Status <span class="text-danger">*</span>
                            </label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required style="background: var(--bg-card); border-color: var(--border-color); color: var(--text-heading);">
                                <option value="{{ \App\Models\User::STATUS_ACTIVE }}" {{ old('status', \App\Models\User::STATUS_ACTIVE) == \App\Models\User::STATUS_ACTIVE ? 'selected' : '' }}>Active (Allow Login)</option>
                                <option value="{{ \App\Models\User::STATUS_INACTIVE }}" {{ old('status') == \App\Models\User::STATUS_INACTIVE ? 'selected' : '' }}>Inactive / Suspended</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column: Personal & Login Credentials -->
                    <div class="col-12 col-lg-9 ps-lg-4">
                        <div class="row g-3">
                            <!-- Section Title: Personal Info -->
                            <div class="col-12">
                                <h6 class="fw-bold mb-1" style="color: var(--text-heading); font-size: 0.9rem;">
                                    <i class="fa-solid fa-user me-1 text-muted"></i> Personal Information
                                </h6>
                                <hr class="my-2" style="border-color: var(--border-color);">
                            </div>

                            <!-- First Name -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-heading);">
                                    First Name <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                                        <i class="fa-solid fa-user"></i>
                                    </span>
                                    <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" placeholder="e.g. Rahul" required style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Last Name -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-heading);">
                                    Last Name
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                                        <i class="fa-regular fa-user"></i>
                                    </span>
                                    <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" placeholder="e.g. Sharma" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Phone Number -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-heading);">
                                    Phone Number
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="text" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" placeholder="e.g. +91 9876543210" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Section Title: Account & Login Credentials -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold mb-1" style="color: var(--text-heading); font-size: 0.9rem;">
                                    <i class="fa-solid fa-key me-1 text-muted"></i> Account Credentials & Access
                                </h6>
                                <hr class="my-2" style="border-color: var(--border-color);">
                            </div>

                            <!-- Username -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-heading);">
                                    Username (Unique Identifier) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                                        <i class="fa-solid fa-at"></i>
                                    </span>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}" placeholder="e.g. rahul_gate" required style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text small text-muted">Used for login to the ERP portal.</div>
                            </div>

                            <!-- Email -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-heading);">
                                    Email Address <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                                        <i class="fa-regular fa-envelope"></i>
                                    </span>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" placeholder="e.g. rahul@ttcsteel.com" required style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Password -->
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-semibold small" style="color: var(--text-heading);">
                                    Password <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="passwordInput" class="form-control @error('password') is-invalid @enderror" placeholder="Minimum 6 characters" required minlength="6" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-heading);">
                                    <button type="button" class="btn border" style="background: var(--bg-input); border-color: var(--border-color); color: var(--text-muted);" onclick="togglePasswordVisibility('passwordInput', this)">
                                        <i class="fa-regular fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 mt-4 pt-2">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold">
                                        Cancel
                                    </a>
                                    <button type="submit" class="btn text-white px-4 py-2 rounded-3 fw-semibold shadow-sm" style="background-color: var(--primary-red); border-color: var(--primary-red);">
                                        <i class="fa-solid fa-check me-1.5"></i> Save User & Assign Role
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-regular fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fa-regular fa-eye';
        }
    }
</script>
@endpush
