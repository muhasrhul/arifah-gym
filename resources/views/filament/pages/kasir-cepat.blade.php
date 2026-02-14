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
    </style>

    <div class="grid-kasir">
        @foreach($products as $product)
            <button 
                wire:click="bayarHarian({{ $product->id }})" 
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
                    {{ $product->stock <= 0 ? 'Stock Habis' : 'Proses Transaksi' }}
                </span>
            </button>
        @endforeach
    </div>

    <div style="margin-top: 60px; text-align: center; border-top: 1px solid rgba(0,0,0,0.05); padding-top: 30px;">
        <div style="display: inline-block; padding: 10px 30px; border-radius: 8px; background: #f4f4f5; border: 1px solid #e4e4e7;">
            <p style="font-size: 0.75rem; font-weight: 800; color: #71717a; text-transform: uppercase; letter-spacing: 2px; margin: 0;">
                ARIFAH Gym Intelligence POS System
            </p>
        </div>
    </div>
</x-filament::page>