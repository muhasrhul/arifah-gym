<x-filament::page>
    <style>
        .grid-kasir {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .btn-kasir {
            width: 100%;
            /* Sudut dibuat lebih premium (tidak terlalu bulat) */
            border-radius: 12px; 
            padding: 50px 30px;
            text-align: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        /* Efek Hover Premium */
        .btn-kasir:hover {
            transform: translateY(-8px);
            filter: brightness(1.1);
        }
        .btn-kasir:active {
            transform: translateY(-2px);
        }
        /* Warna Orange ARIFAH Gym Premium */
        .btn-orange {
            background: linear-gradient(135deg, #ff8c00 0%, #ff4500 100%);
            color: #000000;
            box-shadow: 0 10px 30px rgba(255, 69, 0, 0.3);
            border-bottom: 6px solid #b33000;
        }
        /* Warna Dark Premium (Glassmorphism style) */
        .btn-dark {
            background: linear-gradient(135deg, #2d2d30 0%, #1a1a1c 100%);
            color: #ffffff;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-bottom: 6px solid #000000;
        }
        .nama-produk {
            font-size: 1rem;
            font-weight: 800;
            text-transform: uppercase;
            display: block;
            margin-bottom: 5px;
            letter-spacing: 2px;
            opacity: 0.8;
        }
        .harga-produk {
            font-size: 5.5rem;
            font-weight: 900;
            display: block;
            line-height: 1;
            font-style: italic;
            letter-spacing: -2px;
        }
        .label-bawah {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 20px;
            display: block;
            background: rgba(255,255,255,0.1);
            padding: 4px 10px;
            border-radius: 4px;
        }
        .btn-orange .label-bawah {
            background: rgba(0,0,0,0.1);
        }
        /* Stock Badge */
        .stock-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stock-habis {
            background: #ef4444;
            color: white;
        }
        .stock-menipis {
            background: #f59e0b;
            color: white;
        }
        .stock-aman {
            background: #10b981;
            color: white;
        }
        .btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            filter: grayscale(50%);
        }
        .btn-disabled:hover {
            transform: none;
        }

        /* Modal Styles - Modern Design */
        .modal-overlay {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            background: rgba(0, 0, 0, 0.75) !important;
            backdrop-filter: blur(12px) !important;
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 99999 !important;
            opacity: 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box !important;
        }
        .modal-content {
            background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.9));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 0;
            max-width: min(380px, 90vw);
            width: 90%;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            transform: scale(0.8) translateY(40px);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }
        .modal-overlay.show {
            display: flex !important;
            opacity: 1 !important;
        }
        .modal-overlay.show .modal-content {
            transform: scale(1) translateY(0);
        }
        
        /* Force modal to cover everything */
        body.modal-open {
            overflow: hidden !important;
        }
        .modal-overlay.show {
            position: fixed !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            min-height: 100vh !important;
            min-width: 100vw !important;
        }
        .modal-header {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: white;
            padding: 16px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .modal-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }
        .modal-title {
            font-size: 1.2rem;
            font-weight: 900;
            margin-bottom: 4px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }
        .modal-subtitle {
            font-size: 0.7rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        .modal-body {
            padding: 16px 20px;
        }
        .quantity-section {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid rgba(148, 163, 184, 0.1);
        }
        .qty-btn {
            width: 40px;
            height: 40px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            cursor: pointer;
            font-size: 1.2rem;
            font-weight: 900;
            color: #059669;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.1);
        }
        .qty-btn:hover {
            border-color: #059669;
            background: linear-gradient(145deg, #f0fdf4, #dcfce7);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px -2px rgba(5, 150, 105, 0.2);
        }
        .qty-btn:active {
            transform: translateY(0);
        }
        #quantityInput {
            width: 80px;
            height: 40px;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 900;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            color: #059669;
            transition: all 0.3s ease;
        }
        #quantityInput:focus {
            outline: none;
            border-color: #059669;
            box-shadow: 0 0 0 2px rgba(5, 150, 105, 0.1);
        }
        .total-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-radius: 14px;
            padding: 16px;
            margin-bottom: 16px;
            border: 1px solid rgba(5, 150, 105, 0.1);
        }
        .total-label {
            font-size: 0.7rem;
            color: #059669;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .total-amount {
            font-size: 1.8rem;
            font-weight: 900;
            color: #047857;
            font-style: italic;
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        #unitPrice {
            font-size: 1rem;
            font-weight: 700;
            color: #6b7280;
        }
        .payment-methods {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
        }
        .payment-btn {
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            background: linear-gradient(145deg, #ffffff, #f8fafc);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .payment-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s;
        }
        .payment-btn:hover::before {
            left: 100%;
        }
        .payment-btn:hover {
            border-color: #059669;
            background: linear-gradient(145deg, #f0fdf4, #dcfce7);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px -4px rgba(5, 150, 105, 0.2);
        }
        .payment-btn.selected {
            border-color: #059669;
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -3px rgba(5, 150, 105, 0.4);
        }
        .payment-btn.selected:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px -5px rgba(5, 150, 105, 0.5);
        }
        .payment-icon {
            font-size: 1.4rem;
            margin-bottom: 6px;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
        }
        .payment-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .modal-footer {
            display: flex;
            gap: 12px;
            padding: 0 20px 20px 20px;
        }
        .btn-modal {
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            position: relative;
            overflow: hidden;
        }
        .btn-modal::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s, height 0.3s;
        }
        .btn-modal:active::before {
            width: 300px;
            height: 300px;
        }
        .btn-cancel {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: #374151;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn-cancel:hover {
            background: linear-gradient(135deg, #e5e7eb, #d1d5db);
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.15);
        }
        .btn-confirm {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(5, 150, 105, 0.3);
        }
        .btn-confirm:hover {
            background: linear-gradient(135deg, #047857, #065f46);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(5, 150, 105, 0.4);
        }
    </style>

    <div class="grid-kasir">
        @foreach($products as $product)
            <button 
                onclick="showPaymentModal({{ $product->id }}, '{{ $product->name }}', {{ $product->price }}, {{ $product->stock }})"
                class="btn-kasir {{ $product->color === 'orange' ? 'btn-orange' : 'btn-dark' }} {{ $product->stock <= 0 ? 'btn-disabled' : '' }}"
                {{ $product->stock <= 0 ? 'disabled' : '' }}
            >
                <!-- Stock Badge -->
                @if($product->stock <= 0)
                    <span class="stock-badge stock-habis">HABIS</span>
                @elseif($product->stock <= 5)
                    <span class="stock-badge stock-menipis">{{ $product->stock }} pcs</span>
                @else
                    <span class="stock-badge stock-aman">{{ $product->stock }} pcs</span>
                @endif

                <span class="nama-produk">
                    {{ $product->name }}
                </span>
                
                <span class="harga-produk">
                    {{ number_format($product->price / 1000, 0) }}K
                </span>
                
                <span class="label-bawah">
                    {{ $product->stock <= 0 ? 'Stock Habis' : 'Klik untuk Bayar' }}
                </span>
            </button>
        @endforeach
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalProductName">Konfirmasi Pembayaran</h3>
                <p class="modal-subtitle">Pilih metode pembayaran</p>
            </div>
            
            <div class="modal-body">
                <div class="quantity-section" style="margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: center; gap: 15px;">
                        <button type="button" onclick="changeQuantity(-1)" class="qty-btn" style="width: 40px; height: 40px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; font-size: 1.2rem; font-weight: bold;">-</button>
                        <div style="text-align: center;">
                            <div style="font-size: 0.8rem; color: #6b7280; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 5px;">Jumlah</div>
                            <input type="number" id="quantityInput" value="1" min="1" max="99" onchange="updateTotal()" style="width: 80px; height: 40px; text-align: center; font-size: 1.5rem; font-weight: bold; border: 2px solid #e5e7eb; border-radius: 8px; background: #f8fafc;">
                        </div>
                        <button type="button" onclick="changeQuantity(1)" class="qty-btn" style="width: 40px; height: 40px; border: 2px solid #e5e7eb; border-radius: 8px; background: white; cursor: pointer; font-size: 1.2rem; font-weight: bold;">+</button>
                    </div>
                </div>
                
                <div class="total-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <div style="text-align: left;">
                            <div class="total-label">Harga Satuan</div>
                            <div style="font-size: 1.2rem; font-weight: 600; color: #374151;" id="unitPrice">Rp 0</div>
                        </div>
                        <div style="text-align: right;">
                            <div class="total-label">Total Pembayaran</div>
                            <div class="total-amount" id="modalTotalAmount">Rp 0</div>
                        </div>
                    </div>
                </div>
                
                <div class="payment-methods">
                    <div class="payment-btn selected" data-method="cash">
                        <div class="payment-icon">💵</div>
                        <div class="payment-label">Cash</div>
                    </div>
                    <div class="payment-btn" data-method="transfer">
                        <div class="payment-icon">🏦</div>
                        <div class="payment-label">Transfer</div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button class="btn-modal btn-cancel" onclick="closePaymentModal()">Batal</button>
                <button class="btn-modal btn-confirm" onclick="confirmPayment()">Bayar Sekarang</button>
            </div>
        </div>
    </div>

    <div style="margin-top: 60px; text-align: center; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 30px;">
        <div style="display: inline-block; padding: 10px 30px; border-radius: 8px; background: #f4f4f5; border: 1px solid #e4e4e7;">
            <p style="font-size: 0.75rem; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 2px; margin: 0;">
                ARIFAH Gym Intelligence POS System
            </p>
        </div>
    </div>

    <script>
        let selectedProduct = null;
        let selectedPaymentMethod = 'cash';

        function showPaymentModal(productId, productName, price, stock) {
            if (stock <= 0) return;
            
            selectedProduct = {
                id: productId,
                name: productName,
                price: price,
                stock: stock
            };
            
            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('unitPrice').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('quantityInput').value = 1;
            document.getElementById('quantityInput').max = stock; // Set max berdasarkan stock
            updateTotal();
            
            // Add class to body and show modal
            document.body.classList.add('modal-open');
            document.getElementById('paymentModal').classList.add('show');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.remove('show');
            document.body.classList.remove('modal-open');
            selectedProduct = null;
        }

        function changeQuantity(change) {
            const input = document.getElementById('quantityInput');
            let newValue = parseInt(input.value) + change;
            
            if (newValue < 1) newValue = 1;
            if (newValue > selectedProduct.stock) newValue = selectedProduct.stock;
            
            input.value = newValue;
            updateTotal();
        }

        function updateTotal() {
            if (!selectedProduct) return;
            
            const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
            const total = selectedProduct.price * quantity;
            
            document.getElementById('modalTotalAmount').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }

        function confirmPayment() {
            if (!selectedProduct) return;
            
            const quantity = parseInt(document.getElementById('quantityInput').value) || 1;
            
            // Call Livewire method with payment method and quantity
            @this.call('bayarHarian', selectedProduct.id, selectedPaymentMethod, quantity);
            closePaymentModal();
        }

        // Payment method selection
        document.querySelectorAll('.payment-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('selected'));
                this.classList.add('selected');
                selectedPaymentMethod = this.dataset.method;
            });
        });

        // Close modal when clicking overlay
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });

        // Handle quantity input change
        document.getElementById('quantityInput').addEventListener('input', function() {
            let value = parseInt(this.value);
            if (isNaN(value) || value < 1) {
                this.value = 1;
            } else if (selectedProduct && value > selectedProduct.stock) {
                this.value = selectedProduct.stock;
            }
            updateTotal();
        });
    </script>
</x-filament::page>