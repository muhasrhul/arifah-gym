@php
    $record = $getRecord();
    $hasSignature = !empty($record->digital_signature);
@endphp

@if($hasSignature)
    <!-- Preview Tanda Tangan sebagai Button -->
    <button type="button" 
            class="relative group cursor-pointer signature-preview-{{ $record->id }} bg-transparent border-0 p-0 m-0" 
            style="position: relative; z-index: 20; background: none; outline: none;">
        <img src="{{ $record->digital_signature }}" 
             alt="TTD {{ $record->name }}" 
             class="w-20 h-10 object-contain border border-gray-300 rounded bg-white hover:border-blue-500 transition-colors"
             style="pointer-events: none;">
        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 rounded transition-all flex items-center justify-center">
            <svg class="w-4 h-4 text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </button>

    <script>
        // Add CSS to prevent row click on signature column
        const style{{ $record->id }} = document.createElement('style');
        style{{ $record->id }}.textContent = `
            .signature-preview-{{ $record->id }} {
                position: relative !important;
                z-index: 999 !important;
                pointer-events: auto !important;
            }
            .signature-preview-{{ $record->id }} * {
                pointer-events: auto !important;
            }
            
            /* Modal positioning - fixed to viewport, not parent */
            #signatureModal{{ $record->id }} {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                z-index: 99999 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                background: rgba(0, 0, 0, 0.5) !important;
            }
            
            #signatureModal{{ $record->id }}.hidden {
                display: none !important;
            }
        `;
        document.head.appendChild(style{{ $record->id }});
        
        // Wait for DOM to be ready
        document.addEventListener('DOMContentLoaded', function() {
            // Create modal and append to body
            const modalHTML = `
                <div id="signatureModal{{ $record->id }}" class="hidden">
                    <div class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-auto mx-4" onclick="event.stopPropagation()">
                        <!-- Header -->
                        <div class="flex justify-between items-center p-6 border-b border-gray-200">
                            <h3 class="text-xl font-semibold text-gray-900">Tanda Tangan Digital</h3>
                            <button onclick="closeSignatureModal{{ $record->id }}()" class="text-gray-400 hover:text-gray-600 p-1">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <!-- Nama Member -->
                            <div class="text-center mb-4">
                                <h4 class="text-lg font-medium text-gray-900">{{ $record->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $record->email }} • {{ $record->phone }}</p>
                            </div>
                            
                            <!-- Tanda Tangan -->
                            <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg p-6 mb-6">
                                <img src="{{ $record->digital_signature }}" 
                                     alt="TTD {{ $record->name }}" 
                                     class="w-full h-auto max-h-64 object-contain mx-auto bg-white rounded border">
                            </div>
                            
                            <!-- Info Timestamp -->
                            <div class="grid grid-cols-1 gap-4 mb-6 text-sm">
                                @if($record->signature_timestamp)
                                <div class="bg-blue-50 p-3 rounded">
                                    <span class="font-medium text-blue-800">Ditandatangani:</span>
                                    <br>
                                    <span class="text-blue-600">{{ \Carbon\Carbon::parse($record->signature_timestamp)->setTimezone('Asia/Makassar')->format('d/m/Y H:i:s') }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="flex justify-end space-x-3 p-6 border-t border-gray-200 bg-gray-50">
                            <a href="{{ route('member.signature', $record) }}" target="_blank" 
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm font-medium">
                                Buka di Tab Baru
                            </a>
                            <button onclick="closeSignatureModal{{ $record->id }}()" 
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition-colors text-sm font-medium">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Append modal to body instead of current element
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            
            // Add click event listener to signature preview
            const signaturePreview{{ $record->id }} = document.querySelector('.signature-preview-{{ $record->id }}');
            if (signaturePreview{{ $record->id }}) {
                signaturePreview{{ $record->id }}.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    openSignatureModal{{ $record->id }}();
                    return false;
                }, true);
            }
        });
        
        function openSignatureModal{{ $record->id }}() {
            const modal = document.getElementById('signatureModal{{ $record->id }}');
            if (modal) {
                modal.classList.remove('hidden');
                
                // Add click outside to close
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeSignatureModal{{ $record->id }}();
                    }
                });
                
                // Add escape key to close
                const escapeHandler{{ $record->id }} = function(e) {
                    if (e.key === 'Escape') {
                        closeSignatureModal{{ $record->id }}();
                        document.removeEventListener('keydown', escapeHandler{{ $record->id }});
                    }
                };
                document.addEventListener('keydown', escapeHandler{{ $record->id }});
            }
        }
        
        function closeSignatureModal{{ $record->id }}() {
            const modal = document.getElementById('signatureModal{{ $record->id }}');
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
@else
    <!-- Placeholder untuk yang belum TTD -->
    <div class="w-20 h-10 bg-gray-100 border border-gray-300 rounded flex items-center justify-center">
        <span class="text-xs text-gray-500">Belum TTD</span>
    </div>
@endif