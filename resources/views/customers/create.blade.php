@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@push('css')
<style>
    div.iti--inline-dropdown {
        min-width: 100%!important;
    }
    .iti__selected-flag {
        height: 32px!important;
    }
    .iti--show-flags {
        width: 100%!important;
    }  
    label.error {
        color: red;
    }
    #phone_number{
        font-family: "Hind Vadodara",-apple-system,BlinkMacSystemFont,"Segoe UI","Helvetica Neue",Arial,sans-serif;
        font-size: 15px;
    }
    #map {
        height: 400px;
        width: 100%;
        border-radius: 8px;
        border: 1px solid #ddd;
    }
    .map-container {
        margin-top: 15px;
    }
    .search-container {
        margin-bottom: 15px;
    }
    .search-container input {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">Add New Customer</div>
            <div class="card-body">
                <form id="customerForm" method="POST" action="{{ route('customers.store') }}" enctype="multipart/form-data">
                    @csrf
                    
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="customer-tab" data-bs-toggle="tab" data-bs-target="#customer" type="button" role="tab" aria-controls="customer" aria-selected="true">Customer Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="locations-tab" data-bs-toggle="tab" data-bs-target="#locations" type="button" role="tab" aria-controls="locations" aria-selected="false">Locations</button>
                        </li>
                    </ul>

                    <div class="tab-content pt-3" id="myTabContent">
                        <div class="tab-pane fade show active" id="customer" role="tabpanel" aria-labelledby="customer-tab">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3 row">
                                <div class="col-12">
                                    <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label> <br>
                                    <input type="hidden" name="dial_code" id="dial_code">
                                    <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                                    @error('phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                    <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-primary" onclick="openLocationModal()">Add Location</button>
                            </div>
                            <table class="table table-bordered" id="locationsTable">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Code</th>
                                        <th>Address</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <input type="hidden" name="locations_json" id="locations_json">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Create Customer</button>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Location Modal -->
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Add Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="locationForm">
                    <input type="hidden" id="loc_index">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_code" class="form-label">Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_code" name="code" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="loc_email" name="email" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="loc_address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="loc_address_line_1" name="address_line_1" required>
                    </div>

                    <div class="mb-3">
                        <label for="loc_address_line_2" class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" id="loc_address_line_2" name="address_line_2">
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_country_id" class="form-label">Country <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="loc_country_id" name="country_id" required>
                                    <option value="">Select Country</option>
                                    @foreach($countries as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_state_id" class="form-label">State <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="loc_state_id" name="state_id" required>
                                    <option value="">Select State</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_city_id" class="form-label">City <span class="text-danger">*</span></label>
                                <select class="form-select select2" id="loc_city_id" name="city_id" required>
                                    <option value="">Select City</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_zipcode" class="form-label">Zipcode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_zipcode" name="zipcode" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="loc_contact_number" name="contact_number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="loc_fax" class="form-label">Fax</label>
                                <input type="text" class="form-control" id="loc_fax" name="fax">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Map Location</label>
                        <div class="search-container">
                            <input type="text" id="address_search" placeholder="Search for address..." class="form-control">
                        </div>
                        <div class="map-container">
                            <div id="map"></div>
                        </div>
                        <input type="hidden" id="latitude" name="latitude">
                        <input type="hidden" id="longitude" name="longitude">
                        <small class="form-text text-muted">Click on the map to set the exact location coordinates.</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveLocation()">Save Location</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/intel-tel.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
@endpush

@push('js')
<script src="{{ asset('assets/js/jquery-validate.min.js') }}"></script>
<script src="{{ asset('assets/js/intel-tel.js') }}"></script>
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
let map;
let marker;
let geocoder;
let locations = [];

function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: 40.7128, lng: -74.0060 },
        zoom: 10,
    });

    geocoder = new google.maps.Geocoder();
    marker = new google.maps.Marker({
        map: map,
        draggable: true,
    });

    map.addListener("click", (e) => {
        placeMarkerAndPanTo(e.latLng, map);
    });

    marker.addListener("dragend", () => {
        const position = marker.getPosition();
        document.getElementById("latitude").value = position.lat();
        document.getElementById("longitude").value = position.lng();
    });
}

function placeMarkerAndPanTo(latLng, map) {
    marker.setPosition(latLng);
    map.panTo(latLng);
    document.getElementById("latitude").value = latLng.lat();
    document.getElementById("longitude").value = latLng.lng();
}

