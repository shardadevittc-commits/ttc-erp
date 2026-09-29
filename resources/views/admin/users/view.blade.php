@php
    use Carbon\Carbon;

    $backUrl = request()->query('back_url');

    $membership = !empty($user->membership_data) && isJson($user->membership_data)
        ? json_decode($user->membership_data, false)
        : null;

    $tranee = !empty($user->trainee_plan_data) && isJson($user->trainee_plan_data)
        ? json_decode($user->trainee_plan_data, false)
        : null;
@endphp

@extends('layouts.app')

@section('title', 'View User | TTC Robotronics - Steel Industry ERP')

@push('styles')
<style>
    .user-profile-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }

    .user-profile-header {
        background: linear-gradient(135deg, #f8f9fa, #ffffff);
        border-bottom: 1px solid #e9ecef;
        padding: 20px 24px;
    }

    .user-avatar {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fce8e8;
        color: #dc3545;
        font-size: 20px;
        font-weight: 700;
    }

    .user-name {
        font-size: 17px;
        font-weight: 700;
        color: #212529;
        margin-bottom: 3px;
    }

    .user-email {
        font-size: 13px;
        color: #6c757d;
    }

    .info-table {
        margin-bottom: 0;
    }

    .info-table th {
        width: 35%;
        color: #6c757d;
        font-size: 13px;
        font-weight: 600;
        background: #fafafa;
        padding: 14px 20px;
        border-color: #edf0f2;
    }

    .info-table td {
        color: #212529;
        font-size: 14px;
        padding: 14px 20px;
        border-color: #edf0f2;
        font-weight: 500;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 9px;
    }

    .section-title i {
        color: #dc3545;
    }

    .status-badge {
        font-size: 11px;
        padding: 5px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .document-card {
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
        transition: all .2s ease;
    }

    .document-card:hover {
        border-color: #dc3545;
        box-shadow: 0 5px 18px rgba(0,0,0,.06);
        transform: translateY(-2px);
    }

    .document-preview {
        height: 150px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .document-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .document-icon {
        font-size: 50px;
        color: #dc3545;
    }

    .document-footer {
        padding: 10px 12px;
        border-top: 1px solid #edf0f2;
        font-size: 12px;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .view-document {
        color: #dc3545;
        text-decoration: none;
        font-weight: 600;
    }

    .view-document:hover {
        color: #bb2d3b;
    }

    .empty-value {
        color: #adb5bd;
        font-style: italic;
    }

    @media(max-width: 767px) {
        .info-table th {
            width: 42%;
        }

        .user-profile-header {
            padding: 16px;
        }

        .info-table th,
        .info-table td {
            padding: 11px 14px;
            font-size: 12px;
        }

        .document-preview {
            height: 120px;
        }
    }
</style>
@endpush

@section('content')

<div class="page-header">
    <div class="page-header__inner">

        <div class="page-header__content">

            <div class="page-header__title">
                <h6>Manage Users & Roles</h6>
                <span>
                    View complete user profile, role, membership and account information.
                </span>
            </div>

            <div class="page-header__tabs">
                <a href="{{ route('users.users') }}" class="page-tab active">
                    All Users
                </a>
            </div>

        </div>

        <div class="page-header__actions">

            <a href="{{ route('users.edit', [
                'back_url' => url()->full(),
                'id' => $user->id
            ]) }}"
               class="header-btn header-btn--primary">
                <i class="far fa-edit"></i>
                Edit User
            </a>

            <a href="{{ $backUrl ?: route('users.users') }}"
               class="header-btn header-btn--secondary">
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>

        </div>

    </div>
</div>


