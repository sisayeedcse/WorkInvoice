@extends('layouts.app')
@section('title', 'New Production Order')
@section('page-title', 'New Production Order')

@section('content')
    <div class="page-header">
        <div>
            <h1>Create Production Order</h1>
            <p class="text-muted mb-0" style="font-size:13px;">Define finished product, quantity, and raw materials to
                consume.</p>
        </div>
    </div>

    <div class="form-main">
        <div class="card">
            <div class="card-header"><i class="bi bi-gear-wide-connected me-2" style="color:var(--accent);"></i>Production
                Details</div>
            <div class="card-body">
                <form method="POST" action="{{ route('production-orders.store') }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Finished Product <span class="text-danger">*</span></label>
                            <select name="finished_item_id" class="form-select" required>
                                <option value="">Select product</option>
                                @foreach($finishedProducts as $product)
                                    <option value="{{ $product->id }}" {{ old('finished_item_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Quantity to Produce <span class="text-danger">*</span></label>
                            <input type="number" min="0.001" step="0.001" name="quantity_to_produce" class="form-control"
                                value="{{ old('quantity_to_produce') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" name="production_date" class="form-control"
                                value="{{ old('production_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" id="addMaterialBtn" class="btn btn-outline-primary w-100"><i
                                    class="bi bi-plus-lg me-1"></i>Add Material</button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-bordered align-middle" id="materialsTable">
                            <thead>
                                <tr>
                                    <th style="width:45%;">Raw Material</th>
                                    <th style="width:25%;">Available</th>
                                    <th style="width:20%;">Quantity Used</th>
                                    <th style="width:10%;"></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-accent px-4"><i class="bi bi-check-lg me-1"></i>Create
                            Order</button>
                        <a href="{{ route('production-orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <template id="materialRowTemplate">
        <tr>
            <td>
                <select class="form-select material-select" name="materials[__INDEX__][item_id]" required>
                    <option value="">Select material</option>
                    @foreach($materials as $material)
                        <option value="{{ $material->id }}" data-stock="{{ (float) $material->stock_quantity }}"
                            data-unit="{{ $material->unit }}">
                            {{ $material->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="available-stock text-muted">—</td>
            <td><input type="number" min="0.001" step="0.001" class="form-control"
                    name="materials[__INDEX__][quantity_required]" required></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-link text-danger remove-row">Remove</button>
            </td>
        </tr>
    </template>
@endsection

@push('scripts')
    <script>
        let materialIndex = 0;
        const tableBody = document.querySelector('#materialsTable tbody');
        const template = document.getElementById('materialRowTemplate').innerHTML;

        function addMaterialRow() {
            const html = template.replaceAll('__INDEX__', materialIndex++);
            tableBody.insertAdjacentHTML('beforeend', html);
        }

        document.getElementById('addMaterialBtn').addEventListener('click', addMaterialRow);

        tableBody.addEventListener('change', function (e) {
            if (!e.target.classList.contains('material-select')) return;
            const option = e.target.options[e.target.selectedIndex];
            const stock = parseFloat(option.dataset.stock || 0);
            const unit = option.dataset.unit || '';
            e.target.closest('tr').querySelector('.available-stock').textContent = `${stock.toFixed(3)} ${unit}`;
        });

        tableBody.addEventListener('click', function (e) {
            if (!e.target.classList.contains('remove-row')) return;
            e.target.closest('tr').remove();
        });

        addMaterialRow();
    </script>
@endpush