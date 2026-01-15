/**
 * Checkout Page JavaScript
 * Xử lý các tương tác trên trang checkout
 * Requires: common.js
 */

$(document).ready(function () {

  // Update note counter
  $("#order-note").on("input", function () {
    const length = $(this).val().length;
    $(this)
      .siblings(".note-counter")
      .text(length + "/52 ký tự");
  });

  // VAT invoice checkbox
  $("#vat-invoice").on("change", function () {
    if ($(this).is(":checked")) {
      $("#vat-fields").slideDown(300);
      // Make VAT fields required
      $("#vat-fields input").prop("required", true);
    } else {
      $("#vat-fields").slideUp(300);
      // Remove required
      $("#vat-fields input").prop("required", false);
    }
  });

  // Store selection
  $("#store-select").on("change", function () {
    const selectedOption = $(this).find(":selected");
    const phone = selectedOption.data("phone");
    const address = selectedOption.data("address");

    if (phone || address) {
      $("#store-phone").text(phone || "");
      $("#store-address").text(address || "");
      $("#store-info").slideDown(300);
    } else {
      $("#store-info").slideUp(300);
    }
  });

  // Promotion code variables
  let appliedPromotion = null;

  // Apply promotion code
  $("#btn-apply-promotion").on("click", function () {
    applyPromotionCode();
  });

  // Apply on Enter key
  $("#promotion-code").on("keypress", function (e) {
    if (e.which === 13) {
      e.preventDefault();
      applyPromotionCode();
    }
  });

  // Remove promotion code
  $("#btn-remove-promotion").on("click", function () {
    removePromotionCode();
  });

  function applyPromotionCode() {
    const code = $("#promotion-code").val().trim();
    const $message = $("#promotion-message");
    const $btnApply = $("#btn-apply-promotion");
    const $btnRemove = $("#btn-remove-promotion");

    // Clear previous message
    $message.removeClass("promotion-success promotion-error").text("");

    if (!code) {
      $message.addClass("promotion-error").text("Vui lòng nhập mã khuyến mãi");
      return;
    }

    // Disable button while validating
    $btnApply.prop("disabled", true).text("Đang kiểm tra...");

    // Get subtotal
    const subtotal =
      parseFloat($("#subtotal").text().replace(/[^\d]/g, "")) || 0;

    // Validate promotion code via API
    $.ajax({
      url: getApiPath("promotion/validate.php"),
      method: "POST",
      data: {
        code: code,
        subtotal: subtotal,
      },
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Store applied promotion
          appliedPromotion = response.promotion;

          // Update UI
          $message.addClass("promotion-success").text(response.message);
          $("#promotion-code").prop("disabled", true);
          $btnApply.hide();
          $btnRemove.show();

          // Update discount and total
          updatePromotionDiscount(response.discount);
          updateTotals();
        } else {
          $message
            .addClass("promotion-error")
            .text(response.message || "Mã khuyến mãi không hợp lệ");
          appliedPromotion = null;
          updatePromotionDiscount(0);
          updateTotals();
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        $message
          .addClass("promotion-error")
          .text("Có lỗi xảy ra. Vui lòng thử lại.");
        appliedPromotion = null;
      },
      complete: function () {
        $btnApply.prop("disabled", false).text("Áp dụng");
      },
    });
  }

  function removePromotionCode() {
    const $message = $("#promotion-message");
    const $btnApply = $("#btn-apply-promotion");
    const $btnRemove = $("#btn-remove-promotion");

    // Clear promotion
    appliedPromotion = null;
    $("#promotion-code").val("").prop("disabled", false);
    $message.removeClass("promotion-success promotion-error").text("");
    $btnApply.show();
    $btnRemove.hide();

    // Update discount and total
    updatePromotionDiscount(0);
    updateTotals();
  }

  function updatePromotionDiscount(discount) {
    if (discount > 0) {
      $("#promotion-discount").text("-" + formatCurrency(discount));
      $("#promotion-row").slideDown(300);
    } else {
      $("#promotion-discount").text("-0₫");
      $("#promotion-row").slideUp(300);
    }
  }

  // Calculate totals
  function updateTotals() {
    const subtotal =
      parseFloat($("#subtotal").text().replace(/[^\d]/g, "")) || 0;
    const shippingFee =
      parseFloat($("#shipping-fee").text().replace(/[^\d]/g, "")) || 0;
    const promotionDiscount =
      parseFloat($("#promotion-discount").text().replace(/[^\d]/g, "")) || 0;

    const total = subtotal + shippingFee - promotionDiscount;
    $("#total-amount").text(formatCurrency(total));
  }

  // Pay now button
  $("#pay-now-btn").on("click", function () {
    // Validate form
    if (!$("#agree-terms").is(":checked")) {
      alert("Vui lòng đồng ý với điều khoản mua hàng");
      return;
    }

    const storeId = $("#store-select").val();
    if (!storeId) {
      alert("Vui lòng chọn cửa hàng");
      return;
    }

    const paymentMethod = $("input[name='payment_method']:checked").val();
    if (!paymentMethod) {
      alert("Vui lòng chọn phương thức thanh toán");
      return;
    }

    // Check VAT fields if VAT invoice is checked
    if ($("#vat-invoice").is(":checked")) {
      const vatEmail = $("input[name='vat_email']").val();
      const vatTaxId = $("input[name='vat_tax_id']").val();
      const vatCompany = $("input[name='vat_company']").val();
      const vatAddress = $("input[name='vat_address']").val();

      if (!vatEmail || !vatTaxId || !vatCompany || !vatAddress) {
        alert("Vui lòng điền đầy đủ thông tin hóa đơn VAT");
        return;
      }
    }

    // Disable button
    $(this).prop("disabled", true).text("Đang xử lý...");

    // Prepare order data
    const orderData = {
      store_id: storeId,
      payment_method: paymentMethod,
      order_note: $("#order-note").val(),
      vat_invoice: $("#vat-invoice").is(":checked") ? 1 : 0,
      vat_email: $("input[name='vat_email']").val() || "",
      vat_tax_id: $("input[name='vat_tax_id']").val() || "",
      vat_company: $("input[name='vat_company']").val() || "",
      vat_address: $("input[name='vat_address']").val() || "",
      promotion_code: appliedPromotion ? appliedPromotion.code : "",
      promotion_id: appliedPromotion ? appliedPromotion.id : null,
      promotion_discount: appliedPromotion
        ? appliedPromotion.discount_amount
        : 0,
    };

    // Submit order
    $.ajax({
      url: getApiPath("order/create.php"),
      method: "POST",
      data: orderData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Redirect to order result page
          window.location.href =
            "order_result.php?order_id=" + response.order_id;
        } else {
          alert("Có lỗi xảy ra: " + (response.message || "Vui lòng thử lại"));
          $("#pay-now-btn").prop("disabled", false).text("Thanh toán ngay");
        }
      },
      error: function (xhr, status, error) {
        console.error("Error:", error);
        alert("Có lỗi xảy ra. Vui lòng thử lại.");
        $("#pay-now-btn").prop("disabled", false).text("Thanh toán ngay");
      },
    });
  });

  // Initialize
  updateTotals();
});