<div class="content_area">

    <div class="flex-grow-1 container-p-y">

        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-xxl-8 col-xl-8 col-lg-7 col-md-12">

                {{-- USER INFORMATION --}}
                <div class="card listing-block user-profile-card">

                    <div class="user-profile-header">

                        <div class="d-flex align-items-center gap-3">

                            <div class="user-avatar">
                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                            </div>

                            <div>
                                <div class="user-name">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </div>

                                <div class="user-email">
                                    {{ $user->email }}
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="card-header view_header">
                        <div class="heading section-title">
                            <i class="fas fa-user"></i>
                            <h5 class="mb-0">User Information</h5>
                        </div>
                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table info-table">

                                <tbody>

                                    <tr>
                                        <th> User ID </th>
                                        <td>{{ $user->id }}</td>
                                    </tr>

                                    <tr>
                                        <th>Full Name</th>
                                        <td>
                                            {{ $user->first_name }} {{ $user->last_name }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Email</th>
                                        <td>
                                            <a href="mailto:{{ $user->email }}">
                                                {{ $user->email }}
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Phone Number</th>
                                        <td>
                                            +91-{{ $user->phone_number }}
                                        </td>
                                    </tr>

                                    @if($user->dob)

                                    <tr>
                                        <th>Date of Birth</th>
                                        <td>
                                            {{ Carbon::parse($user->dob)->format('jS F, Y') }}
                                        </td>
                                    </tr>

                                    @endif

                                    @if($user->gender)

                                    <tr>
                                        <th>Gender</th>
                                        <td>
                                            <span class="badge bg-warning status-badge">
                                                {{ ucfirst($user->gender) }}
                                            </span>
                                        </td>
                                    </tr>

                                    @endif

                                    @if($user->address)

                                    <tr>
                                        <th>Full Address</th>
                                        <td>
                                            {!! nl2br(e($user->address)) !!}
                                        </td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                {{-- MEMBERSHIP --}}
                @if($membership)

                <div class="card listing-block user-profile-card mt-4">

                    <div class="card-header view_header">

                        <div class="heading section-title">
                            <i class="fas fa-id-card"></i>
                            <h5 class="mb-0">Membership Information</h5>
                        </div>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table info-table">

                                <tbody>

                                    @if($membership->id)
                                    <tr>
                                        <th>Membership ID</th>
                                        <td>{{ $membership->id }}</td>
                                    </tr>
                                    @endif

                                    @if($membership->title)
                                    <tr>
                                        <th>Membership Title</th>
                                        <td>{{ $membership->title }}</td>
                                    </tr>
                                    @endif

                                    @if($membership->price)
                                    <tr>
                                        <th>Price</th>
                                        <td>{{ priceFormat($membership->price) }}</td>
                                    </tr>
                                    @endif

                                    @if($user->membership_expires_at)

                                    <tr>
                                        <th>Expires On</th>
                                        <td>
                                            {{ date('d-M-Y', strtotime($user->membership_expires_at)) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>

                                            @if(strtotime(date('Y-m-d', strtotime($user->membership_expires_at))) >= strtotime(date('Y-m-d')))

                                                <span class="badge bg-success status-badge">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Active
                                                </span>

                                            @else

                                                <span class="badge bg-danger status-badge">
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    Expired
                                                </span>

                                            @endif

                                        </td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                @endif


                {{-- TRAINEE --}}
                @if($tranee)

                <div class="card listing-block user-profile-card mt-4">

                    <div class="card-header view_header">

                        <div class="heading section-title">
                            <i class="fas fa-graduation-cap"></i>
                            <h5 class="mb-0">Trainee Information</h5>
                        </div>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table info-table">

                                <tbody>

                                    @if($tranee->id)
                                    <tr>
                                        <th>Plan ID</th>
                                        <td>{{ $tranee->id }}</td>
                                    </tr>
                                    @endif

                                    @if($tranee->title)
                                    <tr>
                                        <th>Plan Title</th>
                                        <td>{{ $tranee->title }}</td>
                                    </tr>
                                    @endif

                                    @if($tranee->price)
                                    <tr>
                                        <th>Price</th>
                                        <td>{{ priceFormat($tranee->price) }}</td>
                                    </tr>
                                    @endif

                                    @if($user->trainee_plain_expires_at)

                                    <tr>
                                        <th>Expires On</th>
                                        <td>
                                            {{ date('d-M-Y', strtotime($user->trainee_plain_expires_at)) }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>

                                            @if(strtotime(date('Y-m-d', strtotime($user->trainee_plain_expires_at))) >= strtotime(date('Y-m-d')))

                                                <span class="badge bg-success status-badge">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Active
                                                </span>

                                            @else

                                                <span class="badge bg-danger status-badge">
                                                    <i class="fas fa-times-circle me-1"></i>
                                                    Expired
                                                </span>

                                            @endif

                                        </td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                @endif

            </div>


            {{-- RIGHT COLUMN --}}
            <div class="col-xxl-4 col-xl-4 col-lg-5 col-md-12">

                {{-- OTHER INFORMATION --}}
                <div class="card listing-block user-profile-card">

                    <div class="card-header view_header">

                        <div class="heading section-title">
                            <i class="fas fa-info-circle"></i>
                            <h5 class="mb-0">Other Information</h5>
                        </div>

                    </div>

                    <div class="card-body p-0">

                        <div class="table-responsive">

                            <table class="table info-table">

                                <tbody>

                                    <tr>
                                        <th>Created On</th>
                                        <td>
                                            {{ $user->created_at }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Updated On</th>
                                        <td>
                                            {{ $user->updated_at }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Last Login</th>
                                        <td>
                                            {{ $user->last_login ? _dt($user->last_login) : '-' }}
                                        </td>
                                    </tr>

                                    <tr>
                                        <th>Status</th>
                                        <td>

                                            @if($user->status)

                                                <span class="badge bg-success status-badge">
                                                    Active
                                                </span>

                                            @else

                                                <span class="badge bg-danger status-badge">
                                                    Inactive
                                                </span>

                                            @endif

                                        </td>
                                    </tr>

                                    @if(isset($exp) && !empty($exp))

                                    <tr>
                                        <th>Experience</th>
                                        <td>
                                            {{ $exp }}
                                        </td>
                                    </tr>

                                    @endif

                                    @if($user->dol)

                                    <tr>
                                        <th>Date Of Leaving</th>
                                        <td>
                                            <span class="badge bg-danger status-badge">
                                                {{ Carbon::parse($user->dol)->format('jS F, Y') }}
                                            </span>
                                        </td>
                                    </tr>

                                    @endif

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>


                {{-- QUICK USER SUMMARY --}}
                <div class="card listing-block user-profile-card mt-4">

                    <div class="card-header view_header">

                        <div class="heading section-title">
                            <i class="fas fa-user-check"></i>
                            <h5 class="mb-0">Account Summary</h5>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Account Status</span>

                            @if($user->status)
                                <span class="badge bg-success status-badge">
                                    Active
                                </span>
                            @else
                                <span class="badge bg-danger status-badge">
                                    Inactive
                                </span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-muted">Membership</span>

                            @if($membership)
                                <span class="badge bg-primary status-badge">
                                    Available
                                </span>
                            @else
                                <span class="badge bg-secondary status-badge">
                                    Not Assigned
                                </span>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Trainee Plan</span>

                            @if($tranee)
                                <span class="badge bg-primary status-badge">
                                    Available
                                </span>
                            @else
                                <span class="badge bg-secondary status-badge">
                                    Not Assigned
                                </span>
                            @endif
                        </div>

                    </div>

                </div>

            </div>


            {{-- DOCUMENTS --}}
            @if($user->document)

            <div class="col-12">

                <div class="card listing-block user-profile-card">

                    <div class="card-header view_header">

                        <div class="heading section-title">
                            <i class="fas fa-folder-open"></i>
                            <h5 class="mb-0">User Documents</h5>
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="row g-3">

                            @foreach($user->document as $key => $val)

                                @php
                                    $extension = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                                @endphp

                                <div class="col-xxl-3 col-xl-3 col-lg-4 col-md-4 col-sm-6 col-12">

                                    <div class="document-card">

                                        <a href="{{ url($val) }}"
                                           target="_blank"
                                           class="text-decoration-none">

                                            <div class="document-preview">

                                                @if(in_array($extension, ['png','jpg','jpeg','gif','svg']))

                                                    <img src="{{ General::renderImageUrl($val, 'large') }}"
                                                         alt="Document">

                                                @elseif($extension === 'pdf')

                                                    <img src="{{ url('admin/dev/images/pdf.png') }}"
                                                         alt="PDF">

                                                @elseif(in_array($extension, ['doc','docx']))

                                                    <img src="{{ url('admin/dev/images/docx.png') }}"
                                                         alt="Word Document">

                                                @else

                                                    <i class="fas fa-file document-icon"></i>

                                                @endif

                                            </div>

                                        </a>

                                        <div class="document-footer">

                                            <span class="text-uppercase">
                                                {{ $extension ?: 'file' }}
                                            </span>

                                            <a href="{{ url($val) }}"
                                               target="_blank"
                                               class="view-document">
                                                <i class="fas fa-eye me-1"></i>
                                                View
                                            </a>

                                        </div>

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    </div>

                </div>

            </div>

            @endif

        </div>

    </div>

</div>

@endsection
