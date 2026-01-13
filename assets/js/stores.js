/**
 * Stores Page JavaScript
 * Xử lý tìm kiếm cửa hàng
 */

$(document).ready(function() {
    // Get API path helper
    function getApiPath(endpoint) {
        const currentPath = window.location.pathname;
        let apiPath = `../../api/${endpoint}`;
        return apiPath;
    }

    // Search functionality
    let searchTimeout;
    function performSearch() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            const keyword = $('#search-keyword').val().trim();
            const province = $('#search-province').val();
            const ward = $('#search-ward').val();

            // Build URL with parameters
            const params = new URLSearchParams();
            if (keyword) params.append('keyword', keyword);
            if (province) params.append('province', province);
            if (ward) params.append('ward', ward);

            // Reload page with search parameters
            const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = newUrl;
        }, 500);
    }

    // Search on input change
    $('#search-keyword').on('input', performSearch);
    $('#search-province, #search-ward').on('change', performSearch);

    // Prevent form submission on Enter
    $('#search-keyword').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            performSearch();
        }
    });
});
