@extends('layouts.app')

@section('title', 'Update User')

@section('content')
    <div class="container">
        <h2 class="my-4">Update User</h2>
        <form action="{{ route('update', $user->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT') <!-- Used for sending a PUT request -->
            
            <div class="mb-3">
                <label class="form-label">Profile Image (JPG)</label>
                <input type="file" name="profile_image" class="form-control">
                @if($user->profile_image)
                    <img src="{{ asset('uploads/' . $user->profile_image) }}" alt="Profile Image" class="mt-3" width="100">
                @endif
            </div>
            
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="{{ $user->name }}" maxlength="25" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="+1-(XXX) XXX-XXXX" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Street Address</label>
                <input type="text" name="street_address" class="form-control" value="{{ $user->street_address }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">City</label>
                <input type="text" name="city" class="form-control" value="{{ $user->city }}" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Country</label>
                <select name="country" id="country" class="form-control" required>
                    <option value="">Loading...</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label class="form-label">State</label>
                <select name="state" id="state" class="form-control" required>
                    <option value="">Select Country First</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>

    <!-- jQuery for AJAX -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {
            let selectedCountry = "{{ $user->country }}";
            let selectedState = "{{ $user->state }}";
           

            // Load countries
            $.ajax({
                url: "{{ route('get.countries') }}",
                type: "GET",
                success: function (response) {
                    $("#country").html('<option value="">Select Country</option>');
                    $.each(response, function (index, country) {
                        const selected = country.name === selectedCountry ? 'selected' : '';
                        $("#country").append(
                            `<option value="${country.name}" ${selected}>${country.iso2}</option>`
                        );
                    });

                    // Load states based on user's saved country
                    if (selectedCountry) {
                        loadStates(selectedCountry, selectedState);
                    }
                },
                error: function (xhr, status, error) {
                   
                    $("#country").html('<option value="">Failed to load countries</option>');
                },
            });

            // Load states when country is changed
            $("#country").change(function () {
                let countryIso2 = $(this).val();
                
                $("#state").html('<option value="">Loading...</option>');

                if (!countryIso2) {
                    $("#state").html('<option value="">Select Country First</option>');
                    return;
                }

                loadStates(countryIso2, "");
            });

            function loadStates(countryIso2, selectedState) {
                let url = "{{ route('get.states', ':country') }}".replace(":country", countryIso2);

                $.ajax({
                    url: url,
                    type: "GET",
                    success: function (response) {
                        $("#state").html('<option value="">Select State</option>');
                        $.each(response, function (index, state) {
                            const selected = state.name === selectedState ? 'selected' : '';
                            $("#state").append(`<option value="${state.name}" ${selected}>${state.state_code}</option>`);
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching states:", error);
                        $("#state").html('<option value="">Failed to load states</option>');
                    },
                });
            }
        });
    </script>
@endsection
