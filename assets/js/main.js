/**
 * Main JavaScript for MeowTea Fresh
 * jQuery và AJAX functions
 */

$(document).ready(function () {
  // Back to top button
  $(".back-to-top-link").on("click", function (e) {
    e.preventDefault();
    $("html, body").animate(
      {
        scrollTop: 0,
      },
      600
    );
  });

  // Show/hide back to top button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      $(".back-to-top").fadeIn();
    } else {
      $(".back-to-top").fadeOut();
    }
  });

  // Helper function to get product detail page URL
  function getProductDetailUrl(productId) {
    const currentPath = window.location.pathname;
    let productUrl = "pages/menu/product.php";

    // Nếu đang ở trong pages/menu/, chỉ cần product.php (cùng thư mục)
    if (currentPath.includes("/pages/menu/")) {
      productUrl = "product.php";
    } else if (currentPath.includes("/pages/")) {
      // Nếu đang ở trong pages/ nhưng không phải menu/, cần ../menu/product.php
      productUrl = "../menu/product.php";
    }
    // Nếu ở root, giữ nguyên pages/menu/product.php

    return `${productUrl}?id=${productId}`;
  }

  // Add to cart functionality - Open modal
  $(document).on("click", ".add-to-cart-btn", function (e) {
    e.preventDefault();
    e.stopPropagation();

    const productId = $(this).data("product-id");
    openProductCustomizeModal(productId);
  });

  // Product card click - Disabled (no action)
  // Removed click handler for product-card

  // Update cart count
  function updateCartCount() {
    // Tính đường dẫn API dựa trên vị trí hiện tại
    const currentPath = window.location.pathname;
    let apiPath = "api/cart/count.php";

    // Nếu đang ở trong thư mục con (pages/...), cần thêm ../../
    // Ví dụ: /projects_web_php/pages/menu/index.php -> cần ../../api/...
    if (currentPath.includes("/pages/")) {
      // Đếm số level từ pages/ về root
      const pathParts = currentPath.split("/").filter((p) => p);
      const pagesIndex = pathParts.indexOf("pages");
      if (pagesIndex >= 0) {
        // Số level = số phần tử sau 'pages' + 1 (cho pages)
        const levels = pathParts.length - pagesIndex - 1;
        apiPath = "../".repeat(levels) + apiPath;
      }
    }

    $.ajax({
      url: apiPath,
      method: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          $(".cart-count").text(response.count || 0);
        }
      },
      error: function () {
        // Silent fail
      },
    });
  }

  // Load cart count on page load
  updateCartCount();

  // User dropdown menu toggle
  $(".user-dropdown-toggle").on("click", function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $dropdown = $(this).closest(".user-dropdown");
    $dropdown.toggleClass("active");
  });

  // Close dropdown when clicking outside
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".user-dropdown").length) {
      $(".user-dropdown").removeClass("active");
    }
  });

  // Close dropdown when clicking on dropdown item
  $(".dropdown-item").on("click", function () {
    $(".user-dropdown").removeClass("active");
  });

  // ===== PRODUCT CUSTOMIZE MODAL =====
  // Helper function to get API path
  function getApiPath(endpoint) {
    const currentPath = window.location.pathname;
    let apiPath = `api/${endpoint}`;

    if (currentPath.includes("/pages/")) {
      const pathParts = currentPath.split("/").filter((p) => p);
      const pagesIndex = pathParts.indexOf("pages");
      if (pagesIndex >= 0) {
        const levels = pathParts.length - pagesIndex - 1;
        apiPath = "../".repeat(levels) + apiPath;
      }
    }
    return apiPath;
  }

  // Format currency helper
  function formatCurrency(amount) {
    return new Intl.NumberFormat("vi-VN").format(amount) + "₫";
  }

  // Open product customize modal
  function openProductCustomizeModal(productId) {
    const $modal = $("#product-customize-modal");
    const $loading = $("#modal-loading");
    const $content = $("#modal-product-content");

    // Show modal
    $modal.addClass("active");
    $("body").css("overflow", "hidden");

    // Show loading, hide content
    $loading.show();
    $content.hide();

    // Load product data
    $.ajax({
      url: getApiPath(`product/get.php?id=${productId}`),
      method: "GET",
      dataType: "json",
      success: function (response) {
        $loading.hide();
        if (response.success && response.data) {
          renderProductModal(response.data);
          $content.show();
        } else {
          alert(
            "Không thể tải thông tin sản phẩm: " +
              (response.message || "Lỗi không xác định")
          );
          closeProductModal();
        }
      },
      error: function (xhr, status, error) {
        $loading.hide();
        console.error("Error loading product:", error);
        alert("Có lỗi xảy ra khi tải sản phẩm. Vui lòng thử lại.");
        closeProductModal();
      },
    });
  }

  // Render product data in modal
  function renderProductModal(data) {
    const product = data.product;
    const optionGroups = data.optionGroups;

    // Set product info
    $("#modal-product-id").val(product.MaSP);
    $("#modal-base-price").val(product.GiaCoBan);
    $("#modal-product-name").text(product.TenSP);
    $("#modal-current-price").text(formatCurrency(product.GiaCoBan));
    $("#modal-old-price").text(formatCurrency(product.GiaCoBan * 1.3));

    // Set product image
    const imagePath = product.HinhAnh || "assets/img/products/product_one.png";
    const currentPath = window.location.pathname;
    let imageUrl = imagePath;
    if (currentPath.includes("/pages/menu/")) {
      imageUrl = "../../" + imagePath;
    } else if (currentPath.includes("/pages/")) {
      imageUrl = "../../../" + imagePath;
    }
    $("#modal-product-image").attr("src", imageUrl).attr("alt", product.TenSP);

    // Reset quantity
    $("#modal-quantity").val(1);
    $("#modal-product-note").val("");
    $("#modal-char-count").text("0");

    // Render option groups
    const $optionGroupsContainer = $("#modal-option-groups");
    $optionGroupsContainer.empty();

    optionGroups.forEach(function (group) {
      const $groupDiv = $('<div class="option-group"></div>');
      $groupDiv.append(
        `<h3 class="option-group-title">Chọn ${group.TenNhom}</h3>`
      );
      const $optionList = $('<div class="option-list"></div>');

      group.options.forEach(function (option, index) {
        const isFirst = index === 0;
        const inputType = group.IsMultiple ? "checkbox" : "radio";
        const inputName = group.IsMultiple
          ? "options[]"
          : `option_group_${group.MaOptionGroup}`;
        const checked = !group.IsMultiple && isFirst ? "checked" : "";

        const $optionItem = $(`
          <div class="option-item">
            <input 
              type="${inputType}" 
              name="${inputName}" 
              id="modal_option_${option.MaOptionValue}"
              value="${option.MaOptionValue}"
              data-price="${option.GiaThem}"
              class="${group.IsMultiple ? "option-checkbox" : "option-radio"}"
              ${checked}
            >
            <label for="modal_option_${option.MaOptionValue}">
              ${option.TenGiaTri}
            </label>
            ${
              option.GiaThem > 0
                ? `<span class="option-price">+${formatCurrency(
                    option.GiaThem
                  )}</span>`
                : ""
            }
          </div>
        `);

        $optionList.append($optionItem);
      });

      $groupDiv.append($optionList);
      $optionGroupsContainer.append($groupDiv);
    });

    // Initialize modal handlers
    initModalHandlers();
  }

  // Initialize modal event handlers
  function initModalHandlers() {
    const basePrice = parseFloat($("#modal-base-price").val());
    let quantity = parseInt($("#modal-quantity").val()) || 1;

    // Update total price function
    function updateModalTotalPrice() {
      let total = basePrice;

      // Add selected options prices
      $(".option-checkbox:checked, .option-radio:checked").each(function () {
        total += parseFloat($(this).data("price") || 0);
      });

      // Multiply by quantity
      total *= quantity;

      // Update display
      $("#modal-total-price").text(formatCurrency(total));
    }

    // Quantity controls
    $("#modal-increase-qty")
      .off("click")
      .on("click", function () {
        if (quantity < 10) {
          quantity++;
          $("#modal-quantity").val(quantity);
          updateModalTotalPrice();
        }
      });

    $("#modal-decrease-qty")
      .off("click")
      .on("click", function () {
        if (quantity > 1) {
          quantity--;
          $("#modal-quantity").val(quantity);
          updateModalTotalPrice();
        }
      });

    $("#modal-quantity")
      .off("change")
      .on("change", function () {
        quantity = Math.max(1, Math.min(10, parseInt($(this).val()) || 1));
        $(this).val(quantity);
        updateModalTotalPrice();
      });

    // Option change handlers
    $(document)
      .off(
        "change",
        "#modal-product-content .option-checkbox, #modal-product-content .option-radio"
      )
      .on(
        "change",
        "#modal-product-content .option-checkbox, #modal-product-content .option-radio",
        function () {
          $(this)
            .closest(".option-item")
            .toggleClass("selected", $(this).is(":checked"));
          updateModalTotalPrice();
        }
      );

    // Initialize selected state
    $(
      "#modal-product-content .option-radio:checked, #modal-product-content .option-checkbox:checked"
    ).each(function () {
      $(this).closest(".option-item").addClass("selected");
    });

    // Note character counter
    $("#modal-product-note")
      .off("input")
      .on("input", function () {
        const length = $(this).val().length;
        $("#modal-char-count").text(length);
      });

    // Add to cart button
    $("#modal-add-to-cart-btn")
      .off("click")
      .on("click", function () {
        const options = [];

        // Collect selected options
        $(
          "#modal-product-content .option-checkbox:checked, #modal-product-content .option-radio:checked"
        ).each(function () {
          options.push({
            option_value_id: $(this).val(),
            price: parseFloat($(this).data("price") || 0),
          });
        });

        // Calculate total
        let total = basePrice;
        options.forEach(function (opt) {
          total += opt.price;
        });
        total *= quantity;

        const formData = {
          product_id: $("#modal-product-id").val(),
          quantity: quantity,
          options: JSON.stringify(options),
          note: $("#modal-product-note").val(),
          base_price: basePrice,
          total_price: total,
        };

        // Send to cart API
        $.ajax({
          url: getApiPath("cart/add.php"),
          method: "POST",
          data: formData,
          dataType: "json",
          success: function (response) {
            if (response.success) {
              closeProductModal();
              updateCartCount();
            } else {
              alert(
                "Có lỗi xảy ra: " + (response.message || "Vui lòng thử lại")
              );
            }
          },
          error: function (xhr, status, error) {
            console.error("Error:", error);
            alert("Có lỗi xảy ra. Vui lòng thử lại.");
          },
        });
      });

    // Initial price update
    updateModalTotalPrice();
  }

  // Close product modal
  function closeProductModal() {
    $("#product-customize-modal").removeClass("active");
    $("body").css("overflow", "");
  }

  // Close modal handlers
  $("#close-modal-btn, .modal-overlay").on("click", function (e) {
    if (e.target === this) {
      closeProductModal();
    }
  });

  // Close modal on ESC key
  $(document).on("keydown", function (e) {
    if (
      e.key === "Escape" &&
      $("#product-customize-modal").hasClass("active")
    ) {
      closeProductModal();
    }
  });
});