function openLocationModal(index = null) {
    $('#locationForm')[0].reset();
    $('#loc_index').val('');
    $('#loc_country_id').val('').trigger('change');
    $('#loc_state_id').empty().trigger('change');
    $('#loc_city_id').empty().trigger('change');
    
    if (index !== null) {
        const loc = locations[index];
        $('#loc_index').val(index);
        $('#loc_name').val(loc.name);
        $('#loc_code').val(loc.code);
        $('#loc_email').val(loc.email);
        $('#loc_address_line_1').val(loc.address_line_1);
        $('#loc_address_line_2').val(loc.address_line_2);
        $('#loc_zipcode').val(loc.zipcode);
        $('#loc_contact_number').val(loc.contact_number);
        $('#loc_fax').val(loc.fax);
        $('#latitude').val(loc.latitude);
        $('#longitude').val(loc.longitude);
        
        $('#loc_country_id').val(loc.country_id).trigger('change');
        
        loadStates(loc.country_id, loc.state_id, function() {
            loadCities(loc.state_id, loc.city_id);
        });
        
        if (loc.latitude && loc.longitude) {
            const latLng = new google.maps.LatLng(parseFloat(loc.latitude), parseFloat(loc.longitude));
            placeMarkerAndPanTo(latLng, map);
        }
    }
    
    $('#locationModal').modal('show');
    setTimeout(() => {
        google.maps.event.trigger(map, "resize");
        if (index === null) {
            map.setCenter({ lat: 40.7128, lng: -74.0060 });
        }
    }, 500);
}

function loadStates(countryId, selectedStateId, callback) {
    $.ajax({
        url: "{{ route('state-list') }}",
        type: "POST",
        data: {
            country_id: countryId,
            _token: "{{ csrf_token() }}"
        },
        success: function(data) {
            var $state = $('#loc_state_id');
            $state.empty();
            $state.append('<option value="">Select State</option>');
            $.each(data.items, function(index, item) {
                $state.append(new Option(item.text, item.id, false, item.id == selectedStateId));
            });
            $state.trigger('change');
            if (callback) callback();
        }
    });
}

function loadCities(stateId, selectedCityId) {
    $.ajax({
        url: "{{ route('city-list') }}",
        type: "POST",
        data: {
            state_id: stateId,
            _token: "{{ csrf_token() }}"
        },
        success: function(data) {
            var $city = $('#loc_city_id');
            $city.empty();
            $city.append('<option value="">Select City</option>');
            $.each(data.items, function(index, item) {
                $city.append(new Option(item.text, item.id, false, item.id == selectedCityId));
            });
            $city.trigger('change');
        }
    });
}

function saveLocation() {
    if (!$('#locationForm').valid()) return;

    const locationData = {
        name: $('#loc_name').val(),
        code: $('#loc_code').val(),
        email: $('#loc_email').val(),
        address_line_1: $('#loc_address_line_1').val(),
        address_line_2: $('#loc_address_line_2').val(),
        country_id: $('#loc_country_id').val(),
        state_id: $('#loc_state_id').val(),
        city_id: $('#loc_city_id').val(),
        zipcode: $('#loc_zipcode').val(),
        contact_number: $('#loc_contact_number').val(),
        fax: $('#loc_fax').val(),
        latitude: $('#latitude').val(),
        longitude: $('#longitude').val(),
        // Store names for display
        country_name: $('#loc_country_id option:selected').text(),
        state_name: $('#loc_state_id option:selected').text(),
        city_name: $('#loc_city_id option:selected').text(),
    };

    const index = $('#loc_index').val();
    if (index !== '') {
        locations[index] = locationData;
    } else {
        locations.push(locationData);
    }

    renderLocationsTable();
    $('#locationModal').modal('hide');
}

function deleteLocation(index) {
    if (confirm('Are you sure you want to remove this location?')) {
        locations.splice(index, 1);
        renderLocationsTable();
    }
}

function renderLocationsTable() {
    const tbody = $('#locationsTable tbody');
    tbody.empty();
    locations.forEach((loc, index) => {
        tbody.append(`
            <tr>
                <td>${loc.name}</td>
                <td>${loc.code}</td>
                <td>${loc.address_line_1}, ${loc.city_name}, ${loc.state_name}, ${loc.country_name}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-info" onclick="openLocationModal(${index})">Edit</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteLocation(${index})">Delete</button>
                </td>
            </tr>
        `);
    });
    $('#locations_json').val(JSON.stringify(locations));
}

