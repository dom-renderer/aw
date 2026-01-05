@extends('products.layout', ['step' => $step, 'type' => $type, 'product' => $product])

@push('product-css')
<style>
  .stepper {
    list-style: none;
    padding-left: 1rem;
    position: relative;
  }

  .stepper::before {
    content: "";
    position: absolute;
    top: 0;
    left: 12px;
    width: 2px;
    height: 100%;
    background: #dee2e6;
  }

  .step {
    position: relative;
    margin-bottom: 1rem;
    padding-left: 2rem;
  }

  .step::before {
    content: "";
    position: absolute;
    left: 4px;
    top: 4px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: #0d6efd;
  }
</style>
@endpush

@section('product-content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Inventory Settings</h4>
  </div>

  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" id="trackInventory" name="track_inventory_for_all_variant" @if($product->track_inventory_for_all_variant) checked @endif>
    <label class="form-check-label" for="trackInventory">Track inventory</label>
  </div>
  <div class="form-check form-check-inline">
    <input class="form-check-input" type="checkbox" id="allowBackorders" name="allow_backorder" @if($product->allow_backorder) checked @endif>
    <label class="form-check-label" for="allowBackorders">Allow backorders</label>
  </div>
  <div class="form-check form-check-inline mb-4">
    <input class="form-check-input" type="checkbox" id="autoReorder" name="enable_auto_reorder_alerts" @if($product->enable_auto_reorder_alerts) checked @endif>
    <label class="form-check-label" for="autoReorder">Enable auto-reorder alerts</label>
  </div>

  <div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
      <strong>Simple Product Inventory</strong>
      <button type="button" class="btn btn-sm btn-outline-primary" id="btnAddWarehouseSimple">
        <i class="fa fa-plus"></i> Add Warehouse / Location
      </button>
    </div>
    <div class="card-body">
      <table class="table align-middle table-bordered mb-0">
        <thead class="table-light">
          <tr>
            <th>Warehouse / Location</th>
            <th width="12%">Quantity</th>
            <th width="12%">Reorder</th>
            <th width="12%">Max Stock</th>
            <th>Notes</th>
            <th width="25%" class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody id="simpleInventoryBody"></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modals -->
<div class="modal fade" id="addWarehouseModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Warehouse / Location </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="warehouseSelect" class="form-label">Select Warehouse / Location</label>
          <select id="warehouseSelect" class="form-select">
            <option value="">Select Warehouse / Location</option>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="confirmAddWarehouse">Add</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="showInventoryWarehouseHistory" tabindex="-1" aria-labelledby="showInventoryWarehouseHistoryLabel" aria-hidden="true">
  <div class="modal-xl modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="showInventoryWarehouseHistoryLabel">Stock History</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-responsive w-100" id="historyTable">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">Type</th>
              <th scope="col">Quantity</th>
              <th scope="col">Note</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="adjustStockModal" tabindex="-1" aria-labelledby="adjustStockModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="adjustStockModalLabel">Adjust Stock</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="adjustStockLoader" class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
        <div id="adjustStockContent" style="display: none;">
          <input type="hidden" id="adjustVariantId" name="variant_id">
          <input type="hidden" id="adjustWarehouseId" name="warehouse_id">
          <div class="mb-3">
            <label class="form-label">Current Stock</label>
            <input type="text" class="form-control" id="currentStockDisplay" readonly disabled>
          </div>
          <div class="mb-3">
            <label for="newStockQty" class="form-label">New Stock Quantity <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="newStockQty" min="0" step="1">
            <div class="invalid-feedback">Please enter a valid non-negative number.</div>
          </div>
          <div class="mb-3">
            <label for="adjustNote" class="form-label">Note (Optional)</label>
            <textarea class="form-control" id="adjustNote" rows="3"></textarea>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="saveStockAdjustment">Save Changes</button>
      </div>
    </div>
  </div>
</div>
<!-- Modals -->
@endsection

@push('product-js')
<script>
  const allWarehouses = @json($warehouses);
  let locations = @json($simpleProductInventory ?? []);

  let currentProductId = {{ $product->id }};
  let currentWarehouseId = null;

  $(document).ready(function() {
    renderLocations();

    let historyTable = $('#historyTable').DataTable({
      pageLength: 10,
      searching: false,
      processing: true,
      serverSide: true,
      ajax: {
        "url": "{{ route('products.get-variant-stock-history') }}",
        "type": "GET",
        "data": {
          filter_product: function() {
            return currentProductId;
          },
          filter_warehouse: function() {
            return currentWarehouseId;
          },
          pt: 'simple'
        }
      },
      columns: [{
          data: 'DT_RowIndex',
          orderable: false,
          searchable: false,
        },
        {
          data: 'type',
        },
        {
          data: 'quantity',
        },
        {
          data: 'notes',
        },
      ],
      drawCallback: function() {
        if (currentProductId && currentWarehouseId) {
          $('#showInventoryWarehouseHistory').modal('show');
        }
      }
    });

    $(document).on('click', '.get-history', function() {
      currentWarehouseId = $(this).data('wid');

      if (!isNaN(currentProductId) && !isNaN(currentWarehouseId)) {
        historyTable.ajax.reload();
      }
    });


    $(document).on("click", ".btn-history", function() {
      const row = $(this).closest("tr");
      const historyRow = row.next(".warehouse-history");
      historyRow.find(".history").slideToggle();
    });

    $(document).on("click", ".btn-adjust", function() {
      const pid = $(this).data('pid');
      const wid = $(this).data('wid');

      currentWarehouseId = wid;

      $('#adjustStockModal').modal('show');
      $('#adjustStockLoader').show();
      $('#adjustStockContent').hide();
      $('#saveStockAdjustment').prop('disabled', true);

      $.ajax({
        url: "{{ route('products.adjust-stock') }}",
        type: "GET",
        data: {
          product_id: pid,
          warehouse_id: wid,
          pt: 'simple'
        },
        success: function(response) {
          $('#currentStockDisplay').val(response.quantity);
          $('#newStockQty').val(response.quantity);
          $('#adjustNote').val('');

          $('#adjustStockLoader').hide();
          $('#adjustStockContent').show();
          $('#saveStockAdjustment').prop('disabled', false);
        },
        error: function() {
          alert('Failed to fetch stock information.');
          $('#adjustStockModal').modal('hide');
        }
      });
    });

    $('#saveStockAdjustment').click(function() {
      const form = $('#adjustStockForm');
      const qtyInput = $('#newStockQty');
      const noteInput = $('#adjustNote').val();
      const qty = parseFloat(qtyInput.val());

      if (isNaN(qty) || qty < 0) {
        qtyInput.addClass('is-invalid');
        return;
      }
      qtyInput.removeClass('is-invalid');

      const btn = $(this);
      btn.prop('disabled', true).text('Saving...');

      $.ajax({
        url: "{{ route('products.adjust-stock') }}",
        type: "POST",
        data: {
          warehouse_id: currentWarehouseId,
          product_id: currentProductId,
          quantity: qty,
          note: noteInput,
          pt: 'simple'
        },
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          if (response.success) {
            location.reload();
          } else {
            alert(response.message || 'Failed to update stock.');
            btn.prop('disabled', false).text('Save Changes');
          }
        },
        error: function() {
          alert('An error occurred while saving.');
          btn.prop('disabled', false).text('Save Changes');
        }
      });
    });

    $("#btnAddWarehouseSimple").on("click", function() {
      const used = locations.map(w => w.id);
      const available = allWarehouses.filter(w => !used.includes(w.id));
      const select = $("#warehouseSelect");
      select.empty();
      if (available.length) {
        select.append(`<option value="">Select Warehouse / Location</option>`);
        available.forEach(w => select.append(`<option value="${w.id}">${w.code} - ${w.name} (${w.type ? 'Warehouse' : 'Location'})</option>`));
      } else {
        select.append(`<option value="">No more warehouses / location available</option>`);
      }
      $("#addWarehouseModal").modal("show");
    });

    $("#confirmAddWarehouse").on("click", function() {
      const selectedWarehouse = $("#warehouseSelect").val();
      if (!selectedWarehouse) {
        alert("Please select a warehouse.");
        return;
      }
      if (locations.some(w => w.id == selectedWarehouse)) {
        alert("This warehouse is already added.");
        return;
      }

      const selectedWarehouseObject = allWarehouses.find(item => item.id == selectedWarehouse);
      if (!selectedWarehouseObject) return;

      const newWarehouse = {
        id: selectedWarehouseObject.id,
        name: `${selectedWarehouseObject.code} - ${selectedWarehouseObject.name}`,
        qty: 0,
        reorder: 0,
        max: 0,
        notes: "",
        lastUpdated: "—",
        history: []
      };
      locations.push(newWarehouse);
      const newRowHtml = getWarehouseRowHtml(newWarehouse);
      $("#simpleInventoryBody").append(newRowHtml);
      $("#addWarehouseModal").modal("hide");
    });

    function validateRow(row) {
      const qty = parseFloat(row.find(".qty").val());
      const reorder = parseFloat(row.find(".reorder").val());
      const max = parseFloat(row.find(".max").val());
      let valid = true;

      row.find("input").removeClass("is-invalid");

      if (isNaN(qty) || qty < 0) {
        row.find(".qty").addClass("is-invalid");
        valid = false;
      }
      if (isNaN(reorder) || reorder < 0) {
        row.find(".reorder").addClass("is-invalid");
        valid = false;
      }
      if (isNaN(max) || max < 0 || (max > 0 && reorder > max)) {
        row.find(".max").addClass("is-invalid");
        valid = false;
      }

      return valid;
    }

    function getWarehouseRowHtml(w) {
      return `
      <tr>
        <td>
          <input type="hidden" name="data[warehouse_id][]" value="${w.id}" />
          <strong>${w.name} (${w.type ? 'Warehouse' : 'Location'})</strong>
        </td>
        <td><input type="number" name="data[item_quantity][]" class="form-control qty" value="${w.qty}" /></td>
        <td><input type="number" name="data[item_reordering][]" class="form-control reorder" value="${w.reorder}" /></td>
        <td><input type="number" name="data[item_max][]" class="form-control max" value="${w.max}" /></td>
        <td><textarea class="form-control notes" name="data[item_notes][]" rows="1">${w.notes || ""}</textarea></td>
        <td class="text-end">
          <small class="text-muted d-block mb-1">Last updated: ${w.lastUpdated}</small>
          @if(auth()->guard('web')->user()->isAdmin() || auth()?->guard('web')?->user()?->can('products.get-variant-stock-history'))
          <button type="button" class="btn btn-sm btn-outline-primary btn-history get-history" data-pid="{{ $product->id }}" data-wid="${w.id}">View History</button>
          @endif
          @if(auth()->guard('web')->user()->isAdmin() || auth()?->guard('web')?->user()?->can('products.adjust-stock'))
          <button type="button" class="btn btn-sm btn-outline-success btn-adjust" data-pid="{{ $product->id }}" data-wid="${w.id}">Adjust Stock</button>
          @endif
        </td>
      </tr>
      <tr class="warehouse-history" style="display:none;">
        <td colspan="6">
          <div class="history mt-3">
            <ul class="stepper mb-0">
              ${
                w.history && w.history.length
                  ? w.history.map(h => `<li class="step">${h}</li>`).join("")
                  : "<li class='text-muted'>No history available</li>"
              }
            </ul>
          </div>
        </td>
      </tr>`;
    }

    function renderLocations() {
      const tbody = $("#simpleInventoryBody");
      tbody.empty();
      const rows = (locations || []).map(w => getWarehouseRowHtml(w)).join("");
      tbody.append(rows);
    }
  });
</script>
@endpush