/**
 * Register Page JavaScript
 * Handles password toggle, validation, and form submission
 */

$(document).ready(function () {
  // Password toggle visibility
  function setupPasswordToggle(toggleId, inputId) {
    $(toggleId).on("click", function () {
      const passwordInput = $(inputId);
      const hiddenIcon = $(this).find(".eye-icon-hidden");
      const visibleIcon = $(this).find(".eye-icon-visible");

      if (passwordInput.attr("type") === "password") {
        passwordInput.attr("type", "text");
        hiddenIcon.hide();
        visibleIcon.show();
      } else {
        passwordInput.attr("type", "password");
        hiddenIcon.show();
        visibleIcon.hide();
      }
    });
  }

  setupPasswordToggle("#passwordToggle", "#password");

  // Register form submit
  $("#registerForm").on("submit", function (e) {
    e.preventDefault();

    const $form = $(this);
    const $btn = $("#registerBtn");
    const $btnText = $btn.find(".btn-text");
    const $btnLoading = $btn.find(".btn-loading");
    const $message = $("#registerMessage");

    // Reset message
    $message.hide().removeClass("success error").text("");

    // Disable button and show loading
    $btn.prop("disabled", true);
    $btnText.hide();
    $btnLoading.show();

    // Get form data
    const formData = {
      username: $("#username").val().trim(),
      password: $("#password").val(),
      ho: $("#ho").val().trim(),
      ten: $("#ten").val().trim(),
      dien_thoai: $("#dien_thoai").val().trim() || null,
      email: $("#email").val().trim() || null,
    };

    // AJAX request
    $.ajax({
      url: "../../api/auth/register.php",
      method: "POST",
      data: formData,
      dataType: "json",
      success: function (response) {
        if (response.success) {
          // Show success message
          $message
            .addClass("success")
            .text(response.message || "Đăng ký thành công!")
            .show();

          // Redirect after 1.5 seconds
          setTimeout(function () {
            window.location.href = "../../index.php";
          }, 1500);
        } else {
          // Show error message
          $message
            .addClass("error")
            .text(response.message || "Đăng ký thất bại. Vui lòng thử lại.")
            .show();

          // Re-enable button
          $btn.prop("disabled", false);
          $btnText.show();
          $btnLoading.hide();
        }
      },
      error: function (xhr, status, error) {
        console.error("Register error:", error);
        let errorMessage = "Có lỗi xảy ra. Vui lòng thử lại sau.";

        // Try to parse error response
        if (xhr.responseText) {
          try {
            const errorResponse = JSON.parse(xhr.responseText);
            if (errorResponse.message) {
              errorMessage = errorResponse.message;
            }
          } catch (e) {
            // Use default error message
          }
        }

        $message.addClass("error").text(errorMessage).show();

        // Re-enable button
        $btn.prop("disabled", false);
        $btnText.show();
        $btnLoading.hide();
      },
    });
  });
});