$(document).ready(function() {

    // Main Customer Form Logic
    $('#country_id').select2({
        allowClear: true,
        placeholder: 'Select country',
        width: '100%'
    });

    const input = document.querySelector('#phone_number');
    const iti = window.intlTelInput(input, {
        initialCountry: "in",
        separateDialCode:true,
        nationalMode:false,
        preferredCountries: @json(\App\Models\Country::select('iso2')->pluck('iso2')->toArray()),
        utilsScript: "{{ asset('assets/js/intel-tel-2.min.js') }}"
    });
    input.addEventListener("countrychange", function() {
        if (iti.isValidNumber()) {
            $('#dial_code').val(iti.s.dialCode);
        }
    });
    input.addEventListener('keyup', () => {
        if (iti.isValidNumber()) {
            $('#dial_code').val(iti.s.dialCode);
        }
    });

    $('#country_id').on('change', function() {
        const countryId = $(this).val();
        if (countryId == 20) {
            $('label[for="state_id"]').text('Parish');
            $('#city_id').parent().hide();
        } else {
            $('label[for="state_id"]').text('State');
            $('#city_id').parent().show();
        }
    });

    if ($('#country_id').val() == 20) {
        $('label[for="state_id"]').text('Parish');
        $('#city_id').parent().hide();
    }

    $('#state_id').select2({
        allowClear: true,
        placeholder: 'Select state',
        width: '100%',
        ajax: {
            url: "{{ route('state-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchQuery: params.term,
                    page: params.page || 1,
                    country_id: $('#country_id').val(),
                    _token: "{{ csrf_token() }}"
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    $('#city_id').select2({
        allowClear: true,
        placeholder: 'Select city',
        width: '100%',
        ajax: {
            url: "{{ route('city-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchQuery: params.term,
                    page: params.page || 1,
                    state_id: $('#state_id').val(),
                    _token: "{{ csrf_token() }}"
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    $('#customerForm').validate({
        rules: {
            name: { required: true },
            email: { required: true, email: true },
            phone_number: { required: true },
            country_id: { required: true },
            state_id: { required: true },
            city_id: { 
                required: function(element) {
                    return $('#country_id').val() != 20;
                }
            },
            status: { required: true },
            password: { required: true, minlength: 6 }
        },
        errorPlacement: function(error, element) {
            if (element.attr('id') === 'phone_number') {
                error.insertAfter(element.parent());
            } else {
                error.appendTo(element.parent());
            }
        },
        submitHandler: function (form) {
            $('#dial_code').val(iti.s.dialCode);
            form.submit();
        }
    });

    // Location Modal Logic
    $('#loc_country_id').select2({
        dropdownParent: $('#locationModal'),
        allowClear: true,
        placeholder: 'Select country',
        width: '100%'
    });

    $('#loc_country_id').on('change', function() {
        const countryId = $(this).val();
        if (countryId == 20) {
            $('label[for="loc_state_id"]').text('Parish');
            $('#loc_city_id').parent().parent().hide();
        } else {
            $('label[for="loc_state_id"]').text('State');
            $('#loc_city_id').parent().parent().show();
        }
    });

    $('#loc_state_id').select2({
        dropdownParent: $('#locationModal'),
        allowClear: true,
        placeholder: 'Select state',
        width: '100%',
        ajax: {
            url: "{{ route('state-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchQuery: params.term,
                    page: params.page || 1,
                    country_id: $('#loc_country_id').val(),
                    _token: "{{ csrf_token() }}"
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    $('#loc_city_id').select2({
        dropdownParent: $('#locationModal'),
        allowClear: true,
        placeholder: 'Select city',
        width: '100%',
        ajax: {
            url: "{{ route('city-list') }}",
            type: "POST",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchQuery: params.term,
                    page: params.page || 1,
                    state_id: $('#loc_state_id').val(),
                    _token: "{{ csrf_token() }}"
                };
            },
            processResults: function(data, params) {
                params.page = params.page || 1;
                return {
                    results: $.map(data.items, function(item) {
                        return {
                            id: item.id,
                            text: item.text
                        };
                    }),
                    pagination: {
                        more: data.pagination.more
                    }
                };
            },
            cache: true
        }
    });

    $('#address_search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            const address = $(this).val();
            if (address) {
                geocoder.geocode({ address: address }, (results, status) => {
                    if (status === "OK") {
                        const location = results[0].geometry.location;
                        placeMarkerAndPanTo(location, map);
                        map.setZoom(15);
                    } else {
                        alert("Geocode was not successful for the following reason: " + status);
                    }
                });
            }
        }
    });
});

window.initMap = initMap;
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_api_key', 'YOUR_API_KEY') }}&callback=initMap"></script>
@endpush
