@extends('products.layout', ['step' => $step, 'type' => $type, 'product' => $product])
@section('product-content')
    <div class="row g-4">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h6 class="mb-3">Variant Attributes</h6>
                    <div id="attributesWrapper" class="d-flex flex-column gap-3"></div>
                    <button type="button" class="btn btn-outline-secondary mt-2" id="addAttributeBtn">+ Add Attribute</button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Generated Variants</h6>
                        <small id="variantsCounter" class="text-muted"></small>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle" id="variantsTable">
                            <thead>
                                <tr>
                                    <th style="width: 120px;">Images</th>
                                    <th>Variant Name</th>
                                    <th>SKU</th>
                                    <th>Barcode</th>
                                    <th>Attributes</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="button" class="btn btn-outline-primary" id="generateBarcodesBtn">Generate Barcodes</button>
                        <button type="button" class="btn btn-outline-dark" id="enableAllBtn">Enable All</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@php
$attributes = \App\Models\ProductAttribute::where('product_id', $product->id)
    ->get()
    ->groupBy('title')
    ->map(fn($g) => $g->pluck('value')->toArray());
@endphp

@push('product-css')
<style>
    .variant-primary-preview {
        transition: transform 0.2s ease;
    }
    .variant-primary-preview:hover {
        transform: scale(1.05);
    }
    .variant-image-container {
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .variant-image-container:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    #secondaryImagesContainer .col-md-3 {
        transition: transform 0.2s ease;
    }
    #secondaryImagesContainer .col-md-3:hover {
        transform: translateY(-2px);
    }
    .delete-secondary-image {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    .modal-body .bg-light {
        background-color: #f8f9fa !important;
    }
    .progress {
        border-radius: 10px;
    }
    .progress-bar {
        transition: width 0.3s ease;
    }
</style>
@endpush

@push('product-js')
<script>
const savedAttributes = @json($attributes ?? []);

$(function(){
    const stepUrl = "{{ route('product-management', ['type' => encrypt($type), 'step' => encrypt(2), 'id' => encrypt($product->id)]) }}";

    function buildAttribute(title = '', values = []){
        const id = 'attr_'+Math.random().toString(36).slice(2);
        const $row = $('<div class="border rounded p-3">\
            <div class="d-flex align-items-center mb-2">\
                <div class="form-check me-2">\
                    <input class="form-check-input" type="checkbox" checked>\
                </div>\
                <input type="text" class="form-control form-control-sm me-2" placeholder="Attribute title (e.g., Size)" style="max-width:220px">\
                <div class="flex-grow-1 me-2">\
                    <select multiple class="form-select form-select-sm values"></select>\
                </div>\
                <button type="button" class="btn btn-sm btn-link text-danger remove-attr"><i class="fa fa-trash"></i></button>\
            </div>\
        </div>');
        $row.find('input[type=text]').val(title);
        const $select = $row.find('select.values');
        $select.select2({ tags: true, theme: 'bootstrap4', tokenSeparators: [','], placeholder: 'Add value...' });
        (values||[]).forEach(v=>{ $select.append(new Option(v,v,true,true)).trigger('change'); });
        $row.on('click', '.remove-attr', function(){ $row.remove(); updateCount(); });
        $('#attributesWrapper').append($row);
        return $row;
    }

    $('#addAttributeBtn').on('click', function(){ buildAttribute(); });

    function collectAttributes(){
        const out = [];
        $('#attributesWrapper > div').each(function(){
            const enabled = $(this).find('input[type=checkbox]').is(':checked');
            if(!enabled) return;
            const title = $(this).find('input[type=text]').val().trim();
            const values = $(this).find('select.values').val() || [];
            if(title && values.length) out.push({ title, values });
        });
        return out;
    }

    function updateCount(){
        const attrs = collectAttributes();
        let total = 0;
        if(attrs.length){
            total = attrs.reduce((acc, a)=> acc * (a.values?.length||0), 1);
        }
        $('#variantsCounter').text(total ? (total+ ' variants will be created') : '');
    }

    $('#attributesWrapper').on('change keyup', 'input,select', updateCount);

    function renderVariants(items){
        const $tb = $('#variantsTable tbody');
        $tb.empty();
        // Clean up old modals
        $('[id^="imageModal"]').remove();
        items.forEach(function(it){
            const primaryImage = it.primary_image || it.image || '{{ asset('public/assets/images/image_0.png') }}';
            const secondaryImages = it.secondary_images || [];
            
            const tr = $('<tr data-id="'+it.id+'">\
                <td>\
                    <div class="d-flex align-items-center gap-2">\
                        <div class="position-relative">\
                            <div class="ratio ratio-1x1 border rounded" style="width:50px; cursor:pointer;" data-bs-toggle="modal" data-bs-target="#imageModal'+it.id+'">\
                                <img class="w-100 h-100 rounded variant-primary-preview" style="object-fit:cover" src="'+primaryImage+'" data-variant-id="'+it.id+'">\
                            </div>\
                            <span class="badge bg-primary position-absolute top-0 start-0 translate-middle rounded-pill" style="font-size:0.6rem; padding:2px 4px;">P</span>\
                        </div>\
                        <div class="d-flex flex-column gap-1">\
                            <small class="text-muted" style="font-size:0.7rem;">'+(secondaryImages.length || 0)+' secondary</small>\
                            <button type="button" class="btn btn-sm btn-outline-primary btn-sm py-0 px-2" style="font-size:0.7rem;" data-bs-toggle="modal" data-bs-target="#imageModal'+it.id+'">\
                                <i class="fa fa-images"></i> Manage\
                            </button>\
                        </div>\
                    </div>\
                </td>\
                <td><input type="text" class="form-control form-control-sm inline name" value="'+(it.name||'')+'"></td>\
                <td style="max-width:170px"><input type="text" class="form-control form-control-sm inline sku" value="'+(it.sku||'')+'"></td>\
                <td style="max-width:170px"><input type="text" class="form-control form-control-sm inline barcode" value="'+(it.barcode||'')+'" placeholder="Enter barcode"></td>\
                <td>'+ (it.attributes||[]).map(v=>'<span class="badge bg-light text-dark me-1">'+v+'</span>').join('') +'</td>\
                <td>\
                    <div class="form-check form-switch">\
                        <input class="form-check-input inline status" type="checkbox" '+(it.status?'checked':'')+'>\
                    </div>\
                </td>\
                <td>\
                    <button type="button" class="btn btn-sm btn-outline-danger delete-variant"><i class="fa fa-trash"></i></button>\
                </td>\
            </tr>');
            $tb.append(tr);
            
            // Create modal for image management
            createImageModal(it.id, primaryImage, secondaryImages);
        });
    }
    
    function createImageModal(variantId, primaryImage, secondaryImages){
        if($('#imageModal'+variantId).length) return; // Modal already exists
        
        const modalHtml = '\
        <div class="modal fade" id="imageModal'+variantId+'" tabindex="-1" aria-labelledby="imageModalLabel'+variantId+'" aria-hidden="true">\
            <div class="modal-dialog modal-lg modal-dialog-scrollable">\
                <div class="modal-content">\
                    <div class="modal-header">\
                        <h5 class="modal-title" id="imageModalLabel'+variantId+'">Manage Variant Images</h5>\
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>\
                    </div>\
                    <div class="modal-body">\
                        <div class="mb-4">\
                            <label class="form-label fw-bold">Primary Image <span class="text-danger">*</span></label>\
                            <div class="border rounded p-3 bg-light">\
                                <div class="row align-items-center">\
                                    <div class="col-md-4">\
                                        <div class="ratio ratio-1x1 border rounded bg-white mb-2">\
                                            <img id="primaryPreview'+variantId+'" class="w-100 h-100 rounded" style="object-fit:cover" src="'+primaryImage+'">\
                                        </div>\
                                    </div>\
                                    <div class="col-md-8">\
                                        <input type="file" accept="image/*" class="form-control mb-2 variant-primary-upload" data-variant-id="'+variantId+'" id="primaryUpload'+variantId+'">\
                                        <small class="text-muted">Upload a single primary image (JPG, PNG, WEBP - Max 5MB)</small>\
                                        <div id="primaryProgress'+variantId+'" class="progress mt-2" style="display:none; height:6px;">\
                                            <div class="progress-bar" role="progressbar" style="width:0%"></div>\
                                        </div>\
                                    </div>\
                                </div>\
                            </div>\
                        </div>\
                        <div class="mb-3">\
                            <label class="form-label fw-bold">Secondary Images</label>\
                            <div class="border rounded p-3 bg-light">\
                                <input type="file" accept="image/*" multiple class="form-control mb-3 variant-secondary-upload" data-variant-id="'+variantId+'" id="secondaryUpload'+variantId+'">\
                                <small class="text-muted d-block mb-3">Upload multiple secondary images (JPG, PNG, WEBP - Max 5MB each)</small>\
                                <div id="secondaryProgress'+variantId+'" class="progress mb-3" style="display:none; height:6px;">\
                                    <div class="progress-bar" role="progressbar" style="width:0%"></div>\
                                </div>\
                                <div id="secondaryImagesContainer'+variantId+'" class="row g-2">\
                                </div>\
                            </div>\
                        </div>\
                    </div>\
                    <div class="modal-footer">\
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>\
                    </div>\
                </div>\
            </div>\
        </div>';
        $('body').append(modalHtml);
        
        // Render existing secondary images
        renderSecondaryImages(variantId, secondaryImages);
        
        // Refresh images when modal is opened
        $('#imageModal'+variantId).on('show.bs.modal', function(){
            refreshVariantImages(variantId);
        });
    }
    
    function refreshVariantImages(variantId){
        $.ajax({
            url: stepUrl,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                op: 'get-variant-images',
                id: variantId
            },
            success: function(res){
                if(res.primary_image){
                    $('#primaryPreview'+variantId).attr('src', res.primary_image);
                    $('.variant-primary-preview[data-variant-id="'+variantId+'"]').attr('src', res.primary_image);
                }
                if(res.secondary_images){
                    renderSecondaryImages(variantId, res.secondary_images);
                    const $tr = $('#variantsTable tbody tr[data-id="'+variantId+'"]');
                    $tr.find('small.text-muted').text(res.secondary_images.length + ' secondary');
                }
            }
        });
    }
    
    function renderSecondaryImages(variantId, images){
        const $container = $('#secondaryImagesContainer'+variantId);
        $container.empty();
        
        if(!images || images.length === 0){
            $container.html('<div class="col-12"><p class="text-muted text-center mb-0">No secondary images uploaded yet.</p></div>');
            return;
        }
        
        images.forEach(function(img, index){
            const imgHtml = '\
            <div class="col-md-3 col-6" data-image-id="'+img.id+'">\
                <div class="position-relative border rounded p-2 bg-white">\
                    <div class="ratio ratio-1x1 mb-2">\
                        <img class="w-100 h-100 rounded" style="object-fit:cover" src="'+img.url+'">\
                    </div>\
                    <button type="button" class="btn btn-sm btn-danger w-100 delete-secondary-image" data-image-id="'+img.id+'" data-variant-id="'+variantId+'">\
                        <i class="fa fa-trash"></i> Delete\
                    </button>\
                </div>\
            </div>';
            $container.append(imgHtml);
        });
    }

    function generateVariants(){
        const attributes = collectAttributes();
        if(!attributes.length){
            if(window.Swal) Swal.fire('Attributes required','','warning');
            return;
        }
        $.ajax({
            url: stepUrl,
            method: 'POST',
            data: { _token: '{{ csrf_token() }}', op: 'generate', attributes },
            success: function(res){ renderVariants(res.items || []); updateCount(); },
            error: function(){ if(window.Swal) Swal.fire('Failed to generate','','error'); }
        });
    }

    $('#addAttributeBtn').after('<button type="button" class="btn btn-primary ms-2" id="generateBtn">Generate Variants</button>');
    $(document).on('click','#generateBtn', generateVariants);

    $(document).on('change','.inline.status', function(){
        const id = $(this).closest('tr').data('id');
        $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'inline', id, field:'status', value: $(this).is(':checked') ? 1 : 0 });
    });
    $(document).on('change','.inline.name', function(){
        const id = $(this).closest('tr').data('id');
        $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'inline', id, field:'name', value: $(this).val() });
    });
    $(document).on('change','.inline.sku', function(){
        const id = $(this).closest('tr').data('id');
        $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'inline', id, field:'sku', value: $(this).val() });
    });
    $(document).on('change','.inline.barcode', function(){
        const id = $(this).closest('tr').data('id');
        $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'inline', id, field:'barcode', value: $(this).val() });
    });

    $(document).on('click','.delete-variant', function(){
        const $tr = $(this).closest('tr');
        const id = $tr.data('id');
        const run = () => $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'delete', id }, function(){ $tr.remove(); });
        if(window.Swal) Swal.fire({title:'Delete variant?',icon:'warning',showCancelButton:true}).then(r=>{ if(r.isConfirmed) run(); }); else run();
    });

    // Primary image upload handler
    $(document).on('change', '.variant-primary-upload', function(){
        const variantId = $(this).data('variant-id');
        const file = this.files[0];
        if(!file) return;
        
        // Validate file
        if(!file.type.match('image.*')){
            if(window.Swal) Swal.fire('Invalid file','Please select an image file','error');
            return;
        }
        if(file.size > 5 * 1024 * 1024){
            if(window.Swal) Swal.fire('File too large','Maximum file size is 5MB','error');
            return;
        }
        
        const $progress = $('#primaryProgress'+variantId);
        const $progressBar = $progress.find('.progress-bar');
        $progress.show();
        $progressBar.css('width', '0%');
        
        const fd = new FormData();
        fd.append('_token','{{ csrf_token() }}');
        fd.append('op','upload-primary-image');
        fd.append('id', variantId);
        fd.append('file', file);
        
        $.ajax({ 
            url: stepUrl, 
            method: 'POST', 
            data: fd, 
            processData:false, 
            contentType:false,
            xhr: function(){
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e){
                    if(e.lengthComputable){
                        const percentComplete = (e.loaded / e.total) * 100;
                        $progressBar.css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(res){
                if(res.url){
                    $('#primaryPreview'+variantId).attr('src', res.url);
                    $('.variant-primary-preview[data-variant-id="'+variantId+'"]').attr('src', res.url);
                    $progress.hide();
                    if(window.Swal) Swal.fire('Success','Primary image uploaded successfully','success');
                }
            }, 
            error: function(xhr){
                $progress.hide();
                const msg = xhr.responseJSON?.message || 'Upload failed';
                if(window.Swal) Swal.fire('Error', msg, 'error');
            }
        });
    });
    
    // Secondary images upload handler
    $(document).on('change', '.variant-secondary-upload', function(){
        const variantId = $(this).data('variant-id');
        const files = this.files;
        if(!files || files.length === 0) return;
        
        // Validate files
        let validFiles = [];
        for(let i = 0; i < files.length; i++){
            const file = files[i];
            if(!file.type.match('image.*')){
                if(window.Swal) Swal.fire('Invalid file','File '+(i+1)+' is not an image','error');
                continue;
            }
            if(file.size > 5 * 1024 * 1024){
                if(window.Swal) Swal.fire('File too large','File '+(i+1)+' exceeds 5MB limit','error');
                continue;
            }
            validFiles.push(file);
        }
        
        if(validFiles.length === 0) return;
        
        const $progress = $('#secondaryProgress'+variantId);
        const $progressBar = $progress.find('.progress-bar');
        $progress.show();
        $progressBar.css('width', '0%');
        
        const fd = new FormData();
        fd.append('_token','{{ csrf_token() }}');
        fd.append('op','upload-secondary-images');
        fd.append('id', variantId);
        validFiles.forEach(function(file){
            fd.append('files[]', file);
        });
        
        $.ajax({ 
            url: stepUrl, 
            method: 'POST', 
            data: fd, 
            processData:false, 
            contentType:false,
            xhr: function(){
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e){
                    if(e.lengthComputable){
                        const percentComplete = (e.loaded / e.total) * 100;
                        $progressBar.css('width', percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            success: function(res){
                $progress.hide();
                if(res.images && res.images.length > 0){
                    renderSecondaryImages(variantId, res.images);
                    // Update secondary count in table
                    const $tr = $('#variantsTable tbody tr[data-id="'+variantId+'"]');
                    $tr.find('small.text-muted').text(res.images.length + ' secondary');
                    if(window.Swal) Swal.fire('Success', res.images.length + ' image(s) uploaded successfully', 'success');
                }
            }, 
            error: function(xhr){
                $progress.hide();
                const msg = xhr.responseJSON?.message || 'Upload failed';
                if(window.Swal) Swal.fire('Error', msg, 'error');
            }
        });
        
        // Reset file input
        $(this).val('');
    });
    
    // Delete secondary image handler
    $(document).on('click', '.delete-secondary-image', function(){
        const imageId = $(this).data('image-id');
        const variantId = $(this).data('variant-id');
        const $imgContainer = $(this).closest('[data-image-id="'+imageId+'"]');
        
        const run = () => {
            $.ajax({
                url: stepUrl,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    op: 'delete-image',
                    id: imageId,
                    variant_id: variantId
                },
                success: function(res){
                    $imgContainer.remove();
                    // Update secondary count in table
                    const $tr = $('#variantsTable tbody tr[data-id="'+variantId+'"]');
                    const remaining = $('#secondaryImagesContainer'+variantId+' [data-image-id]').length;
                    $tr.find('small.text-muted').text(remaining + ' secondary');
                    if(window.Swal) Swal.fire('Success','Image deleted successfully','success');
                },
                error: function(xhr){
                    const msg = xhr.responseJSON?.message || 'Delete failed';
                    if(window.Swal) Swal.fire('Error', msg, 'error');
                }
            });
        };
        
        if(window.Swal) {
            Swal.fire({
                title: 'Delete image?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(r => { if(r.isConfirmed) run(); });
        } else {
            run();
        }
    });

    $('#generateBarcodesBtn').on('click', function(){
        $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'generate-barcodes' }, function(){ refreshList(); });
    });

    function refreshList(){
        $.ajax({ url: stepUrl, method: 'POST', data: { _token:'{{ csrf_token() }}', op:'list' }, success: function(res){ renderVariants(res.items||[]); } });
    }

    $('#enableAllBtn').on('click', function(){
        $.post(stepUrl, { _token:'{{ csrf_token() }}', op:'enable-all' }, function(){
            $('#variantsTable tbody .inline.status').prop('checked', true);
        });
    });

    if (Object.keys(savedAttributes).length) {
        for (const [title, values] of Object.entries(savedAttributes)) {
            buildAttribute(title, values);
        }
    } else {
        buildAttribute();
    }
    updateCount();
    refreshList();
});
</script>
@endpush