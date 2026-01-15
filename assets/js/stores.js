/**
 * Stores Page JavaScript
 * Xử lý tìm kiếm cửa hàng
 */

$(document).ready(function () {
  // Set stores hero banner height to match viewport minus header
  function setStoresHeroHeight() {
    const header = $(".main-header");
    const headerHeight = header.length > 0 ? header.outerHeight() : 80;
    const viewportHeight = window.innerHeight;
    const heroHeight = viewportHeight - headerHeight;

    $(".stores-hero").css("height", heroHeight + "px");
  }

  // Set height on load
  setStoresHeroHeight();

  // Update height on window resize
  let resizeTimeout;
  $(window).on("resize", function () {
    if (resizeTimeout) {
      clearTimeout(resizeTimeout);
    }
    resizeTimeout = setTimeout(function () {
      setStoresHeroHeight();
    }, 250);
  });

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
    searchTimeout = setTimeout(function () {
      const keyword = $("#search-keyword").val().trim();
      const province = $("#search-province").val();
      const ward = $("#search-ward").val();

      // Build URL with parameters
      const params = new URLSearchParams();
      if (keyword) params.append("keyword", keyword);
      if (province) params.append("province", province);
      if (ward) params.append("ward", ward);

      // Reload page with search parameters
      const newUrl =
        window.location.pathname +
        (params.toString() ? "?" + params.toString() : "");
      window.location.href = newUrl;
    }, 500);
  }

  // Search on input change
  $("#search-keyword").on("input", performSearch);
  $("#search-province, #search-ward").on("change", performSearch);

  // Prevent form submission on Enter
  $("#search-keyword").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      performSearch();
    }
  });
});
