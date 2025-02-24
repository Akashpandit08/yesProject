@extends('layouts.app')

@section('title', 'User List')

@section('content')
    <div class="container">

        <div class="d-flex flex-wrap justify-content-between mb-3">
            <a href="{{ route('create') }}" class="btn btn-primary">Add User</a>
            <a href="{{ route('export.csv') }}" class="btn btn-success">Export CSV</a>
            <a href="{{ route('users.download') }}" class="btn btn-info">Download Empty CSV</a>
        </div>

        {{-- CSV Import Form --}}
        <div class="card p-3 mb-4">
            <form action="{{ route('users.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="input-group">
                    <input type="file" name="csv_file" class="form-control" required>
                    <button type="submit" class="btn btn-warning">Import CSV</button>
                </div>
            </form>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Responsive Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
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
                            <td class="text-center">
                                <img src="{{ asset('uploads/' . $user->profile_image) }}" width="50" class="rounded-circle">
                            </td>
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
    </div>
@endsection
