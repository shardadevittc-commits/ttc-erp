@extends('layouts.app')

@section('title', ucfirst(auth()->user()->role['short_name']) . ' Dashboard | TTC Robotronics - Steel Industry ERP')

@section('content')

@endsection

@push('scripts')

@endpush
