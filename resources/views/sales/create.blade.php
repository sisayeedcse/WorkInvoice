@extends('layouts.app')
@section('title', 'POS')
@section('page-title', 'POS')

@section('content')
    <div class="hero-panel mb-4">
        <div>
            <div class="hero-kicker"><i class="bi bi-shop"></i> Point of Sale</div>
            <h1 class="hero-title">POS Checkout</h1>
            <p class="hero-copy">Fast, touch-friendly sales checkout with live stock validation.</p>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-search me-2" style="color:var(--accent);"></i>Product List</span>
                    <input id="productSearch" class="form-control form-control-sm" style="max-width:220px;"
                        placeholder="Search product...">
                </div>
                <div class="card-body">
                    <div class="row g-2" id="productGrid">
                        @foreach($products as $product)
                            <div class="col-12 col-md-6">
                                <button type="button" class="btn btn-light text-start w-100 p-3 add-product-btn"
                                    data-id="{{ $product->id }}" data-name="{{ $product->name }}"
                                    data-price="{{ (float) $product->default_price }}"
                                    data-stock="{{ (float) $product->stock_quantity }}" data-unit="{{ $product->unit }}">
                                    <div class="fw-semibold">{{ $product->name }}</div>
                                    <div class="small text-muted">AED {{ number_format($product->default_price, 2) }} • Stock:
                                        {{ number_format($product->stock_quantity, 3) }} {{ $product->unit }}</div>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-cart3 me-2" style="color:var(--accent);"></i>Cart</div>
                <div class="card-body">
                    <div id="cartItems" class="mb-3"></div>
                    <div class="d-flex justify-content-between mb-2"><span>Total Items</span><strong id="cartQty">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3"><span>Grand Total</span><strong id="cartTotal">AED
                            0.00</strong></div>

                    <div class="mb-2">
                        <label class="form-label">Customer (Optional)</label>
                        <input type="text" id="customerName" class="form-control" placeholder="Walk-in customer">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Payment Method</label>
                        <select id="paymentMethod" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="online">Online</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="printReceipt" checked>
                        <label class="form-check-label" for="printReceipt">Print Receipt</label>
                    </div>

                    <div class="d-grid gap-2">
                        <button id="checkoutBtn" class="btn btn-accent"><i
                                class="bi bi-check2-circle me-1"></i>Checkout</button>
                        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary">View Sales History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const cart = [];
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function renderCart() {
            const cartItemsEl = document.getElementById('cartItems');
            if (!cart.length) {
                cartItemsEl.innerHTML = '<div class="text-muted">No items added yet.</div>';
                document.getElementById('cartQty').textContent = '0';
                document.getElementById('cartTotal').textContent = 'AED 0.00';
                return;
            }

            let totalQty = 0;
            let totalAmount = 0;
            cartItemsEl.innerHTML = cart.map((item, index) => {
                const lineTotal = item.quantity * item.price;
                totalQty += item.quantity;
                totalAmount += lineTotal;
                return `
                    <div class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>${item.name}</strong>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="removeItem(${index})">Remove</button>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <input type="number" min="0.001" step="0.001" class="form-control form-control-sm" style="max-width:110px;" value="${item.quantity}" onchange="updateQty(${index}, this.value)">
                            <span class="small text-muted">x AED ${item.price.toFixed(2)}</span>
                            <span class="ms-auto fw-semibold">AED ${lineTotal.toFixed(2)}</span>
                        </div>
                        <div class="small text-muted mt-1">Available: ${item.stock.toFixed(3)} ${item.unit}</div>
                    </div>`;
            }).join('');

            document.getElementById('cartQty').textContent = totalQty.toFixed(3);
            document.getElementById('cartTotal').textContent = `AED ${totalAmount.toFixed(2)}`;
        }

        function addToCart(product) {
            const existing = cart.find(i => i.item_id === product.id);
            if (existing) {
                if ((existing.quantity + 1) > existing.stock) {
                    alert(`Insufficient stock for ${existing.name}`);
                    return;
                }
                existing.quantity += 1;
            } else {
                if (product.stock <= 0) {
                    alert(`No stock available for ${product.name}`);
                    return;
                }
                cart.push({
                    item_id: product.id,
                    name: product.name,
                    price: product.price,
                    stock: product.stock,
                    unit: product.unit,
                    quantity: 1
                });
            }
            renderCart();
        }

        function removeItem(index) {
            cart.splice(index, 1);
            renderCart();
        }

        function updateQty(index, value) {
            const qty = parseFloat(value || 0);
            if (qty <= 0) {
                removeItem(index);
                return;
            }
            if (qty > cart[index].stock) {
                alert(`Insufficient stock for ${cart[index].name}`);
                renderCart();
                return;
            }
            cart[index].quantity = qty;
            renderCart();
        }

        document.querySelectorAll('.add-product-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                addToCart({
                    id: parseInt(btn.dataset.id),
                    name: btn.dataset.name,
                    price: parseFloat(btn.dataset.price),
                    stock: parseFloat(btn.dataset.stock),
                    unit: btn.dataset.unit,
                });
            });
        });

        document.getElementById('productSearch').addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('.add-product-btn').forEach(btn => {
                const text = (btn.dataset.name || '').toLowerCase();
                btn.closest('.col-12').style.display = text.includes(term) ? '' : 'none';
            });
        });

        document.getElementById('checkoutBtn').addEventListener('click', async () => {
            if (!cart.length) {
                alert('Add at least one item.');
                return;
            }

            const payload = {
                customer_name: document.getElementById('customerName').value || null,
                payment_method: document.getElementById('paymentMethod').value,
                print_receipt: document.getElementById('printReceipt').checked,
                items: cart.map(i => ({ item_id: i.item_id, quantity: i.quantity }))
            };

            try {
                const response = await fetch('{{ route('pos.checkout') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();
                if (!response.ok) {
                    alert(data.message || 'Failed to complete sale.');
                    return;
                }

                if (data.print_receipt) {
                    window.open(data.receipt_url, '_blank');
                }
                window.location.href = data.sale_url;
            } catch (error) {
                alert('Failed to complete sale. Please try again.');
            }
        });

        renderCart();
    </script>
@endpush