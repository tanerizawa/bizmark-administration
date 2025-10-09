/**
 * View Toggle Functionality
 * Supports Grid and List view with localStorage persistence
 */

// View Toggle Function
function toggleView(view, storageKey = 'viewMode') {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (!gridView || !listView || !gridBtn || !listBtn) {
        console.warn('View toggle elements not found');
        return;
    }
    
    if (view === 'grid') {
        gridView.style.display = 'grid';
        listView.style.display = 'none';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
        localStorage.setItem(storageKey, 'grid');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
        localStorage.setItem(storageKey, 'list');
    }
}

// Initialize view on page load
function initializeView(storageKey = 'viewMode') {
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem(storageKey) || 'grid';
        toggleView(savedView, storageKey);
    });
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { toggleView, initializeView };
}
