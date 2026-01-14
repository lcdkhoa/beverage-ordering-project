/**
 * Management Page JavaScript
 * AJAX CRUD operations for product management
 */

$(document).ready(function () {
  // Calculate API base path
  function getApiBasePath() {
    const currentPath = window.location.pathname;
    let apiPath = "api/management/";

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

  const apiBasePath = getApiBasePath();
  const isAdmin = $("#btn-add-product").length > 0; // Check if add button exists

  // Load products on page load
  loadProducts();

  // Load categories for dropdown (Admin only)
  if (isAdmin) {
    loadCategories();
  }

  // ===== MODAL HANDLERS =====
  // Add Product Modal
  $("#btn-add-product").on("click", function () {
    // Load categories first, then open modal
    if (isAdmin) {
      loadCategories();
    }
    $("#add-product-modal").addClass("active");
    // Reset form after a small delay to ensure categories are loaded
    setTimeout(function () {
      $("#add-product-form")[0].reset();
      // Ensure select is enabled
      $("#product-category").prop("disabled", false);
    }, 100);
  });

  $("#close-add-modal, #cancel-add-product, .modal-overlay").on(
    "click",
    function (e) {
      if (
        $(e.target).hasClass("modal-overlay") ||
        $(e.target).closest(".modal-close").length ||
        $(e.target).attr("id") === "cancel-add-product"
      ) {
        $("#add-product-modal").removeClass("active");
      }
    }
  );

  // Edit Price Modal
  $(document).on("click", ".btn-edit-price", function () {
    const productId = $(this).data("product-id");
    const productName = $(this).data("product-name");
    const currentPrice = $(this).data("product-price");

    $("#edit-product-id").val(productId);
    $("#edit-product-name").val(productName);
    $("#edit-product-price").val(currentPrice);
    $("#edit-price-modal").addClass("active");
  });

  $("#close-edit-modal, #cancel-edit-price, .modal-overlay").on(
    "click",
    function (e) {
      if (
        $(e.target).hasClass("modal-overlay") ||
        $(e.target).closest(".modal-close").length ||
        $(e.target).attr("id") === "cancel-edit-price"
      ) {
        $("#edit-price-modal").removeClass("active");
      }
    }
  );

  // Close modal on Escape key
  $(document).on("keydown", function (e) {
    if (e.key === "Escape") {
      $(".modal").removeClass("active");
    }
  });

  // Delete Product Handler
  $(document).on("click", ".btn-delete-product", function () {
    const productId = $(this).data("product-id");
    const productName = $(this).data("product-name");

    // Confirm before deleting
    if (
      !confirm(
        "Bạn có chắc chắn muốn xóa sản phẩm '" +
          productName +
          "'?\n\nHành động này không thể hoàn tác."
      )
    ) {
      return;
    }

    // Submit via AJAX
    $.ajax({
      url: apiBasePath + "delete-product.php",
      method: "POST",
      data: {
        product_id: productId,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          loadProducts(); // Reload products list
        } else {
          showAlert(response.message || "Có lỗi xảy ra", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        showAlert("Có lỗi xảy ra khi xóa sản phẩm. Vui lòng thử lại.", "error");
      },
    });
  });

  // ===== FORM SUBMISSIONS =====
  // Add Product Form
  $("#add-product-form").on("submit", function (e) {
    e.preventDefault();

    const formData = {
      ten_sp: $("#product-name").val().trim(),
      ma_category: $("#product-category").val(),
      gia_co_ban: $("#product-price").val(),
      hinh_anh: $("#product-image").val().trim() || "",
    };

    // Validation
    if (!formData.ten_sp) {
      showAlert("Vui lòng nhập tên sản phẩm", "error");
      return;
    }

    if (!formData.ma_category) {
      showAlert("Vui lòng chọn danh mục", "error");
      return;
    }

    if (!formData.gia_co_ban || formData.gia_co_ban < 0) {
      showAlert("Vui lòng nhập giá bán hợp lệ", "error");
      return;
    }

    // Submit via AJAX
    $.ajax({
      url: apiBasePath + "create-product.php",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          $("#add-product-modal").removeClass("active");
          $("#add-product-form")[0].reset();
          loadProducts(); // Reload products list
        } else {
          showAlert(response.message || "Có lỗi xảy ra", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        showAlert(
          "Có lỗi xảy ra khi thêm sản phẩm. Vui lòng thử lại.",
          "error"
        );
      },
    });
  });

  // Edit Price Form
  $("#edit-price-form").on("submit", function (e) {
    e.preventDefault();

    const formData = {
      product_id: $("#edit-product-id").val(),
      price: $("#edit-product-price").val(),
    };

    // Validation
    if (!formData.price || formData.price < 0) {
      showAlert("Vui lòng nhập giá bán hợp lệ", "error");
      return;
    }

    // Submit via AJAX
    $.ajax({
      url: apiBasePath + "update-price.php",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          showAlert(response.message, "success");
          $("#edit-price-modal").removeClass("active");
          loadProducts(); // Reload products list
        } else {
          showAlert(response.message || "Có lỗi xảy ra", "error");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        showAlert("Có lỗi xảy ra khi cập nhật giá. Vui lòng thử lại.", "error");
      },
    });
  });

  // ===== AJAX FUNCTIONS =====
  function loadProducts() {
    $.ajax({
      url: apiBasePath + "products.php",
      method: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          renderProducts(response.data);
        } else {
          $("#products-accordion").html(
            '<div class="alert alert-error">' +
              (response.message || "Không thể tải danh sách sản phẩm") +
              "</div>"
          );
        }
      },
      error: function (xhr, status, error) {
        console.error("Error loading products:", error);
        $("#products-accordion").html(
          '<div class="alert alert-error">Có lỗi xảy ra khi tải danh sách sản phẩm</div>'
        );
      },
    });
  }

  function loadCategories() {
    const $select = $("#product-category");

    // Ensure select is enabled before loading
    $select.prop("disabled", false);

    $.ajax({
      url: apiBasePath + "categories.php",
      method: "GET",
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Keep the first option (placeholder)
          $select.find("option:not(:first)").remove();

          if (response.data && response.data.length > 0) {
            response.data.forEach(function (category) {
              $select.append(
                $("<option></option>")
                  .attr("value", category.MaCategory)
                  .text(category.TenCategory)
              );
            });
            // Ensure select is enabled when options are loaded
            $select.prop("disabled", false).removeAttr("disabled");
          } else {
            console.warn("No categories found");
            // Don't disable, just show warning
            if ($select.find("option").length === 1) {
              $select.append(
                $("<option></option>")
                  .attr("value", "")
                  .text("Không có danh mục nào")
                  .prop("disabled", true)
              );
            }
          }
        } else {
          console.error("Failed to load categories:", response.message);
          // Don't disable select, just show error in placeholder
          if ($select.find("option").length === 1) {
            $select.find("option:first").text("-- Lỗi tải danh mục --");
          }
        }
      },
      error: function (xhr, status, error) {
        console.error("Error loading categories:", error);
        // Don't disable select, just show error
        if ($select.find("option").length === 1) {
          $select.find("option:first").text("-- Lỗi tải danh mục --");
        }
      },
    });
  }

  function renderProducts(products) {
    const $accordion = $("#products-accordion");

    if (products.length === 0) {
      $accordion.html('<div class="empty-state">Chưa có sản phẩm nào</div>');
      return;
    }

    // Group products by category
    const productsByCategory = {};
    products.forEach(function (product) {
      const categoryName = product.TenCategory || "Khác";
      if (!productsByCategory[categoryName]) {
        productsByCategory[categoryName] = [];
      }
      productsByCategory[categoryName].push(product);
    });

    // Build accordion HTML
    let html = "";
    let accordionIndex = 0;

    Object.keys(productsByCategory)
      .sort()
      .forEach(function (categoryName) {
        const categoryProducts = productsByCategory[categoryName];
        const accordionId = "accordion-" + accordionIndex;
        const isExpanded = accordionIndex === 0 ? "expanded" : ""; // First category expanded by default

        html += '<div class="accordion-item ' + isExpanded + '">';
        html +=
          '<div class="accordion-header" data-accordion="' + accordionId + '">';
        html += '<div class="accordion-title">';
        html +=
          '<span class="category-name">' + escapeHtml(categoryName) + "</span>";
        html +=
          '<span class="product-count">(' +
          categoryProducts.length +
          " sản phẩm)</span>";
        html += "</div>";
        html +=
          '<svg class="accordion-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
        html += '<path d="M6 9l6 6 6-6"/>';
        html += "</svg>";
        html += "</div>";

        html += '<div class="accordion-content" id="' + accordionId + '">';
        html += '<div class="products-table-wrapper">';
        html += '<table class="products-table">';
        html += "<thead>";
        html += "<tr>";
        html += "<th>Mã SP</th>";
        html += "<th>Hình ảnh</th>";
        html += "<th>Tên sản phẩm</th>";
        html += "<th>Giá bán</th>";
        if (isAdmin) {
          html += "<th>Thao tác</th>";
        }
        html += "</tr>";
        html += "</thead>";
        html += "<tbody>";

        categoryProducts.forEach(function (product) {
          const imagePath =
            product.HinhAnh || "assets/img/products/product_one.png";
          const price = formatCurrency(product.GiaCoBan);

          html += "<tr>";
          html += "<td>" + product.MaSP + "</td>";
          html +=
            '<td><img src="../../' +
            imagePath +
            '" alt="' +
            escapeHtml(product.TenSP) +
            '" class="product-image"></td>';
          html +=
            '<td><div class="product-name">' +
            escapeHtml(product.TenSP) +
            "</div></td>";
          html += '<td><div class="product-price">' + price + "</div></td>";

          if (isAdmin) {
            html += "<td>";
            html += '<div class="action-buttons">';
            html +=
              '<button type="button" class="btn btn-edit btn-edit-price" ' +
              'data-product-id="' +
              product.MaSP +
              '" ' +
              'data-product-name="' +
              escapeHtml(product.TenSP) +
              '" ' +
              'data-product-price="' +
              product.GiaCoBan +
              '">';
            html +=
              '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            html +=
              '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>';
            html +=
              '<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>';
            html += "</svg>";
            html += " Sửa giá";
            html += "</button>";
            html +=
              '<button type="button" class="btn btn-delete btn-delete-product" ' +
              'data-product-id="' +
              product.MaSP +
              '" ' +
              'data-product-name="' +
              escapeHtml(product.TenSP) +
              '">';
            html +=
              '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">';
            html += '<polyline points="3 6 5 6 21 6"/>';
            html +=
              '<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>';
            html += "</svg>";
            html += " Xóa";
            html += "</button>";
            html += "</div>";
            html += "</td>";
          }

          html += "</tr>";
        });

        html += "</tbody>";
        html += "</table>";
        html += "</div>";
        html += "</div>";
        html += "</div>";

        accordionIndex++;
      });

    $accordion.html(html);

    // Initialize accordion toggle functionality
    initAccordion();
  }

  function initAccordion() {
    $(".accordion-header")
      .off("click")
      .on("click", function () {
        const accordionId = $(this).data("accordion");
        const $item = $(this).closest(".accordion-item");
        const $content = $("#" + accordionId);

        // Toggle current item
        if ($item.hasClass("expanded")) {
          $item.removeClass("expanded");
          $content.slideUp(300);
        } else {
          $item.addClass("expanded");
          $content.slideDown(300);
        }
      });
  }

  // ===== HELPER FUNCTIONS =====
  function formatCurrency(amount) {
    return new Intl.NumberFormat("vi-VN", {
      style: "currency",
      currency: "VND",
      minimumFractionDigits: 0,
    }).format(amount);
  }

  function escapeHtml(text) {
    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return String(text).replace(/[&<>"']/g, function (m) {
      return map[m];
    });
  }

  function showAlert(message, type) {
    // Remove existing alerts
    $(".alert").remove();

    const alertClass = type === "success" ? "alert-success" : "alert-error";
    const $alert = $(
      '<div class="alert ' + alertClass + '">' + escapeHtml(message) + "</div>"
    );

    // Insert at the top of management content
    $(".management-content").prepend($alert);

    // Auto remove after 5 seconds
    setTimeout(function () {
      $alert.fadeOut(function () {
        $(this).remove();
      });
    }, 5000);
  }
});
