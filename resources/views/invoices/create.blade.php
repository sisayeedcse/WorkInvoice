@extends('layouts.app')
@section('title', 'New Invoice')
@section('page-title', 'New Invoice')

@push('styles')
    <style>
        .qf-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: var(--radius-xl);
            padding: 28px 32px;
            margin-bottom: 28px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 8px 32px rgba(31, 47, 135, .22);
        }

        .qf-hero-left h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0 0 4px;
            color: #fff;
        }

        .qf-hero-left p {
            font-size: .825rem;
            opacity: .78;
            margin: 0;
        }

        .qf-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255, 255, 255, .15);
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: var(--radius-full);
            padding: 5px 14px;
            font-size: .75rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .qf-hero-steps {
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, .12);
            border-radius: var(--radius-full);
            padding: 4px 8px;
        }

        .qf-step {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .72rem;
            font-weight: 600;
            opacity: .65;
            padding: 4px 10px;
            border-radius: var(--radius-full);
            transition: all .2s;
        }

        .qf-step.active {
            background: rgba(255, 255, 255, .25);
            opacity: 1;
        }

        .qf-step-num {
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .65rem;
        }

        .qf-step.active .qf-step-num {
            background: #fff;
            color: var(--primary);
        }

        .qf-step-sep {
            width: 20px;
            height: 1px;
            background: rgba(255, 255, 255, .3);
            margin: 0 2px;
        }

        .qf-section {
            background: var(--bg-primary);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            margin-bottom: 20px;
            overflow: hidden;
        }

        .qf-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            background: linear-gradient(180deg, #fff 0%, var(--gray-50) 100%);
            border-bottom: 1px solid var(--border-primary);
        }

        .qf-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .925rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .qf-section-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            background: var(--accent-200);
            color: var(--accent-800);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
        }

        .qf-section-badge {
            font-size: .7rem;
            font-weight: 700;
            color: var(--gray-600);
            background: var(--gray-100);
            border-radius: var(--radius-full);
            padding: 3px 12px;
            border: 1px solid var(--gray-300);
        }

        .qf-section-body {
            padding: 24px;
        }

        .qf-label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 6px;
            letter-spacing: .02em;
        }

        .qf-label .req {
            color: var(--danger);
            margin-left: 2px;
        }

        .qf-input-wrap {
            position: relative;
        }

        .qf-input-wrap .qf-input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            font-size: .9rem;
            pointer-events: none;
            z-index: 2;
        }

        .qf-input-wrap .form-control,
        .qf-input-wrap .form-select {
            padding-left: 36px;
        }

        .qf-customer-select {
            font-size: .9rem !important;
            border: 1.5px solid var(--border-primary) !important;
            border-radius: var(--radius-md) !important;
            padding: 10px 12px 10px 36px !important;
            transition: border-color .18s, box-shadow .18s !important;
        }

        .qf-customer-select:focus {
            border-color: var(--primary-light) !important;
            box-shadow: 0 0 0 3px rgba(47, 67, 206, .1) !important;
        }

        .qf-details-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
        }

        @media (max-width: 992px) {
            .qf-details-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 576px) {
            .qf-details-grid {
                grid-template-columns: 1fr;
            }
        }

        .qf-table-wrap {
            overflow-x: auto;
        }

        .qf-table {
            width: 100%;
            border-collapse: collapse;
        }

        .qf-table thead th {
            background: var(--gray-100);
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--gray-700);
            padding: 11px 12px;
            border-bottom: 2px solid var(--gray-300);
            white-space: nowrap;
        }

        .qf-table thead th:first-child {
            padding-left: 16px;
        }

        .qf-table tbody tr.item-row {
            transition: background .15s;
            border-bottom: 1px solid var(--gray-100);
        }

        .qf-table tbody tr.item-row:last-child {
            border-bottom: none;
        }

        .qf-table tbody tr.item-row:hover {
            background: var(--gray-50);
        }

        .qf-table td {
            padding: 9px 10px;
            vertical-align: middle;
        }

        .qf-table td:first-child {
            padding-left: 16px;
        }

        .qf-table td .form-control,
        .qf-table td .form-select {
            font-size: .85rem;
            border-radius: var(--radius-sm);
            padding: 7px 10px;
            border-color: var(--gray-200);
            transition: border-color .15s;
        }

        .qf-table td .form-control:focus,
        .qf-table td .form-select:focus {
            border-color: var(--primary-300);
            box-shadow: 0 0 0 2.5px rgba(47, 67, 206, .08);
        }

        .qf-row-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: var(--gray-200);
            color: var(--gray-700);
            font-size: .7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .qf-row-total {
            font-size: .9rem;
            font-weight: 700;
            color: var(--primary-700);
            text-align: right;
            padding-right: 6px;
            white-space: nowrap;
        }

        .qf-remove-btn {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: transparent;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-400);
            font-size: .9rem;
            transition: all .15s;
            margin: 0 auto;
        }

        .qf-remove-btn:hover {
            background: var(--danger-light);
            color: var(--danger);
        }

        .qf-add-row-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin: 14px 16px 16px;
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-size: .8rem;
            font-weight: 600;
            background: transparent;
            border: 1.5px dashed var(--primary-300);
            color: var(--primary-600);
            cursor: pointer;
            transition: all .16s;
        }

        .qf-add-row-btn:hover {
            background: var(--primary-50);
            border-style: solid;
            border-color: var(--primary-400);
            transform: translateY(-1px);
        }

        .qf-totals-panel {
            border-top: 2px solid var(--gray-200);
            background: var(--gray-50);
            padding: 20px 24px;
            display: flex;
            justify-content: flex-end;
            gap: 0;
        }

        .qf-totals-inner {
            width: 100%;
            max-width: 520px;
        }

        .qf-totals-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--gray-200);
            gap: 16px;
        }

        .qf-totals-row:last-child {
            border-bottom: none;
        }

        .qf-totals-label {
            font-size: .85rem;
            font-weight: 600;
            color: var(--gray-600);
            white-space: nowrap;
        }

        .qf-totals-value {
            font-size: .9rem;
            font-weight: 700;
            color: var(--text-primary);
            white-space: nowrap;
        }

        .qf-totals-input-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid var(--gray-200);
            gap: 16px;
        }

        .qf-totals-input-row label {
            font-size: .85rem;
            font-weight: 600;
            color: var(--gray-600);
            white-space: nowrap;
            margin: 0;
            flex-shrink: 0;
        }

        .qf-totals-input {
            width: 140px;
            text-align: right;
            font-size: .875rem !important;
            font-weight: 600 !important;
            border: 1.5px solid var(--gray-300) !important;
            border-radius: var(--radius-sm) !important;
            padding: 6px 10px !important;
        }

        .qf-totals-input:focus {
            border-color: var(--primary-300) !important;
            box-shadow: 0 0 0 2.5px rgba(47, 67, 206, .08) !important;
        }

        .qf-grand-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0 4px;
            margin-top: 4px;
        }

        .qf-grand-total-label {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .qf-grand-total-value {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -.02em;
        }

        .qf-tip {
            background: var(--primary-50);
            border: 1px solid var(--primary-100);
            border-left: 4px solid var(--primary-300);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            font-size: .8rem;
            color: var(--primary-700);
            line-height: 1.55;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }

        .qf-tip i {
            font-size: 1rem;
            margin-top: 1px;
            flex-shrink: 0;
            color: var(--primary-400);
        }

        .qf-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            padding: 20px 24px;
            background: var(--gray-50);
            border-top: 1px solid var(--border-primary);
        }

        .btn-qf-save {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-hover) 100%);
            color: #fff;
            border: none;
            border-radius: var(--radius-full);
            padding: 11px 32px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            transition: all .18s;
            box-shadow: 0 4px 14px rgba(240, 141, 34, .3);
        }

        .btn-qf-save:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(240, 141, 34, .4);
            color: #fff;
        }

        .btn-qf-save:active {
            transform: translateY(0);
        }

        .btn-qf-cancel {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1.5px solid var(--border-primary);
            color: var(--text-secondary);
            border-radius: var(--radius-full);
            padding: 10px 24px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .16s;
        }

        .btn-qf-cancel:hover {
            background: var(--gray-100);
            color: var(--text-primary);
        }

        @media (max-width: 991px) {
            .qf-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 22px 20px;
            }

            .qf-hero-steps {
                display: none !important;
            }

            .qf-totals-inner {
                max-width: 100%;
            }
        }

        @media (max-width: 640px) {
            .qf-hero-left h1 {
                font-size: 1.2rem;
                color: #fff;
            }

            .qf-totals-input-row {
                flex-wrap: wrap;
                gap: 8px;
            }

            .qf-totals-input {
                width: 100%;
                text-align: left;
            }
        }
    </style>
