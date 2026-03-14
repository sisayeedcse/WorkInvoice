@extends('layouts.app')
@section('title', 'New Quotation')
@section('page-title', 'New Quotation')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1">
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
        <h1>Create New Quotation</h1>
        <p class="text-muted mb-0" style="font-size:13px;">Build a clear customer proposal with faster item entry and a cleaner financial summary.</p>
    </div>
</div>

<form method="POST" action="{{ route('quotations.store') }}" id="quoteForm">
@csrf
<div class="row g-3">

    <!-- Left: Customer & Header -->
    <div class="col-12 col-lg-4 sticky-summary">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2" style="color:var(--accent);"></i>Customer</div>
            <div class="card-body">
                <label class="form-label">Select Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                    <option value="">— Select Customer —</option>
                    @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                        {{ $customer->name }}{{ $customer->company_name ? ' ('.$customer->company_name.')' : '' }}
                    </option>
                    @endforeach
                </select>
                @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="mt-2">
                    <a href="{{ route('customers.create') }}" class="text-muted" style="font-size:12px;">
                        <i class="bi bi-plus-circle me-1"></i>Add new customer
                    </a>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calendar me-2" style="color:var(--accent);"></i>Dates</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Quotation Number</label>
                    <input type="text" name="quotation_number" class="form-control @error('quotation_number') is-invalid @enderror"
                           value="{{ old('quotation_number') }}" placeholder="Leave blank to auto-generate">
                    @error('quotation_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">If left blank, the system will generate it automatically.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Quotation Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control"
                           value="{{ old('date', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until" class="form-control"
                           value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}">
                </div>
                <div class="mt-3">
                    <label class="form-label">Prepared By</label>
                    <input type="text" name="prepared_by" class="form-control"
                           value="{{ old('prepared_by') }}" placeholder="Name of quotation maker">
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-calculator me-2" style="color:var(--accent);"></i>Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2" style="font-size:13.5px;">
                    <span class="text-muted">Subtotal</span>
                    <span id="display-subtotal" class="fw-semibold">AED 0.00</span>
                </div>
                <div class="mb-3">
                    <label class="form-label">Discount (AED)</label>
                    <input type="number" name="discount" id="discount" class="form-control"
                           value="{{ old('discount', '0') }}" step="0.01" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tax (%)</label>
                    <input type="number" name="tax" id="tax" class="form-control"
                           value="{{ old('tax', '0') }}" step="0.1" min="0" max="100">
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold" style="font-size:15px;">Grand Total</span>
                    <span id="display-grand-total" class="fw-bold" style="font-size:15px;color:var(--accent);">AED 0.00</span>
                </div>
            </div>
        </div>

        <div class="surface-note mt-3">
            <strong>Quotation flow</strong><br>
            Start with the customer, confirm dates, add services, and review totals before sending or converting later to an order.
        </div>
    </div>

    <!-- Right: Items + Notes -->
    <div class="col-12 col-lg-8">

        <!-- Services Quick Select -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-tools me-2" style="color:var(--accent);"></i>Quick Add from Library</span>
            </div>
            <div class="card-body pb-2">
                <div class="d-flex flex-wrap gap-2" id="quick-items-container">
                    @foreach($items->take(12) as $item)
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill quick-add-btn"
                            data-name="{{ $item->name }}"
                            data-price="{{ $item->default_price }}"
                            data-unit="{{ $item->unit }}"
                            data-desc="{{ $item->description }}">
                        <i class="bi bi-plus-circle me-1"></i>{{ $item->name }}
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Line Items</span>
                <button type="button" class="btn btn-sm btn-primary rounded-pill" onclick="addRow()">
                    <i class="bi bi-plus-lg me-1"></i> Add Row
                </button>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" id="items-table">
                    <thead>
                        <tr class="table-light">
                            <th style="min-width:180px;">Service / Item</th>
                            <th style="min-width:150px;">Description</th>
                            <th style="width:90px;">Qty</th>
                            <th style="width:100px;">Unit</th>
                            <th style="width:120px;">Unit Price</th>
                            <th style="width:110px;">Total</th>
                            <th style="width:40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        <tr class="item-row" data-index="0">
                            <td><input type="text" name="items[0][item_name]" class="form-control item-name-input" placeholder="Service name" required autocomplete="off"></td>
                            <td><input type="text" name="items[0][description]" class="form-control" placeholder="Optional"></td>
                            <td><input type="number" name="items[0][quantity]" class="form-control qty-input text-center" value="1" step="0.01" min="0.01" required></td>
                            <td><select name="items[0][unit]" class="form-select">
                                @foreach(['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'] as $u)
                                <option value="{{ $u }}">{{ $u }}</option>
                                @endforeach
                            </select></td>
                            <td><input type="number" name="items[0][unit_price]" class="form-control price-input" value="0.00" step="0.01" min="0" required></td>
                            <td><input type="text" class="form-control total-input bg-light text-end fw-semibold" value="0.00" readonly></td>
                            <td class="text-center"><span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill text-danger"></i></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes & Terms -->
        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2"
                              placeholder="Additional notes for this quotation...">{{ old('notes') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="4">{{ old('terms') }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-accent px-4">
                <i class="bi bi-check-lg me-1"></i> Save Quotation
            </button>
            <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = 1;

function addRow(name = '', desc = '', price = '0.00', unit = 'Unit') {
    const units = ['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'];
    const optionsHtml = units.map(u => `<option value="${u}" ${u===unit?'selected':''}>${u}</option>`).join('');

    const tr = document.createElement('tr');
    tr.className = 'item-row';
    tr.dataset.index = rowIndex;
    tr.innerHTML = `
        <td><input type="text" name="items[${rowIndex}][item_name]" class="form-control item-name-input" value="${name}" placeholder="Service name" required autocomplete="off"></td>
        <td><input type="text" name="items[${rowIndex}][description]" class="form-control" value="${desc}" placeholder="Optional"></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input text-center" value="1" step="0.01" min="0.01" required></td>
        <td><select name="items[${rowIndex}][unit]" class="form-select">${optionsHtml}</select></td>
        <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control price-input" value="${price}" step="0.01" min="0" required></td>
        <td><input type="text" class="form-control total-input bg-light text-end fw-semibold" value="0.00" readonly></td>
        <td class="text-center"><span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill text-danger"></i></span></td>`;

    document.getElementById('items-container').appendChild(tr);
    bindRowEvents(tr);
    rowIndex++;
    calcTotals();
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length <= 1) { alert('At least one item is required.'); return; }
    btn.closest('tr').remove();
    calcTotals();
}

function bindRowEvents(row) {
    row.querySelector('.qty-input').addEventListener('input', calcTotals);
    row.querySelector('.price-input').addEventListener('input', calcTotals);
}

function calcTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty   = parseFloat(row.querySelector('.qty-input')?.value)   || 0;
        const price = parseFloat(row.querySelector('.price-input')?.value) || 0;
        const total = qty * price;
        const totalInput = row.querySelector('.total-input');
        if (totalInput) totalInput.value = total.toFixed(2);
        subtotal += total;
    });

    const discount  = parseFloat(document.getElementById('discount')?.value)  || 0;
    const taxPct    = parseFloat(document.getElementById('tax')?.value)        || 0;
    const afterDisc = subtotal - discount;
    const grandTotal = afterDisc + (afterDisc * taxPct / 100);

    document.getElementById('display-subtotal').textContent   = 'AED ' + subtotal.toFixed(2);
    document.getElementById('display-grand-total').textContent = 'AED ' + grandTotal.toFixed(2);
}

// Bind events to existing rows on load
document.querySelectorAll('.item-row').forEach(row => bindRowEvents(row));
document.getElementById('discount')?.addEventListener('input', calcTotals);
document.getElementById('tax')?.addEventListener('input', calcTotals);
calcTotals();

// Quick-add buttons
document.querySelectorAll('.quick-add-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        addRow(this.dataset.name, this.dataset.desc || '', parseFloat(this.dataset.price).toFixed(2), this.dataset.unit);
    });
});
</script>
@endpush
