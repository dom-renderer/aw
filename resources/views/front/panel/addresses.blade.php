@extends('front.layout')

@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/select2.min.css')  }}">
<style>
    .address-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        transition: box-shadow 0.3s;
        position: relative;
    }
    .address-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .address-card .badge {
        position: absolute;
        top: 15px;
        right: 15px;
    }
    .empty-addresses {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-addresses img {
        max-width: 200px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="accoont-menu">
    <div class="container py-4">
        <div class="row">
            <!-- Sidebar (Desktop) -->
            <div class="col-lg-4 col-xl-3 mb-4 d-none d-lg-block">
                <div class="sidebar acount-leftbx c-lft-box bdr-clr nav flex-column nav-pills" id="v-pills-tab"
                    role="tablist" aria-orientation="vertical">
                    <h3 class="mb-4">Menu</h3>
                    <a href="{{ route('home') }}" class="nav-link">
                        <img src="{{ asset('front-theme/images/aacount-1.svg') }}" class="bar-img" alt=""> Dashboard
                    </a>
                    <a href="{{ route('wishlist') }}" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt=""> Wishlist
                    </a>
                    <a href="{{ route('addresses') }}" class="nav-link active">
                        <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt=""> Addresses
                    </a>
                    <a href="#" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt=""> Profile Settings
                    </a>
                    <a href="#" class="nav-link">
                        <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt=""> Payment Methods
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt=""> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Offcanvas Sidebar (Mobile) -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar"
                aria-labelledby="offcanvasSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title fw-bold" id="offcanvasSidebarLabel">Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body nav flex-column nav-pills" id="v-pills-tab-mobile" role="tablist">
                    <a href="{{ route('home') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/aacount-1.svg') }}" class="bar-img" alt=""> Dashboard
                    </a>
                    <a href="{{ route('wishlist') }}" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-3.svg') }}" alt=""> Wishlist
                    </a>
                    <a href="{{ route('addresses') }}" class="nav-link active" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-4.svg') }}" alt=""> Addresses
                    </a>
                    <a href="#" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-5.svg') }}" alt=""> Profile Settings
                    </a>
                    <a href="#" class="nav-link" data-bs-dismiss="offcanvas">
                        <img src="{{ asset('front-theme/images/menuicon-7.svg') }}" alt=""> Payment Methods
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" data-bs-dismiss="offcanvas">
                            <img src="{{ asset('front-theme/images/menuicon-9.svg') }}" alt=""> Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8 col-xl-9">
                <!-- Mobile Menu Button -->
                <button class="btn btn-primary mb-3 mobile-menu-btn menu-none-wb" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#offcanvasSidebar">
                    ☰ Menu
                </button>

                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>My Addresses</h2>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addressModal" onclick="resetForm()">
                            <i class="fa fa-plus"></i> Add New Address
                        </button>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($addresses->count() > 0)
                        <div class="addresses-list">
                            @foreach($addresses as $address)
                                <div class="address-card" id="address-card-{{ $address->id }}">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h5 class="mb-2">{{ $address->name }}</h5>
                                            <p class="mb-1"><strong>Code:</strong> {{ $address->code }}</p>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-primary edit-address" data-address="{{ json_encode($address) }}">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-address" data-id="{{ $address->id }}">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        </div>
                                    </div>
                                    <div class="address-details">
                                        <p class="mb-1"><strong>Address:</strong> {{ $address->address_line_1 }}</p>
                                        @if($address->address_line_2)
                                            <p class="mb-1">{{ $address->address_line_2 }}</p>
                                        @endif
                                        <p class="mb-1">
                                            @if($address->city){{ $address->city->name }}, @endif
                                            @if($address->state){{ $address->state->name }}, @endif
                                            @if($address->country){{ $address->country->name }} @endif
                                            {{ $address->zipcode }}
                                        </p>
                                        <p class="mb-1"><strong>Email:</strong> {{ $address->email }}</p>
                                        <p class="mb-1"><strong>Contact:</strong> {{ $address->contact_number }}</p>
                                        @if($address->fax)
                                            <p class="mb-1"><strong>Fax:</strong> {{ $address->fax }}</p>
                                        @endif
                                        @if($address->latitude && $address->longitude)
                                            <p class="mb-0"><strong>Coordinates:</strong> {{ $address->latitude }}, {{ $address->longitude }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-addresses">
                            <img src="{{ asset('front-theme/images/empty-address.png') }}" alt="No Addresses" onerror="this.style.display='none'">
                            <h4>No addresses found</h4>
                            <p class="text-muted">Add your first address to get started!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Address Modal -->
<div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addressModalLabel">Add New Address</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addressForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="address_id" id="addressId">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="address_line_1" class="form-label">Address Line 1 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="address_line_1" name="address_line_1" required>
                    </div>
                    <div class="mb-3">
                        <label for="address_line_2" class="form-label">Address Line 2</label>
                        <input type="text" class="form-control" id="address_line_2" name="address_line_2">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                            <select class="form-control" id="country_id" name="country_id" required>
                                <option value="">Select Country</option>
                                @foreach($countries as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="state_id" class="form-label">State <span class="text-danger">*</span></label>
                            <select class="form-control" id="state_id" name="state_id" required>
                                <option value="">Select State</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="city_id" class="form-label">City</label>
                            <select class="form-control" id="city_id" name="city_id">
                                <option value="">Select City</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="zipcode" class="form-label">Zipcode <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="zipcode" name="zipcode" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_number" class="form-label">Contact Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fax" class="form-label">Fax</label>
                            <input type="text" class="form-control" id="fax" name="fax">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" step="any" class="form-control" id="latitude" name="latitude">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" step="any" class="form-control" id="longitude" name="longitude">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Address</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('assets/js/select2.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#country_id').select2({
            allowClear: true,
            placeholder: 'Select country',
            width: '100%',
            dropdownParent: $('#addressModal')
        });
        
        $('#state_id').select2({
            allowClear: true,
            placeholder: 'Select state',
            dropdownParent: $('#addressModal'),
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
            dropdownParent: $('#addressModal'),
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

        // Handle form submission
        $('#addressForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();
            const method = $('#formMethod').val();
            const addressId = $('#addressId').val();
            let url = '{{ route("addresses.store") }}';
            
            if (method === 'PUT' && addressId) {
                url = '{{ route("addresses.update", ":id") }}'.replace(':id', addressId);
            }

            $.ajax({
                url: url,
                type: method === 'PUT' ? 'POST' : 'POST',
                data: formData + (method === 'PUT' ? '&_method=PUT' : ''),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#addressModal').modal('hide');
                        location.reload();
                    } else {
                        alert(response.message || 'An error occurred');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = 'Validation errors:\n';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + '\n';
                        });
                        alert(errorMsg);
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

        // Edit address
        $('.edit-address').on('click', function() {
            const address = $(this).data('address');
            $('#addressModalLabel').text('Edit Address');
            $('#formMethod').val('PUT');
            $('#addressId').val(address.id);
            $('#name').val(address.name);
            $('#code').val(address.code);
            $('#address_line_1').val(address.address_line_1);
            $('#address_line_2').val(address.address_line_2 || '');
            $('#country_id').val(address.country_id).trigger('change');
            
            setTimeout(function() {
                $('#state_id').val(address.state_id).trigger('change');
                setTimeout(function() {
                    $('#city_id').val(address.city_id);
                }, 500);
            }, 500);
            
            $('#zipcode').val(address.zipcode);
            $('#email').val(address.email);
            $('#contact_number').val(address.contact_number);
            $('#fax').val(address.fax || '');
            $('#latitude').val(address.latitude || '');
            $('#longitude').val(address.longitude || '');
            
            $('#addressModal').modal('show');
        });

        // Delete address
        $('.delete-address').on('click', function() {
            const addressId = $(this).data('id');
            
            if (!confirm('Are you sure you want to delete this address?')) {
                return;
            }

            $.ajax({
                url: '{{ route("addresses.delete", ":id") }}'.replace(':id', addressId),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#address-card-' + addressId).fadeOut(300, function() {
                            $(this).remove();
                            if ($('.address-card').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        alert(response.message || 'Failed to delete address');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        alert('Please login to continue');
                        window.location.href = '{{ route("login") }}';
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });

    function resetForm() {
        $('#addressModalLabel').text('Add New Address');
        $('#formMethod').val('POST');
        $('#addressId').val('');
        $('#addressForm')[0].reset();
        $('#state_id').html('<option value="">Select State</option>');
        $('#city_id').html('<option value="">Select City</option>');
    }
</script>
@endpush
