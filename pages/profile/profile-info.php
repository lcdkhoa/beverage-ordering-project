<!-- Personal Info Tab -->
<div id="infoTab" class="profile-tab active">
    <div class="profile-tab-header">
        <h1 class="profile-tab-title">Thông tin cá nhân</h1>
        <p class="profile-tab-subtitle">Xem và quản lý thông tin tài khoản của bạn</p>
    </div>

    <div class="profile-form-card">
        <form id="updateProfileForm" class="profile-form" method="POST">
            <!-- Account Information Section -->
            <div class="form-section collapsible">
                <h3 class="form-section-title collapsible-header" data-target="account-info">
                    <span class="section-title-text">Thông tin tài khoản</span>
                    <svg class="collapse-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </h3>
                <div id="account-info" class="collapsible-content active">
                <div class="form-row">
                    <!-- Username (Read-only) -->
                    <div class="form-group">
                        <label for="username" class="form-label">Tên đăng nhập</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input form-input-readonly" 
                            value="<?php echo e($username); ?>"
                            readonly
                            disabled
                        >
                    </div>

                    <!-- GioiTinh (Editable) -->
                    <div class="form-group">
                        <label for="gioi_tinh" class="form-label">Giới tính</label>
                        <select 
                            id="gioi_tinh" 
                            name="gioi_tinh" 
                            class="form-input dropdown-select"
                        >
                            <option value="">-- Chọn giới tính --</option>
                            <option value="M" <?php echo ($userGioiTinh === 'M') ? 'selected' : ''; ?>>Nam</option>
                            <option value="F" <?php echo ($userGioiTinh === 'F') ? 'selected' : ''; ?>>Nữ</option>
                            <option value="O" <?php echo ($userGioiTinh === 'O') ? 'selected' : ''; ?>>Khác</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Ho (Read-only) -->
                    <div class="form-group">
                        <label for="ho" class="form-label">Họ</label>
                        <input 
                            type="text" 
                            id="ho" 
                            name="ho" 
                            class="form-input form-input-readonly" 
                            value="<?php echo e($userHo); ?>"
                            readonly
                            disabled
                        >
                    </div>

                    <!-- Ten (Read-only) -->
                    <div class="form-group">
                        <label for="ten" class="form-label">Tên</label>
                        <input 
                            type="text" 
                            id="ten" 
                            name="ten" 
                            class="form-input form-input-readonly" 
                            value="<?php echo e($userTen); ?>"
                            readonly
                            disabled
                        >
                    </div>
                </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section collapsible">
                <h3 class="form-section-title collapsible-header" data-target="contact-info">
                    <span class="section-title-text">Thông tin liên hệ</span>
                    <svg class="collapse-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </h3>
                <div id="contact-info" class="collapsible-content active">
                <div class="form-row">
                    <!-- Email (Editable) -->
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            placeholder="Nhập địa chỉ email"
                            value="<?php echo e($userEmail); ?>"
                            maxlength="100"
                            autocomplete="email"
                        >
                    </div>

                    <!-- DienThoai (Editable) -->
                    <div class="form-group">
                        <label for="dien_thoai" class="form-label">Số điện thoại</label>
                        <input 
                            type="tel" 
                            id="dien_thoai" 
                            name="dien_thoai" 
                            class="form-input" 
                            placeholder="Nhập số điện thoại"
                            value="<?php echo e($userPhone); ?>"
                            maxlength="20"
                            autocomplete="tel"
                        >
                    </div>
                </div>

                <!-- Address Field (Full width) -->
                <div class="form-group">
                    <label for="dia_chi" class="form-label">Địa chỉ</label>
                    <textarea 
                        id="dia_chi" 
                        name="dia_chi" 
                        class="form-input form-textarea <?php echo !$isCustomer ? 'form-input-readonly' : ''; ?>" 
                        placeholder="<?php echo $isCustomer ? 'Nhập địa chỉ của bạn' : ''; ?>"
                        rows="3"
                        maxlength="500"
                        <?php echo !$isCustomer ? 'readonly disabled' : ''; ?>
                    ><?php echo e($userDiaChi); ?></textarea>
                    <?php if ($isCustomer): ?>
                    <small class="form-hint">Địa chỉ này sẽ được sử dụng làm địa chỉ giao hàng mặc định</small>
                    <?php endif; ?>
                </div>
                </div>
            </div>

            <!-- Error/Success Message -->
            <div id="updateProfileMessage" class="login-message" style="display: none;"></div>

            <!-- Submit Button -->
            <div class="form-actions">
                <button type="submit" class="login-btn" id="updateProfileBtn">
                    <span class="btn-text">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Lưu thay đổi
                    </span>
                    <span class="btn-loading" style="display: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10" stroke-opacity="0.25"/>
                            <path d="M12 2a10 10 0 0 1 10 10" stroke-opacity="0.75"/>
                        </svg>
                        Đang xử lý...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
