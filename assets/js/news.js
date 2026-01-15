/**
 * News Page JavaScript
 * Xử lý banner height và các tính năng trang tin tức
 * Requires: common.js
 */

$(document).ready(function () {
  // Set news banner height to match viewport minus header
  function setNewsBannerHeight() {
    const header = $(".main-header");
    const headerHeight = header.length > 0 ? header.outerHeight() : 80;
    const viewportHeight = window.innerHeight;
    const bannerHeight = viewportHeight - headerHeight;

    $("#news-banner-section").css("height", bannerHeight + "px");
  }

  // Set height on load
  setNewsBannerHeight();

  // Update height on window resize using common helper
  $(window).on("resize", handleResize(setNewsBannerHeight, 250));
});
