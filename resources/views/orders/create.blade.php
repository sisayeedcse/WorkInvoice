@extends('layouts.app')
@section('title', 'New Order')
@section('page-title', 'New Order')

@section('content')
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-1" style="font-size:12px;">
            <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Orders</a></li>
            <li class="breadcrumb-item active">New</li>
        </ol></nav>
        <h1>Create New Order</h1>
    </div>
</div>

<form method="POST" action="{{ route('orders.store') }}">
@csrf
<div class="row g-3">
    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-person me-2" style="color:var(--accent);"></i>Customer</div>
            <div class="card-body">
                <select name="customer_id" class="form-select" required>
                    <option value="">— Select Customer —</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->name }}{{ $c->company_name ? ' ('.$c->company_name.')' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-calendar me-2" style="color:var(--accent);"></i>Dates</div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Order Date <span class="text-danger">*</span></label>
                    <input type="date" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
                </div>
                <div>
                    <label class="form-label">Expected Delivery</label>
                    <input type="date" name="delivery_date" class="form-control" value="{{ old('delivery_date') }}">
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
                    <input type="number" name="discount" id="discount" class="form-control" value="{{ old('discount', 0) }}" step="0.01" min="0">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tax (%)</label>
                    <input type="number" name="tax" id="tax" class="form-control" value="{{ old('tax', 0) }}" step="0.1" min="0">
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
                    <thead><tr class="table-light">
                        <th style="min-width:180px;">Service / Item</th>
                        <th style="min-width:130px;">Description</th>
                        <th style="width:80px;">Qty</th>
                        <th style="width:95px;">Unit</th>
                        <th style="width:110px;">Price</th>
                        <th style="width:110px;">Total</th>
                        <th style="width:35px;"></th>
                    </tr></thead>
                    <tbody id="items-container">
                        <tr class="item-row" data-index="0">
                            <td><input type="text" name="items[0][item_name]" class="form-control item-name-input" placeholder="Service name" required></td>
                            <td><input type="text" name="items[0][description]" class="form-control" placeholder="Optional"></td>
                            <td><input type="number" name="items[0][quantity]" class="form-control qty-input text-center" value="1" step="0.01" min="0.01" required></td>
                            <td><select name="items[0][unit]" class="form-select">
                                @foreach(['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'] as $u)
                                <option>{{ $u }}</option>@endforeach
                            </select></td>
                            <td><input type="number" name="items[0][unit_price]" class="form-control price-input" value="0.00" step="0.01" min="0" required></td>
                            <td><input type="text" class="form-control total-input bg-light text-end fw-semibold" value="0.00" readonly></td>
                            <td class="text-center"><span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill text-danger"></i></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any notes...">{{ old('notes') }}</textarea>
                </div>
                <div>
                    <label class="form-label">Delivery Information</label>
                    <textarea name="delivery_info" class="form-control" rows="2" placeholder="Delivery instructions...">{{ old('delivery_info') }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Save Order</button>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </div>
</div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex=1;
function addRow(){
    const units=['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'];
    const opts=units.map(u=>`<option>${u}</option>`).join('');
    const tr=document.createElement('tr'); tr.className='item-row'; tr.dataset.index=rowIndex;
    tr.innerHTML=`<td><input type="text" name="items[${rowIndex}][item_name]" class="form-control item-name-input" placeholder="Service name" required></td>
        <td><input type="text" name="items[${rowIndex}][description]" class="form-control" placeholder="Optional"></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="form-control qty-input text-center" value="1" step="0.01" min="0.01" required></td>
        <td><select name="items[${rowIndex}][unit]" class="form-select">${opts}</select></td>
        <td><input type="number" name="items[${rowIndex}][unit_price]" class="form-control price-input" value="0.00" step="0.01" min="0" required></td>
        <td><input type="text" class="form-control total-input bg-light text-end fw-semibold" value="0.00" readonly></td>
        <td class="text-center"><span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill text-danger"></i></span></td>`;
    document.getElementById('items-container').appendChild(tr); bindRow(tr); rowIndex++; calc();
}
function removeRow(btn){ if(document.querySelectorAll('.item-row').length<=1){alert('Need at least one item.');return;} btn.closest('tr').remove(); calc(); }
function bindRow(r){ r.querySelector('.qty-input').addEventListener('input',calc); r.querySelector('.price-input').addEventListener('input',calc); }
function calc(){
    let s=0;
    document.querySelectorAll('.item-row').forEach(r=>{
        const q=parseFloat(r.querySelector('.qty-input')?.value)||0, p=parseFloat(r.querySelector('.price-input')?.value)||0, t=q*p;
        const ti=r.querySelector('.total-input'); if(ti)ti.value=t.toFixed(2); s+=t;
    });
    const d=parseFloat(document.getElementById('discount')?.value)||0, tx=parseFloat(document.getElementById('tax')?.value)||0;
    const ad=s-d, gt=ad+(ad*tx/100);
    document.getElementById('display-subtotal').textContent='AED '+s.toFixed(2);
    document.getElementById('display-grand-total').textContent='AED '+gt.toFixed(2);
}
document.querySelectorAll('.item-row').forEach(bindRow);
document.getElementById('discount')?.addEventListener('input',calc);
document.getElementById('tax')?.addEventListener('input',calc);
calc();
</script>
@endpush
