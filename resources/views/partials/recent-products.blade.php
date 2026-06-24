<!-- Recent Products Sidebar (Retargeting) -->
<script>
// Track recently viewed products
function trackProductView(productId, productName, productImage, productPrice) {
    let recent = JSON.parse(localStorage.getItem('recent-products') || '[]');
    
    // Remove if already exists
    recent = recent.filter(p => p.id !== productId);
    
    // Add to front
    recent.unshift({ id: productId, name: productName, image: productImage, price: productPrice });
    
    // Keep only 5
    recent = recent.slice(0, 5);
    
    localStorage.setItem('recent-products', JSON.stringify(recent));
    updateRecentSidebar();
}

function updateRecentSidebar() {
    const recent = JSON.parse(localStorage.getItem('recent-products') || '[]');
    const sidebar = document.getElementById('recent-products-sidebar');
    
    if (recent.length === 0) {
        sidebar?.classList.add('opacity-0', 'pointer-events-none', '-translate-x-full');
        return;
    }
    
    let html = `
        <div class="space-y-3">
            <h4 class="font-semibold text-sm text-[var(--brand-ink)]">Vus récemment</h4>
    `;
    
    recent.forEach(product => {
        html += `
            <a href="/catalog/products/${product.id}" class="group block">
                <div class="bg-white rounded-lg overflow-hidden shadow hover:shadow-lg transition-shadow">
                    <div class="aspect-square bg-gray-100 overflow-hidden">
                        <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <div class="p-2">
                        <p class="text-xs font-medium text-[var(--brand-ink)] truncate">${product.name}</p>
                        <p class="text-xs text-[var(--brand-copy)] font-semibold">${product.price}</p>
                    </div>
                </div>
            </a>
        `;
    });
    
    html += `</div>`;
    
    if (sidebar) {
        sidebar.innerHTML = html;
        sidebar.classList.remove('opacity-0', 'pointer-events-none', '-translate-x-full');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', updateRecentSidebar);
</script>

<!-- Sticky Sidebar Container -->
<div id="recent-products-sidebar" class="fixed left-0 top-1/2 -translate-y-1/2 bg-white rounded-r-lg shadow-lg p-4 w-48 max-h-96 overflow-y-auto transition-all duration-300 opacity-0 pointer-events-none -translate-x-full z-30">
    <!-- Content will be inserted by JavaScript -->
</div>

<!-- Alternative: Bottom Sticky Bar (for mobile) -->
<div class="hidden sm:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-[var(--brand-line)] shadow-lg z-30 max-h-24 overflow-x-auto">
    <div class="flex gap-3 p-4 overflow-x-auto snap-x snap-mandatory">
        <script>
        (function() {
            const recent = JSON.parse(localStorage.getItem('recent-products') || '[]');
            if (recent.length > 0) {
                let html = '<p class="text-xs font-semibold text-[var(--brand-ink)] whitespace-nowrap py-1">Vues récemment:</p>';
                recent.forEach(p => {
                    html += `<a href="/catalog/products/${p.id}" class="snap-center flex-shrink-0 w-20 text-center">
                        <img src="${p.image}" alt="${p.name}" class="w-20 h-20 object-cover rounded">
                        <p class="text-xs mt-1 truncate">${p.name}</p>
                    </a>`;
                });
                document.currentScript.parentElement.innerHTML = html;
            }
        })();
        </script>
    </div>
</div>
