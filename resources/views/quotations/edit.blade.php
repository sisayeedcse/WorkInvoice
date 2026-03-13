@extends('layouts.app')
@section('title', 'Edit ' . $quotation->quotation_number)
@section('page-title', 'Edit Quotation')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('quotations.index') }}">Quotations</a></li>
            <li class="breadcrumb-item"><a href="{{ route('quotations.show', $quotation) }}">{{ $quotation->quotation_number }}</a></li>
            <li class="breadcrumb-item active">Edit</li>
        </ol></nav>
        <h1>Edit {{ $quotation->quotation_number }}</h1>
    </div>
</div>

<form method="POST" action="{{ route('quotations.update', $quotation) }}" id="quoteForm">
@csrf @method('PUT')
<div class="row g-3">

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2" style="color:var(--accent);"></i>Customer</div>
            <div class="card-body">
                <label class="form-label">Customer <span class="text-danger">*</span></label>
                <select name="customer_id" class="form-select" required>
                    <option value="">— Select —</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id', $quotation->customer_id) == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}{{ $c->company_name ? ' ('.$c->company_name.')' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calendar me-2" style="color:var(--accent);"></i>Dates & Status</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" class="form-control"
                           value="{{ old('date', $quotation->date->format('Y-m-d')) }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Valid Until</label>
                    <input type="date" name="valid_until" class="form-control"
                           value="{{ old('valid_until', optional($quotation->valid_until)->format('Y-m-d')) }}">
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['draft','sent','accepted','rejected'] as $s)
                        <option value="{{ $s }}" {{ old('status', $quotation->status) == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
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
                           value="{{ old('discount', $quotation->discount) }}" step="0.01" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tax (%)</label>
                    <input type="number" name="tax" id="tax" class="form-control"
                           value="{{ old('tax', $quotation->tax) }}" step="0.1" min="0" max="100">
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold" style="font-size:15px;">
                    <span>Grand Total</span>
                    <span id="display-grand-total" style="color:var(--accent);">AED 0.00</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-8">
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-list-ul me-2" style="color:var(--accent);"></i>Line Items</span>
                <button type="button" class="btn btn-sm btn-primary rounded-pill" onclick="addRow()">
                    <i class="bi bi-plus-lg me-1"></i> Add Row
                </button>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr class="table-light">
                            <th style="min-width:180px;">Service / Item</th>
                            <th style="min-width:140px;">Description</th>
                            <th style="width:80px;">Qty</th>
                            <th style="width:95px;">Unit</th>
                            <th style="width:110px;">Price</th>
                            <th style="width:110px;">Total</th>
                            <th style="width:35px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        @foreach(old('items') ? [] : $quotation->items as $i => $item)
                        <tr class="item-row" data-index="{{ $i }}">
                            <td><input type="text" name="items[{{ $i }}][item_name]" class="form-control item-name-input" value="{{ $item->item_name }}" required></td>
                            <td><input type="text" name="items[{{ $i }}][description]" class="form-control" value="{{ $item->description }}"></td>
                            <td><input type="number" name="items[{{ $i }}][quantity]" class="form-control qty-input text-center" value="{{ $item->quantity }}" step="0.01" min="0.01" required></td>
                            <td><select name="items[{{ $i }}][unit]" class="form-select">
                                @foreach(['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'] as $u)
                                <option value="{{ $u }}" {{ $item->unit == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select></td>
                            <td><input type="number" name="items[{{ $i }}][unit_price]" class="form-control price-input" value="{{ $item->unit_price }}" step="0.01" min="0" required></td>
                            <td><input type="text" class="form-control total-input bg-light text-end fw-semibold" value="{{ number_format($item->total, 2) }}" readonly></td>
                            <td class="text-center"><span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill text-danger"></i></span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $quotation->notes) }}</textarea>
                </div>
                <div>
                    <label class="form-label">Terms & Conditions</label>
                    <textarea name="terms" class="form-control" rows="4">{{ old('terms', $quotation->terms) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Update Quotation</button>
            <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = {{ $quotation->items->count() }};

function addRow(name='',desc='',price='0.00',unit='Unit'){
    const units=['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'];
    const opts=units.map(u=>`<option value="${u}"${u===unit?' selected':''}>${u}</option>`).join('');
    const tr=document.createElement('tr');
    tr.className='item-row'; tr.dataset.index=rowIndex;
    tr.innerHTML=`
        <td><input type="text" name="items[${rowIndex}][item_name]" class="form-control item-name-input" value="${name}" required></td>
        <td><input type="text" name="items[${rowIndex}][description]" class="form-control" value="${desc}"></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input text-center" value="1" step="0.01" min="0.01" required></td>
        <td><select name="items[${rowIndex}][unit]" class="form-select">${opts}</select></td>
        <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control price-input" value="${price}" step="0.01" min="0" required></td>
        <td><input type="text" class="form-control total-input bg-light text-end fw-semibold" value="0.00" readonly></td>
        <td class="text-center"><span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill text-danger"></i></span></td>`;
    document.getElementById('items-container').appendChild(tr);
    bindRowEvents(tr); rowIndex++; calcTotals();
}
function removeRow(btn){
    if(document.querySelectorAll('.item-row').length<=1){alert('At least one item required.');return;}
    btn.closest('tr').remove(); calcTotals();
}
function bindRowEvents(row){
    row.querySelector('.qty-input').addEventListener('input',calcTotals);
    row.querySelector('.price-input').addEventListener('input',calcTotals);
}
function calcTotals(){
    let sub=0;
    document.querySelectorAll('.item-row').forEach(r=>{
        const q=parseFloat(r.querySelector('.qty-input')?.value)||0;
        const p=parseFloat(r.querySelector('.price-input')?.value)||0;
        const t=q*p; const ti=r.querySelector('.total-input');
        if(ti) ti.value=t.toFixed(2); sub+=t;
    });
    const disc=parseFloat(document.getElementById('discount')?.value)||0;
    const tax=parseFloat(document.getElementById('tax')?.value)||0;
    const ad=sub-disc; const gt=ad+(ad*tax/100);
    document.getElementById('display-subtotal').textContent='AED '+sub.toFixed(2);
    document.getElementById('display-grand-total').textContent='AED '+gt.toFixed(2);
}
document.querySelectorAll('.item-row').forEach(r=>bindRowEvents(r));
document.getElementById('discount')?.addEventListener('input',calcTotals);
document.getElementById('tax')?.addEventListener('input',calcTotals);
calcTotals();
</script>
@endpush
