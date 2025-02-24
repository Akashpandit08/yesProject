@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="container">
    <h2 class="my-4 text-center">Add User</h2>
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-sm-12">
            <form action="{{ route('store') }}" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="mb-3">
                    <label class="form-label">Profile Image (JPG)</label>
                    <input type="file" name="profile_image" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" maxlength="25" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input id="phone" type="tel" name="phone" class="form-control" placeholder="+1-(XXX) XXX-XXXX" required>
                    <input type="hidden" name="full_phone" id="full_phone">
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Street Address</label>
                    <input type="text" name="street_address" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <select name="country" id="country" class="form-select" required>
                        <option value="">Loading...</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">State</label>
                    <select name="state" id="state" class="form-select" required>
                        <option value="">Select Country First</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>
    </div>
</div>

<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('get.countries') }}",
            type: "GET",
            success: function (response) {
                $("#country").html('<option value="">Select Country</option>');
                $.each(response, function (index, country) {
                    $("#country").append(
                        `<option value="${country.name}" data-code="${country.iso2}">${country.iso2}</option>`
                    );
                });
            },
            error: function () {
                $("#country").html('<option value="">Failed to load countries</option>');
            },
        });

        $("#country").change(function () {
            var countryName = $(this).val();
            if (!countryName) {
                $("#state").html('<option value="">Select Country First</option>');
                return;
            }
            var url = "{{ route('get.states', ':country') }}".replace(":country", encodeURIComponent(countryName));
            $("#state").html('<option value="">Loading...</option>');
            $.ajax({
                url: url,
                type: "GET",
                success: function (response) {
                    $("#state").html('<option value="">Select State</option>');
                    $.each(response, function (index, state) {
                        $("#state").append(`<option value="${state.name}">${state.state_code}</option>`);
                    });
                },
                error: function () {
                    $("#state").html('<option value="">Failed to load states</option>');
                },
            });
        });
    });
</script>

<!-- Include intl-tel-input JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
<script>
    const phoneInputField = document.querySelector("#phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
        hiddenInput: "full_phone",
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });

    phoneInputField.addEventListener('change', function() {
        document.querySelector("#full_phone").value = phoneInput.getNumber();
    });
</script>
@endsection
