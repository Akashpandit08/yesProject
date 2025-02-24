@extends('layouts.app')

@section('title', 'User List')

@section('content')
    <div class="container">
        <h2 class="my-4">User List</h2>
        <a href="{{ route('create') }}" class="btn btn-primary mb-3">Add User</a>
        <a href="{{ route('export.csv') }}" class="btn btn-success mb-3">Export CSV</a>
        
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Profile Image</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Street Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Country</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td><img src="{{ asset('uploads/' . $user->profile_image) }}" width="50"></td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->street_address }}</td>
                        <td>{{ $user->city }}</td>
                        <td>{{ $user->state }}</td>
                        <td>{{ $user->country }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
