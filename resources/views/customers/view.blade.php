@extends('layouts.app', ['title' => $title, 'subTitle' => $subTitle])

@section('content')
<div class="row">
    <div class="col-md-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Details</h5>
            </div>
            <div class="card-body">
                
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
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <th width="30%">Name:</th>
                                        <td>{{ $customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $customer->email }}</td>
                                    </tr>
                                    <tr>
                                        <th>Phone Number:</th>
                                        <td>+{{ $customer->dial_code }} {{ $customer->phone_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($customer->status)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Created At:</th>
                                        <td>{{ $customer->created_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                    <tr>
                                        <th>Updated At:</th>
                                        <td>{{ $customer->updated_at->format('M d, Y H:i A') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="locations" role="tabpanel" aria-labelledby="locations-tab">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Address</th>
                                    <th>Coordinates</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->locations as $location)
                                    <tr>
                                        <td>{{ $location->name }}</td>
                                        <td>{{ $location->code }}</td>
                                        <td>
                                            {{ $location->address_line_1 }}
                                            @if($location->address_line_2), {{ $location->address_line_2 }} @endif
                                            <br>
                                            {{ $location->city ? $location->city->name : '' }}, 
                                            {{ $location->state ? $location->state->name : '' }}, 
                                            {{ $location->country ? $location->country->name : '' }} - {{ $location->zipcode }}
                                        </td>
                                        <td>
                                            @if($location->latitude && $location->longitude)
                                                {{ $location->latitude }}, {{ $location->longitude }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No locations found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary">Back to List</a>
                    @if(auth()->guard('web')->user()->can('customers.edit'))
                        <a href="{{ route('customers.edit', encrypt($customer->id)) }}" class="btn btn-primary">Edit Customer</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