@endpush

@section('content')

    <div class="qf-hero">
        <div class="qf-hero-left">
            <div class="qf-hero-badge mb-2">
                <i class="bi bi-receipt"></i> New Invoice
            </div>
            <h1>Create Invoice</h1>
            <p>Add customer details, billing dates, and line items to generate a polished invoice.</p>
        </div>
        <div class="qf-hero-steps d-none d-lg-flex">
            <div class="qf-step active">
                <div class="qf-step-num">1</div><span>Customer</span>
            </div>
            <div class="qf-step-sep"></div>
            <div class="qf-step active">
                <div class="qf-step-num">2</div><span>Details</span>
            </div>
            <div class="qf-step-sep"></div>
            <div class="qf-step active">
                <div class="qf-step-num">3</div><span>Items</span>
            </div>
            <div class="qf-step-sep"></div>
            <div class="qf-step active">
                <div class="qf-step-num">4</div><span>Review</span>
            </div>
        </div>
    </div>

    <div class="qf-tip">
        <i class="bi bi-lightbulb-fill"></i>
        <div><strong>Workflow tip:</strong> Verify due date and totals before saving so payment tracking stays accurate from
            draft to paid status.</div>
    </div>

    <form method="POST" action="{{ route('invoices.store') }}" id="invoiceForm">
        @csrf

        <div class="qf-section">
            <div class="qf-section-header">
                <div class="qf-section-title">
                    <div class="qf-section-icon"><i class="bi bi-person-fill"></i></div>
                    Customer
                </div>
                <span class="qf-section-badge">Step 1</span>
            </div>
            <div class="qf-section-body">
                <div class="row g-4 align-items-end">
                    <div class="col-12 col-lg-6">
                        <label class="qf-label">Select Customer <span class="req">*</span></label>
                        <div class="qf-input-wrap">
                            <i class="bi bi-search qf-input-icon"></i>
                            <select name="customer_id"
                                class="form-select qf-customer-select @error('customer_id') is-invalid @enderror" required>
                                <option value="">— Choose a customer —</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}{{ $c->company_name ? ' · ' . $c->company_name : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('customer_id')<div class="invalid-feedback d-block mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-lg-6 d-flex align-items-end pb-1">
                        <a href="{{ route('customers.create') }}" class="text-muted d-inline-flex align-items-center gap-1"
                            style="font-size:.82rem;">
                            <i class="bi bi-plus-circle"></i> Create a new customer
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="qf-section">
            <div class="qf-section-header">
                <div class="qf-section-title">
                    <div class="qf-section-icon"><i class="bi bi-calendar2-week"></i></div>
                    Invoice Details
                </div>
                <span class="qf-section-badge">Step 2</span>
            </div>
            <div class="qf-section-body">
                <div class="qf-details-grid">
                    <div>
                        <label class="qf-label">Invoice Number</label>
                        <div class="qf-input-wrap">
                            <i class="bi bi-hash qf-input-icon"></i>
                            <input type="text" class="form-control" value="Auto-generated on save" readonly>
                        </div>
                    </div>
                    <div>
                        <label class="qf-label">Invoice Date <span class="req">*</span></label>
                        <div class="qf-input-wrap">
                            <i class="bi bi-calendar-event qf-input-icon"></i>
                            <input type="date" name="date" class="form-control" value="{{ old('date', date('Y-m-d')) }}"
                                required>
                        </div>
                    </div>
                    <div>
                        <label class="qf-label">Due Date</label>
                        <div class="qf-input-wrap">
                            <i class="bi bi-calendar-check qf-input-icon"></i>
                            <input type="date" name="due_date" class="form-control"
                                value="{{ old('due_date', date('Y-m-d', strtotime('+30 days'))) }}">
                        </div>
                    </div>
                    <div>
                        <label class="qf-label">Status</label>
                        <div class="qf-input-wrap">
                            <i class="bi bi-circle-fill qf-input-icon"></i>
                            <input type="text" class="form-control" value="Draft" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="qf-section">
            <div class="qf-section-header">
                <div class="qf-section-title">
                    <div class="qf-section-icon"><i class="bi bi-list-ul"></i></div>
                    Line Items
                </div>
                <span class="qf-section-badge">Step 3</span>
            </div>

            <div class="qf-table-wrap">
                <table class="qf-table" id="items-table">
                    <thead>
                        <tr>
                            <th style="width:44px; text-align:center;">#</th>
                            <th style="min-width:220px;">Service / Item</th>
                            <th style="width:150px;">Date (Optional)</th>
                            <th style="min-width:180px;">Description</th>
                            <th style="width:100px; text-align:center;">Qty</th>
                            <th style="width:110px;">Unit</th>
                            <th style="width:140px;">Unit Price (AED)</th>
                            <th style="width:140px; text-align:right;">Total (AED)</th>
                            <th style="width:44px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-container">
                        <tr class="item-row" data-index="0">
                            <td>
                                <div class="qf-row-num">1</div>
                            </td>
                            <td><input type="text" name="items[0][item_name]" class="form-control item-name-input"
                                    placeholder="Service name" required></td>
                            <td><input type="date" name="items[0][item_date]" class="form-control"></td>
                            <td><input type="text" name="items[0][description]" class="form-control" placeholder="Optional">
                            </td>
                            <td><input type="number" name="items[0][quantity]" class="form-control qty-input text-center"
                                    value="1" step="0.01" min="0.01" required></td>
                            <td>
                                <select name="items[0][unit]" class="form-select">
                                    @foreach(['Unit', 'Piece', 'Meter', 'Sqm', 'Kg', 'Set', 'Lot', 'Hour', 'Day', 'Job'] as $u)
                                        <option value="{{ $u }}">{{ $u }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td><input type="number" name="items[0][unit_price]" class="form-control price-input"
                                    value="0.00" step="0.01" min="0" required></td>
                            <td>
                                <div class="qf-row-total" id="row-total-0">0.00</div>
                            </td>
                            <td>
                                <button type="button" class="qf-remove-btn" onclick="removeRow(this)" title="Remove">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" class="qf-add-row-btn" onclick="addRow()">
                <i class="bi bi-plus-lg"></i> Add Line Item
            </button>

            <div class="qf-totals-panel">
                <div class="qf-totals-inner">
                    <div class="qf-totals-row">
                        <span class="qf-totals-label">Subtotal</span>
                        <span class="qf-totals-value" id="display-subtotal">AED 0.00</span>
                    </div>
                    <div class="qf-totals-input-row">
                        <label for="discount">Discount (AED)</label>
                        <input type="number" name="discount" id="discount" class="form-control qf-totals-input"
                            value="{{ old('discount', 0) }}" step="0.01" min="0">
                    </div>
                    <div class="qf-totals-row">
                        <span class="qf-totals-label">After Discount</span>
                        <span class="qf-totals-value" id="display-after-discount">AED 0.00</span>
                    </div>
                    <div class="qf-totals-input-row">
                        <label for="tax">Tax Rate (%)</label>
                        <input type="number" name="tax" id="tax" class="form-control qf-totals-input"
                            value="{{ old('tax', 0) }}" step="0.1" min="0" max="100">
                    </div>
                    <div class="qf-totals-row">
                        <span class="qf-totals-label">Tax Amount</span>
                        <span class="qf-totals-value" id="display-tax-val">AED 0.00</span>
                    </div>
                    <div class="qf-grand-total-row">
                        <span class="qf-grand-total-label">Grand Total</span>
                        <span class="qf-grand-total-value">AED <span id="display-grand-total">0.00</span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="qf-section">
            <div class="qf-section-header">
                <div class="qf-section-title">
                    <div class="qf-section-icon"><i class="bi bi-chat-left-text"></i></div>
                    Notes &amp; Terms
                </div>
                <span class="qf-section-badge">Step 4</span>
            </div>
            <div class="qf-section-body">
                <div class="row g-4">
                    <div class="col-12 col-md-5">
                        <label class="qf-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="5"
                            style="font-size:.875rem; resize:vertical;">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12 col-md-7">
                        <label class="qf-label">Terms &amp; Conditions</label>
                        <textarea name="terms" class="form-control" rows="5"
                            style="font-size:.875rem; resize:vertical;">{{ old('terms') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="qf-actions">
                <button type="submit" class="btn-qf-save">
                    <i class="bi bi-check2-circle"></i> Save Invoice
                </button>
                <a href="{{ route('invoices.index') }}" class="btn-qf-cancel">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
            </div>
        </div>

    </form>
@endsection

@push('scripts')
    <script>
        var rowIndex = 1;
        var UNITS = ['Unit', 'Piece', 'Meter', 'Sqm', 'Kg', 'Set', 'Lot', 'Hour', 'Day', 'Job'];

        function buildUnitOptions(selected) {
            selected = selected || 'Unit';
            return UNITS.map(function (unit) {
                return '<option value="' + unit + '"' + (unit === selected ? ' selected' : '') + '>' + unit + '</option>';
            }).join('');
        }

        function addRow(name, desc, price, unit, itemDate) {
            name = name || '';
            desc = desc || '';
            price = price || '0.00';
            unit = unit || 'Unit';
            itemDate = itemDate || '';

            var idx = rowIndex;
            var tr = document.createElement('tr');
            tr.className = 'item-row';
            tr.dataset.index = idx;
            tr.innerHTML =
                '<td><div class="qf-row-num">' + (idx + 1) + '</div></td>' +
                '<td><input type="text" name="items[' + idx + '][item_name]" class="form-control item-name-input" value="' + escHtml(name) + '" placeholder="Service name" required></td>' +
                '<td><input type="date" name="items[' + idx + '][item_date]" class="form-control" value="' + escHtml(itemDate) + '"></td>' +
                '<td><input type="text" name="items[' + idx + '][description]" class="form-control" value="' + escHtml(desc) + '" placeholder="Optional"></td>' +
                '<td><input type="number" name="items[' + idx + '][quantity]" class="form-control qty-input text-center" value="1" step="0.01" min="0.01" required></td>' +
                '<td><select name="items[' + idx + '][unit]" class="form-select">' + buildUnitOptions(unit) + '</select></td>' +
                '<td><input type="number" name="items[' + idx + '][unit_price]" class="form-control price-input" value="' + escHtml(price) + '" step="0.01" min="0" required></td>' +
                '<td><div class="qf-row-total" id="row-total-' + idx + '">0.00</div></td>' +
                '<td><button type="button" class="qf-remove-btn" onclick="removeRow(this)" title="Remove"><i class="bi bi-trash3"></i></button></td>';

            document.getElementById('items-container').appendChild(tr);
            bindRowEvents(tr);
            rowIndex++;
            updateRowNumbers();
            calcTotals();
            tr.querySelector('.item-name-input').focus();
        }

        function removeRow(btn) {
            var rows = document.querySelectorAll('.item-row');
            if (rows.length <= 1) {
                var row = btn.closest('tr');
                row.style.transition = 'background .1s';
                row.style.background = 'var(--danger-light)';
                setTimeout(function () { row.style.background = ''; }, 600);
                return;
            }
            btn.closest('tr').remove();
            updateRowNumbers();
            calcTotals();
        }

        function updateRowNumbers() {
            document.querySelectorAll('.item-row').forEach(function (row, index) {
                var num = row.querySelector('.qf-row-num');
                if (num) num.textContent = index + 1;
            });
        }

        function bindRowEvents(row) {
            row.querySelector('.qty-input').addEventListener('input', calcTotals);
            row.querySelector('.price-input').addEventListener('input', calcTotals);
        }

        function calcTotals() {
            var subtotal = 0;
            document.querySelectorAll('.item-row').forEach(function (row) {
                var qty = parseFloat(row.querySelector('.qty-input') ? row.querySelector('.qty-input').value : 0) || 0;
                var price = parseFloat(row.querySelector('.price-input') ? row.querySelector('.price-input').value : 0) || 0;
                var total = qty * price;
                var cell = row.querySelector('.qf-row-total');
                if (cell) cell.textContent = fmtNum(total);
                subtotal += total;
            });

            var discount = parseFloat(document.getElementById('discount') ? document.getElementById('discount').value : 0) || 0;
            var taxPct = parseFloat(document.getElementById('tax') ? document.getElementById('tax').value : 0) || 0;
            var afterDiscount = subtotal - discount;
            var taxAmount = afterDiscount * taxPct / 100;
            var grandTotal = afterDiscount + taxAmount;

            document.getElementById('display-subtotal').textContent = 'AED ' + fmtNum(subtotal);
            document.getElementById('display-after-discount').textContent = 'AED ' + fmtNum(afterDiscount);
            document.getElementById('display-tax-val').textContent = 'AED ' + fmtNum(taxAmount);
            document.getElementById('display-grand-total').textContent = fmtNum(grandTotal);
        }

        function fmtNum(n) {
            return n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function escHtml(str) {
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        document.querySelectorAll('.item-row').forEach(function (row) { bindRowEvents(row); });
        var discEl = document.getElementById('discount');
        var taxEl = document.getElementById('tax');
        if (discEl) discEl.addEventListener('input', calcTotals);
        if (taxEl) taxEl.addEventListener('input', calcTotals);
        calcTotals();
    </script>
@endpush