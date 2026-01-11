@extends('products.layout', ['step' => $step, 'type' => $type, 'product' => $product])

@section('product-content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Review Product</h5>
                        <p class="text-muted mt-1">
                            Review all the information for your product. You can go back and edit any step.
                            When finished, publish the product or save it as a draft.
                        </p>
                    </div>
                    <div class="card-body">
                        @php
                            $editLink = function ($stepNumber) use ($product, $type) {
                                return route('product-management', ['type' => encrypt($type), 'step' => encrypt($stepNumber), 'id' => encrypt($product->id)]);
                            };
                        @endphp

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Step 1: Basics</h6>
                                <a href="{{ $editLink(1) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>Primary Image:</strong>
                                        @if ($reviewData['primaryImage'])
                                            <img src="{{ asset('storage/' . $reviewData['primaryImage']->file) }}"
                                                alt="Primary" class="img-fluid rounded mt-2" style="max-height: 150px;">
                                        @else
                                            <p class="text-muted">None</p>
                                        @endif
                                    </div>
                                    <div class="col-md-9">
                                        <p class="mb-1"><strong>Name:</strong> {{ $product->name }}</p>
                                        <p class="mb-1"><strong>SKU:</strong> {{ $product->sku }}</p>
                                        <p class="mb-1"><strong>Brand:</strong> {{ $reviewData['brand'] }}</p>
                                        <p class="mb-1"><strong>Status:</strong>
                                            {!! $product->status
                                                ? '<span class="badge bg-success">Active</span>'
                                                : '<span class="badge bg-danger">Inactive</span>' !!}
                                        </p>
                                        <p class="mb-1"><strong>Tags:</strong>
                                            @forelse ($product->tags as $tag)
                                                <span class="badge bg-secondary">{{ $tag }}</span>
                                            @empty
                                                <span class="text-muted">No tags</span>
                                            @endforelse
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Step 2: Bundle</h6>
                                <a href="{{ $editLink(2) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Pricing Configuration</h6>
                                        <p class="mb-1"><strong>Mode:</strong> {{ $reviewData['bundle']['pricing']['mode'] }}</p>
                                        @if($reviewData['bundle']['pricing']['mode'] == 'Fixed Price')
                                            <p class="mb-1"><strong>Fixed Price:</strong> ${{ number_format($reviewData['bundle']['pricing']['fixed_price'], 2) }}</p>
                                        @endif
                                        <p class="mb-1"><strong>Discount:</strong> 
                                            {{ $reviewData['bundle']['pricing']['discount_value'] }} 
                                            {{ $reviewData['bundle']['pricing']['discount_mode'] == 'Percentage' ? '%' : '$' }}
                                            ({{ $reviewData['bundle']['pricing']['discount_mode'] }})
                                        </p>
                                        <hr>
                                        <p class="mb-1"><strong>Subtotal:</strong> ${{ number_format($reviewData['bundle']['pricing']['subtotal'], 2) }}</p>
                                        <p class="mb-0 fs-5 text-primary"><strong>Final Price:</strong> ${{ number_format($reviewData['bundle']['pricing']['final_price'], 2) }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2">Bundle Items ({{ count($reviewData['bundle']['items']) }})</h6>
                                        <ul class="list-group list-group-flush">
                                            @forelse($reviewData['bundle']['items'] as $item)
                                                <li class="list-group-item px-0 py-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                                            <div class="text-muted small">
                                                                {{ $item['quantity'] }} x {{ $item['unit'] }} 
                                                                @ ${{ number_format($item['unit_price'], 2) }}
                                                            </div>
                                                        </div>
                                                        <div class="fw-semibold">
                                                            ${{ number_format($item['total_price'], 2) }}
                                                        </div>
                                                    </div>
                                                </li>
                                            @empty
                                                <li class="list-group-item px-0 text-muted">No items in bundle</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Step 6: Categories & SEO</h6>
                                <a href="{{ $editLink(3) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                            </div>
                            <div class="card-body">
                                <p class="mb-1"><strong>Primary Category:</strong>
                                    {{ $reviewData['primaryCategory'] }}</p>
                                <p class="mb-1"><strong>Additional Categories:</strong>
                                    {{ $reviewData['additionalCategories']->count() }}</p>
                                <p class="mb-1"><strong>SEO Title:</strong> {{ $reviewData['seo']['title'] ?? 'N/A' }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection