{{-- resources/views/partials/item-rows.blade.php --}}
{{-- Usage: @include('partials.item-rows', ['items' => $items, 'existingItems' => $quotation->items]) --}}
<div id="items-container">
    @if(isset($existingItems) && $existingItems->count())
        @foreach($existingItems as $i => $existingItem)
        <tr class="item-row" data-index="{{ $i }}">
            <td>
                <input type="text" name="items[{{ $i }}][item_name]"
                       class="form-control item-name-input"
                       value="{{ $existingItem->item_name }}"
                       placeholder="Service or item name" required
                       autocomplete="off">
            </td>
            <td>
                <input type="text" name="items[{{ $i }}][description]"
                       class="form-control"
                       value="{{ $existingItem->description }}"
                       placeholder="Optional description">
            </td>
            <td>
                <input type="number" name="items[{{ $i }}][quantity]"
                       class="form-control qty-input text-center"
                       value="{{ $existingItem->quantity }}"
                       step="0.01" min="0.01" required>
            </td>
            <td>
                <select name="items[{{ $i }}][unit]" class="form-select">
                    @foreach(['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'] as $u)
                    <option value="{{ $u }}" {{ $existingItem->unit == $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[{{ $i }}][unit_price]"
                       class="form-control price-input"
                       value="{{ $existingItem->unit_price }}"
                       step="0.01" min="0" required>
            </td>
            <td>
                <input type="text" class="form-control total-input bg-light text-end fw-semibold"
                       value="{{ number_format($existingItem->total, 2) }}" readonly>
            </td>
            <td class="text-center">
                <span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill"></i></span>
            </td>
        </tr>
        @endforeach
    @else
        <tr class="item-row" data-index="0">
            <td>
                <input type="text" name="items[0][item_name]"
                       class="form-control item-name-input"
                       placeholder="Service or item name" required
                       autocomplete="off">
            </td>
            <td>
                <input type="text" name="items[0][description]"
                       class="form-control" placeholder="Optional description">
            </td>
            <td>
                <input type="number" name="items[0][quantity]"
                       class="form-control qty-input text-center"
                       value="1" step="0.01" min="0.01" required>
            </td>
            <td>
                <select name="items[0][unit]" class="form-select">
                    @foreach(['Unit','Piece','Meter','Sqm','Kg','Set','Lot','Hour','Day','Job'] as $u)
                    <option value="{{ $u }}">{{ $u }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" name="items[0][unit_price]"
                       class="form-control price-input"
                       value="0.00" step="0.01" min="0" required>
            </td>
            <td>
                <input type="text" class="form-control total-input bg-light text-end fw-semibold"
                       value="0.00" readonly>
            </td>
            <td class="text-center">
                <span class="remove-row" onclick="removeRow(this)"><i class="bi bi-x-circle-fill"></i></span>
            </td>
        </tr>
    @endif
</div>
