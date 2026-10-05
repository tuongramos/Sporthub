
const API_BASE = window.location.origin + '/Sporhub/api/public/api';

document.addEventListener('DOMContentLoaded', function () {
    initMobileMenu();
    initMegaMenuToggle();
    initBannerSlider();
    initScrollToTop();
    setActiveNavLink();
    initNavScroll();
    initCheckout();
    initUserAuth();
    initSearch();

    if (document.getElementById('grid-bongda') || document.getElementById('sidebar-bongda') || document.querySelector('.mega-menu-column')) {
        loadClientCategories();
    }
    if (document.getElementById('new-products-list')) {
        loadNewProducts();
    }
    if (document.getElementById('products-list')) {
        loadClientProducts();
        
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                loadClientProducts();
            });
        }
    }
    if (document.getElementById('detail-title')) {
        loadProductDetail();
    }
    if (document.getElementById('promotions-list')) {
        loadPromotions();
    }

    updateCartIcon();
    if (document.getElementById('cart-tbody')) {
        renderCart();
    }
});

//User Auth State
function initUserAuth() {
    const userStr = localStorage.getItem('user');
    const btnAccount = document.getElementById('btn-account');
    
    if (userStr && btnAccount) {
        try {
            const user = JSON.parse(userStr);
            
            btnAccount.href = 'javascript:void(0)';
            btnAccount.style.position = 'relative';
            
            btnAccount.innerHTML = `
                <img src="assets/images/user.png" alt="Tài khoản">
                <span style="max-width: 80px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${user.username}</span>
                <div class="user-dropdown-menu" style="display: none; position: absolute; top: 100%; right: -20px; background: white; border: 1px solid #ddd; border-radius: 4px; padding: 10px; z-index: 1000; min-width: 150px; text-align: left; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    <div style="padding: 8px 0; cursor: pointer; color: #333; font-weight: bold; border-bottom: 1px solid #eee; margin-bottom: 5px; text-align: center;" id="btn-profile">Thông tin</div>
                    <div style="padding: 5px 0; cursor: pointer; color: #f44336; font-weight: bold; text-align: center;" id="btn-logout">Đăng xuất</div>
                </div>
            `;

            const dropdownMenu = btnAccount.querySelector('.user-dropdown-menu');

            btnAccount.addEventListener('mouseenter', () => {
                dropdownMenu.style.display = 'block';
            });

            btnAccount.addEventListener('mouseleave', () => {
                dropdownMenu.style.display = 'none';
            });

            // Handle Profile Modal
            const btnProfile = btnAccount.querySelector('#btn-profile');
            if (btnProfile) {
                btnProfile.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    openProfileModal(user.id);
                });
            }

            const btnLogout = btnAccount.querySelector('#btn-logout');
            if (btnLogout) {
                btnLogout.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    localStorage.removeItem('user');
                    window.location.href = 'taikhoan.html';
                });
            }
        } catch (e) {
            console.error('Invalid user data', e);
        }
    }
}

function openProfileModal(userId) {
    let modal = document.getElementById('user-profile-modal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'user-profile-modal';
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.background = 'rgba(0,0,0,0.5)';
        modal.style.display = 'none';
        modal.style.alignItems = 'center';
        modal.style.justifyContent = 'center';
        modal.style.zIndex = '9999';
        
        modal.innerHTML = `
            <div class="modal-content" style="background: white; padding: 40px; border-radius: 8px; width: 90%; max-width: 500px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); text-align: left; box-sizing: border-box;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">
                    <h2 style="margin: 0; color: #f26522;">Thông tin tài khoản</h2>
                    <button id="close-profile-modal" style="background: none; border: none; font-size: 20px; cursor: pointer;">&times;</button>
                </div>
                <form id="profile-form">
                    <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Họ</label>
                            <input type="text" id="profile-last-name" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tên</label>
                            <input type="text" id="profile-first-name" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        </div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tên đăng nhập</label>
                        <input type="text" id="profile-username" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email</label>
                        <input type="email" id="profile-email" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Số điện thoại</label>
                        <input type="text" id="profile-phone" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Giới tính</label>
                        <select id="profile-sex" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                            <option value="">Chọn giới tính</option>
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Địa chỉ</label>
                        <textarea id="profile-address" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; resize: none;"></textarea>
                    </div>
                    <button type="submit" style="width: 100%; padding: 10px; background: #f26522; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Cập nhật thông tin</button>
                </form>
            </div>
        `;
        document.body.appendChild(modal);

        document.getElementById('close-profile-modal').addEventListener('click', () => {
            modal.style.display = 'none';
        });

        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const payload = {
                username: document.getElementById('profile-username').value,
                email: document.getElementById('profile-email').value,
                first_name: document.getElementById('profile-first-name').value,
                last_name: document.getElementById('profile-last-name').value,
                phone_number: document.getElementById('profile-phone').value,
                sex: document.getElementById('profile-sex').value,
                address: document.getElementById('profile-address').value
            };

            try {
                const res = await fetch(`${API_BASE}/users/${userId}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });
                const result = await res.json();
                if (result.success) {
                    alert('Cập nhật thông tin thành công!');
                    // Update username in localStorage if changed
                    const userStr = localStorage.getItem('user');
                    if(userStr) {
                        const user = JSON.parse(userStr);
                        user.username = payload.username;
                        localStorage.setItem('user', JSON.stringify(user));
                        window.location.reload();
                    }
                    modal.style.display = 'none';
                } else {
                    alert('Cập nhật thất bại: ' + result.message);
                }
            } catch (error) {
                console.error(error);
                alert('Lỗi kết nối đến server!');
            }
        });
    }

    // Fetch user data
    fetch(`${API_BASE}/users/${userId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.data) {
                const u = data.data;
                document.getElementById('profile-username').value = u.username || '';
                document.getElementById('profile-email').value = u.email || '';
                document.getElementById('profile-first-name').value = u.first_name || '';
                document.getElementById('profile-last-name').value = u.last_name || '';
                document.getElementById('profile-phone').value = u.phone_number || '';
                document.getElementById('profile-sex').value = u.sex || '';
                document.getElementById('profile-address').value = u.address || '';
                modal.style.display = 'flex';
            } else {
                alert('Không thể lấy thông tin người dùng');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Lỗi kết nối lấy dữ liệu!');
        });
}

//Search Functionality
function initSearch() {
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search');

    if (!searchForm || !searchInput) return;

    const urlParams = new URLSearchParams(window.location.search);
    const searchKeyword = urlParams.get('search');
    if (searchKeyword) {
        searchInput.value = searchKeyword;
    }

    // Bắt sự kiện submit form tìm kiếm
    searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const keyword = searchInput.value.trim();

        if (keyword === '') {
            window.location.href = 'sanpham.html';
            return;
        }

        window.location.href = `sanpham.html?search=${encodeURIComponent(keyword)}`;
    });
}

//Mobile Menu Toggle
function initMobileMenu() {
    const menuIcon = document.getElementById('menu-icon');
    const navMenu = document.getElementById('nav-menu');

    if (menuIcon && navMenu) {
        menuIcon.addEventListener('click', function (e) {
            e.stopPropagation();
            navMenu.classList.toggle('show');
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!menuIcon.contains(e.target) && !navMenu.contains(e.target)) {
                closeNavigationMenus();
            }
        });
    }
}

// Hàm đóng toàn bộ menu điều hướng và mega menu
function closeNavigationMenus() {
    document.querySelectorAll('.has-mega-menu').forEach(item => {
        item.classList.remove('open');
    });

    const navMenu = document.getElementById('nav-menu');
    if (navMenu) {
        navMenu.classList.remove('show');
    }
}

// Xử lý bật/tắt menu danh mục sản phẩm (Chỉ trên mobile và khi đang ở chính trang sản phẩm)
function initMegaMenuToggle() {
    const hasMegaMenus = document.querySelectorAll('.has-mega-menu');

    hasMegaMenus.forEach(item => {
        const link = item.querySelector(':scope > a');
        if (!link) return;

        link.addEventListener('click', function (e) {
            const isSanphamPage = window.location.pathname.includes('sanpham.html');
            const isArrow = e.target.classList.contains('nav-arrow');

            if (window.innerWidth <= 768) {
                if (isArrow) {
                    // Bấm vào mũi tên -> Mở/đóng danh sách danh mục con
                    e.preventDefault();
                    e.stopPropagation();
                    item.classList.toggle('open');
                    if (item.classList.contains('open')) {
                        const navMenu = document.getElementById('nav-menu');
                        if (navMenu) navMenu.scrollTop = 0;
                    }
                } else if (isSanphamPage) {
                    // Đang ở trang sanpham.html mà bấm vào chữ "Sản phẩm" -> Reset về "Tất cả sản phẩm"
                    e.preventDefault();
                    e.stopPropagation();
                    closeNavigationMenus();
                    switchCategory(null, '');
                }
                // Nếu đang ở trang khác (trang chủ,...): Cho phép điều hướng trực tiếp sang sanpham.html xem Tất cả sản phẩm
            }
        });
    });

    // Đóng mega menu khi bấm ra ngoài trên mobile
    document.addEventListener('click', function (e) {
        if (window.innerWidth <= 768 && !e.target.closest('.has-mega-menu')) {
            document.querySelectorAll('.has-mega-menu').forEach(item => item.classList.remove('open'));
        }
    });

    // Khi click vào bất kỳ link danh mục nào bên trong mega menu trên mobile: tự động đóng menu
    document.querySelectorAll('.mega-menu').forEach(menu => {
        menu.addEventListener('click', function (e) {
            const a = e.target.closest('a');
            if (a && window.innerWidth <= 768) {
                closeNavigationMenus();
            }
        });
    });
}


// Xử lý khi bấm nút Back/Forward trên trình duyệt
window.addEventListener('popstate', function () {
    if (window.location.pathname.includes('sanpham.html')) {
        loadClientProducts();
        const urlParams = new URLSearchParams(window.location.search);
        const currentCatId = urlParams.get('category_id');
        document.querySelectorAll('.sidebar-list li a, .mega-menu-column ul li a, .all-products-link').forEach(link => {
            link.classList.remove('active');
            const catId = link.getAttribute('data-category-id');
            if (currentCatId && catId && String(catId) === String(currentCatId)) {
                link.classList.add('active');
            } else if (!currentCatId && link.classList.contains('all-products-link')) {
                link.classList.add('active');
            }
        });
    }
});

//Banner Slider
function initBannerSlider() {
    const slider = document.getElementById('banner-slider');
    const slides = document.querySelectorAll('.slide');
    const dotsContainer = document.querySelector('.slider-dots');

    if (!slider || slides.length === 0) return;

    let currentIndex = 0;
    const totalSlides = slides.length;

    // Create dots
    if (dotsContainer) {
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('slider-dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', function () {
                goToSlide(i);
            });
            dotsContainer.appendChild(dot);
        }
    }

    function goToSlide(index) {
        currentIndex = index;
        updateSlider();
    }

    function updateSlider() {
        slider.style.transform = 'translateX(-' + (currentIndex * 100) + '%)';

        // Update dots
        var dots = document.querySelectorAll('.slider-dot');
        dots.forEach(function (dot, i) {
            if (i === currentIndex) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    // Expose moveSlide globally for onclick buttons
    window.moveSlide = function (direction) {
        currentIndex += direction;
        if (currentIndex < 0) currentIndex = totalSlides - 1;
        if (currentIndex >= totalSlides) currentIndex = 0;
        updateSlider();
    };

    // Auto slide
    var autoSlide = setInterval(function () {
        window.moveSlide(1);
    }, 4000);

    var container = document.querySelector('.slider-container');
    if (container) {
        container.addEventListener('mouseenter', function () {
            clearInterval(autoSlide);
        });
        container.addEventListener('mouseleave', function () {
            autoSlide = setInterval(function () {
                window.moveSlide(1);
            }, 4000);
        });
    }
}

//Scroll to Top
function initScrollToTop() {
    var btn = document.querySelector('.scroll-top');
    if (!btn) return;

    window.addEventListener('scroll', function () {
        if (window.scrollY > 300) {
            btn.classList.add('show');
        } else {
            btn.classList.remove('show');
        }
    });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

//Active Nav Link
function setActiveNavLink() {
    var currentPage = window.location.pathname.split('/').pop() || 'index.html';
    var navLinks = document.querySelectorAll('.nav-menu > li > a');

    navLinks.forEach(function (link) {
        var href = link.getAttribute('href');
        if (href) {
            var linkPage = href.split('?')[0];
            if (linkPage === currentPage) {
                link.classList.add('active');
            }
        }
    });
}

//Hide/Show Nav on Scroll
function initNavScroll() {
    let lastScrollTop = 0;
    const nav = document.querySelector('.main-nav');
    if (!nav) return;

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > lastScrollTop && scrollTop > 150) {
            nav.classList.add('nav-hidden');
        } else {
            nav.classList.remove('nav-hidden');
        }
        lastScrollTop = scrollTop;
    });
}

//Checkout Logic
function initCheckout() {
    const checkoutStep1 = document.getElementById('checkout-step-1');
    if (!checkoutStep1) return;

    const userStr = localStorage.getItem('user');
    if (!userStr) {
        alert('Vui lòng đăng nhập để thực hiện thanh toán!');
        window.location.href = 'taikhoan.html';
        return;
    }
    const user = JSON.parse(userStr);

    // Điền sẵn thông tin user
    const nameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const phoneInput = document.getElementById('phone');
    if(nameInput && user.first_name) nameInput.value = (user.last_name + ' ' + user.first_name).trim();
    if(emailInput && user.email) emailInput.value = user.email;
    if(phoneInput && user.phone_number) phoneInput.value = user.phone_number;

    const checkoutDataStr = sessionStorage.getItem('checkoutData');
    console.log('[Checkout] checkoutDataStr from sessionStorage:', checkoutDataStr);
    if(!checkoutDataStr) {
        alert('Không có sản phẩm nào để thanh toán!');
        window.location.href = 'index.html';
        return;
    }
    let checkoutData;
    try {
        checkoutData = JSON.parse(checkoutDataStr);
    } catch(e) {
        console.error('[Checkout] JSON parse error:', e);
        sessionStorage.removeItem('checkoutData');
        alert('Dữ liệu giỏ hàng bị lỗi. Vui lòng chọn lại sản phẩm!');
        window.location.href = 'index.html';
        return;
    }
    console.log('[Checkout] parsed checkoutData:', checkoutData);
    // Validate items có price hợp lệ
    if(!checkoutData.items || checkoutData.items.length === 0) {
        sessionStorage.removeItem('checkoutData');
        alert('Giỏ hàng trống. Vui lòng chọn sản phẩm!');
        window.location.href = 'index.html';
        return;
    }
    const hasInvalidPrice = checkoutData.items.some(item => !item.price || item.price <= 0);
    if(hasInvalidPrice) {
        console.warn('[Checkout] Phát hiện item có giá = 0, xóa dữ liệu cũ và yêu cầu chọn lại');
        sessionStorage.removeItem('checkoutData');
        alert('Dữ liệu sản phẩm không hợp lệ (giá = 0). Vui lòng chọn lại sản phẩm!');
        window.location.href = 'index.html';
        return;
    }
    
    // Render order summary
    const summaryItems = document.getElementById('checkout-summary-items');
    const subtotalElem = document.getElementById('subtotal');
    const totalPriceElem = document.getElementById('total-price');
    const discountRow = document.getElementById('discount-row');
    const discountAmountElem = document.getElementById('discount-amount');
    
    let subtotal = 0;
    if (summaryItems) summaryItems.innerHTML = '';
    
    checkoutData.items.forEach(item => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;
        const priceFmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(itemTotal);
        
        if (summaryItems) {
            summaryItems.innerHTML += `
                <div class="summary-item">
                    <div class="item-img-wrapper">
                        <img src="${item.img}" alt="Sản phẩm">
                        <span class="item-qty">${item.quantity}</span>
                    </div>
                    <div class="item-info">
                        <span class="item-name">${item.name} (${item.color}, ${item.size})</span>
                    </div>
                    <div class="item-price">${priceFmt}</div>
                </div>
            `;
        }
    });
    
    let currentDiscount = 0;
    let currentPromotionId = null;
    let finalTotal = subtotal;
    let shippingFee = 0;

    function updatePriceDisplay() {
        if(subtotalElem) subtotalElem.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(subtotal);
        if(currentDiscount > 0 && discountRow) {
            discountRow.style.display = 'flex';
            if(discountAmountElem) discountAmountElem.textContent = '-' + new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(currentDiscount);
        } else if(discountRow) {
            discountRow.style.display = 'none';
        }
        
        const total = subtotal - currentDiscount + shippingFee;
        finalTotal = total > 0 ? total : 0;
        if(totalPriceElem) totalPriceElem.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(finalTotal);
    }
    
    updatePriceDisplay();

    // Discount logic
    const btnApplyDiscount = document.getElementById('btn-apply-discount');
    const discountInput = document.getElementById('discount-input');
    
    if(btnApplyDiscount && discountInput) {
        btnApplyDiscount.addEventListener('click', async function() {
            const code = discountInput.value.trim();
            if(!code) {
                alert('Vui lòng nhập mã giảm giá!');
                return;
            }
            
            try {
                const res = await fetch(`${API_BASE}/promotions`);
                const data = await res.json();
                const promos = Array.isArray(data) ? data : (data.data || []);
                
                const validPromo = promos.find(p => p.code === code && p.status === 'active' && (!p.is_deleted || p.is_deleted == 0));
                
                if(!validPromo) {
                    alert('Mã giảm giá không hợp lệ hoặc đã hết hạn.');
                    return;
                }
                
                // Check min order
                if(validPromo.min_order_value && subtotal < parseFloat(validPromo.min_order_value)) {
                    alert(`Đơn hàng phải tối thiểu ${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(validPromo.min_order_value)} để áp dụng mã này.`);
                    return;
                }
                
                // Calculate
                if(validPromo.discount_amount && parseFloat(validPromo.discount_amount) > 0) {
                    currentDiscount = parseFloat(validPromo.discount_amount);
                } else if(validPromo.discount_percent && parseFloat(validPromo.discount_percent) > 0) {
                    currentDiscount = subtotal * (parseFloat(validPromo.discount_percent) / 100);
                }
                
                currentPromotionId = validPromo.id;
                alert('Áp dụng mã giảm giá thành công!');
                updatePriceDisplay();
                
            } catch(e) {
                alert('Lỗi kiểm tra mã giảm giá!');
            }
        });
    }

    // 1. Fetch Provinces API
    const provinceSelect = document.getElementById('province');
    const wardSelect = document.getElementById('ward');
    
    if(provinceSelect) {
        fetch('https://provinces.open-api.vn/api/v2/p/')
            .then(response => response.json())
            .then(data => {
                data.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.code;
                    option.textContent = province.name;
                    provinceSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error fetching provinces:', error));

        provinceSelect.addEventListener('change', function() {
            const provinceCode = this.value;
            wardSelect.innerHTML = '<option value="">Xã / Phường</option>';
            wardSelect.disabled = true;

            if (provinceCode) {
                fetch(`https://provinces.open-api.vn/api/v2/p/${provinceCode}?depth=2`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.wards) {
                            data.wards.forEach(ward => {
                                const option = document.createElement('option');
                                option.value = ward.code;
                                option.textContent = ward.name;
                                wardSelect.appendChild(option);
                            });
                            wardSelect.disabled = false;
                        }
                    })
                    .catch(error => console.error('Error fetching wards:', error));
            }
        });
    }

    // 2. Toggle Steps
    const btnToStep2 = document.getElementById('btn-to-step2');
    const btnBackStep1 = document.getElementById('btn-back-step1');
    const step2 = document.getElementById('checkout-step-2');
    const bcStep1 = document.getElementById('bc-step1');
    const bcStep2 = document.getElementById('bc-step2');
    const shippingCostElem = document.getElementById('shipping-cost');
    const discountSection = document.getElementById('discount-section');

    if(btnToStep2) {
        btnToStep2.addEventListener('click', function() {
            const inputs = document.querySelectorAll('#delivery-form input[required], #delivery-form select[required]');
            let valid = true;
            inputs.forEach(input => {
                if (!input.value) valid = false;
            });

            if (!valid) {
                alert('Vui lòng điền đầy đủ thông tin giao hàng!');
                return;
            }

            checkoutStep1.style.display = 'none';
            step2.style.display = 'block';
            bcStep1.classList.remove('active');
            bcStep2.classList.add('active');
            
            if(discountSection) discountSection.remove();

            shippingFee = 35000;
            if(shippingCostElem) shippingCostElem.textContent = '35,000₫';
            updatePriceDisplay();
        });
    }

    if(btnBackStep1) {
        btnBackStep1.addEventListener('click', function() {
            checkoutStep1.style.display = 'block';
            step2.style.display = 'none';
            bcStep1.classList.add('active');
            bcStep2.classList.remove('active');
            
            shippingFee = 0;
            if(shippingCostElem) shippingCostElem.textContent = '—';
            updatePriceDisplay();
        });
    }

    // 3. Payment Method Toggle
    const paymentRadios = document.getElementsByName('payment_method');
    const bankInfo = document.getElementById('bank-info');

    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'bank') {
                if(bankInfo) bankInfo.style.display = 'block';
            } else {
                if(bankInfo) bankInfo.style.display = 'none';
            }
        });
    });

    // 4. Complete Order Modal & API submission
    const btnCompleteOrder = document.getElementById('btn-complete-order');
    const successModal = document.getElementById('success-modal');

    if(btnCompleteOrder) {
        btnCompleteOrder.addEventListener('click', async function() {
            const pName = document.getElementById('fullname').value;
            const pPhone = document.getElementById('phone').value;
            const pAddress = document.getElementById('address').value;
            
            let pProv = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
            let pWard = wardSelect.options[wardSelect.selectedIndex]?.text || '';
            const fullAddress = `${pName} - ${pPhone} - ${pAddress}, ${pWard}, ${pProv}`;
            
            const paymentMethodInput = document.querySelector('input[name="payment_method"]:checked');
            const paymentMethod = paymentMethodInput ? paymentMethodInput.value : 'cod';
            
            // Disable button
            btnCompleteOrder.disabled = true;
            btnCompleteOrder.textContent = 'Đang xử lý...';
            
            try {
                const getVNDateTime = () => new Date().toLocaleString('sv-SE', { timeZone: 'Asia/Ho_Chi_Minh' });

                const orderPayload = {
                    order_date: getVNDateTime(),
                    total_amount: subtotal,
                    final_amount: finalTotal,
                    shipping_address: fullAddress,
                    status: 'pending',
                    user_id: user.id,
                    promotion_id: currentPromotionId
                };
                
                const orderRes = await fetch(`${API_BASE}/orders`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(orderPayload)
                });
                const orderData = await orderRes.json();
                
                if(!orderData.success) throw new Error(orderData.message || 'Lỗi tạo đơn hàng');
                const createdOrder = orderData.data;
                const orderId = createdOrder.id || (typeof createdOrder === 'number' ? createdOrder : createdOrder.insertId) || 0; // fallback if api returns object or id
                
                if(orderId) {
                    for (const item of checkoutData.items) {
                        const detailPayload = {
                            order_id: orderId,
                            product_variant_id: item.variant_id,
                            quantity: item.quantity,
                            unit_price: item.price
                        };
                        
                        await fetch(`${API_BASE}/order-details`, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify(detailPayload)
                        });
                    }
                    
                    // Create Payment
                    const paymentPayload = {
                        amount: finalTotal,
                        payment_method: paymentMethod === 'cod' ? 'COD' : 'BANK_TRANSFER',
                        payment_date: getVNDateTime(),
                        status: paymentMethod === 'cod' ? 'pending' : 'completed',
                        transaction_id: 'TXN' + Date.now(),
                        order_id: orderId
                    };
                    
                    await fetch(`${API_BASE}/payments`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(paymentPayload)
                    });
                }
                
                // Success
                sessionStorage.removeItem('checkoutData');
                if(successModal) successModal.style.display = 'flex';
                
            } catch(error) {
                console.error(error);
                alert('Lỗi đặt hàng: ' + error.message);
                btnCompleteOrder.disabled = false;
                btnCompleteOrder.textContent = 'Hoàn tất đơn hàng';
            }
        });
    }
}

// PRODUCT DETAIL PAGE LOGIC

function changeMainImage(src, thumbElement) {
    document.getElementById('main-product-image').src = src;
    
    // Update active thumbnail
    const thumbnails = document.querySelectorAll('.thumbnail');
    thumbnails.forEach(t => t.classList.remove('active'));
    thumbElement.classList.add('active');
}

function selectColor(element) {
    const options = document.querySelectorAll('.color-option');
    options.forEach(opt => {
        opt.classList.remove('active');
        opt.querySelector('.ui-icon-check').style.display = 'none';
    });
    
    element.classList.add('active');
    element.querySelector('.ui-icon-check').style.display = 'inline-block';
    
    const selectedColor = element.getAttribute('data-color');
    if(window.currentProductVariants && window.currentProductVariants.length > 0) {

        const variantsForColor = window.currentProductVariants.filter(v => v.color === selectedColor);
        const sizeOptionsContainer = document.getElementById('detail-size-options');
        
        const uniqueSizes = [...new Set(window.currentProductVariants.map(v => v.size).filter(s => s))];
        
        sizeOptionsContainer.innerHTML = '';
        uniqueSizes.forEach(size => {
            const stockOfSizeForColor = variantsForColor.filter(v => v.size === size).reduce((sum, v) => sum + parseInt(v.stock_quantity), 0);
            const isDisabled = stockOfSizeForColor <= 0 ? 'disabled' : '';
            
            sizeOptionsContainer.innerHTML += `
                <button class="size-option ${isDisabled}" ${isDisabled ? 'disabled' : ''} onclick="selectSize(this)">
                    ${size}
                </button>
            `;
        });
        
        const firstAvailable = sizeOptionsContainer.querySelector('.size-option:not(.disabled)');
        if (firstAvailable) {
            selectSize(firstAvailable);
        }
    }
}

function selectSize(element) {
    if (element.classList.contains('disabled')) return;
    
    const options = document.querySelectorAll('.size-option');
    options.forEach(opt => opt.classList.remove('active'));
    
    element.classList.add('active');
}

function decreaseQty() {
    const input = document.getElementById('product-qty');
    let val = parseInt(input.value);
    if (val > 1) {
        input.value = val - 1;
    }
}

function increaseQty() {
    const input = document.getElementById('product-qty');
    let val = parseInt(input.value);
    input.value = val + 1;
}

function validateQty() {
    const input = document.getElementById('product-qty');
    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) {
        input.value = 1;
    }
}

function addToCart() {
    const userStr = localStorage.getItem('user');
    if (!userStr) {
        alert('Vui lòng đăng nhập để thêm vào giỏ hàng!');
        window.location.href = 'taikhoan.html';
        return;
    }

    const titleElem = document.getElementById('detail-title');
    if(!titleElem || titleElem.innerText === 'Đang tải...') {
        alert('Dữ liệu sản phẩm chưa được tải xong.');
        return;
    }

    const title = titleElem.innerText;
    
    let price = 0;
    if(window.currentProductData && window.currentProductData.price) {
        price = parseFloat(window.currentProductData.price);
    } else {
        const priceText = document.getElementById('detail-current-price').innerText;
        if(priceText) {
            const cleaned = priceText.replace(/[^\d]/g, '');
            if(cleaned) price = parseInt(cleaned, 10);
        }
    }
    
    if(!price || price <= 0) {
        alert('Không thể xác định giá sản phẩm. Vui lòng thử lại!');
        return;
    }
    
    const img = document.getElementById('main-product-image').src;
    const qty = parseInt(document.getElementById('product-qty').value);
    
    const activeColor = document.querySelector('.color-option.active');
    const activeSize = document.querySelector('.size-option.active');
    
    if(!activeColor || !activeSize) {
        alert('Vui lòng chọn màu sắc và kích cỡ!');
        return;
    }
    
    const color = activeColor.getAttribute('data-color');
    const size = activeSize.innerText.trim();
    
    let variantId = null;
    if(window.currentProductVariants) {
        const variant = window.currentProductVariants.find(v => v.color === color && v.size === size);
        if(variant) {
            variantId = variant.id;
        }
    }
    
    if(!variantId) {
        alert('Sản phẩm với lựa chọn này tạm thời không khả dụng.');
        return;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    let cartItems = [];
    const cartStr = localStorage.getItem(getCartKey());
    if(cartStr) {
        try {
            cartItems = JSON.parse(cartStr);
        } catch(e) {}
    }

    const existingIndex = cartItems.findIndex(item => item.variant_id === variantId);
    if(existingIndex >= 0) {
        cartItems[existingIndex].quantity += qty;
    } else {
        cartItems.push({
            product_id: productId,
            variant_id: variantId,
            name: title,
            color: color,
            size: size,
            price: price,
            quantity: qty,
            img: img
        });
    }

    localStorage.setItem(getCartKey(), JSON.stringify(cartItems));
    updateCartIcon();
    alert('Đã thêm sản phẩm vào giỏ hàng thành công!');
}

function buyNow() {
    const userStr = localStorage.getItem('user');
    if (!userStr) {
        alert('Vui lòng đăng nhập để tiếp tục mua hàng!');
        window.location.href = 'taikhoan.html';
        return;
    }

    const titleElem = document.getElementById('detail-title');
    if(!titleElem || titleElem.innerText === 'Đang tải...') {
        alert('Dữ liệu sản phẩm chưa được tải xong.');
        return;
    }

    const title = titleElem.innerText;
    
    let price = 0;
    if(window.currentProductData && window.currentProductData.price) {
        price = parseFloat(window.currentProductData.price);
    } else {
        const priceText = document.getElementById('detail-current-price').innerText;
        if(priceText) {
            const cleaned = priceText.replace(/[^\d]/g, '');
            if(cleaned) price = parseInt(cleaned, 10);
        }
    }
    
    if(!price || price <= 0) {
        alert('Không thể xác định giá sản phẩm. Vui lòng thử lại!');
        return;
    }
    
    const img = document.getElementById('main-product-image').src;
    const qty = parseInt(document.getElementById('product-qty').value);
    
    const activeColor = document.querySelector('.color-option.active');
    const activeSize = document.querySelector('.size-option.active');
    
    if(!activeColor || !activeSize) {
        alert('Vui lòng chọn màu sắc và kích cỡ!');
        return;
    }
    
    const color = activeColor.getAttribute('data-color');
    const size = activeSize.innerText.trim();
    
    let variantId = null;
    if(window.currentProductVariants) {
        const variant = window.currentProductVariants.find(v => v.color === color && v.size === size);
        if(variant) {
            variantId = variant.id;
        }
    }
    
    if(!variantId) {
        alert('Sản phẩm với lựa chọn này tạm thời không khả dụng.');
        return;
    }
    
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    const checkoutData = {
        type: 'buy_now',
        items: [{
            product_id: productId,
            variant_id: variantId,
            name: title,
            color: color,
            size: size,
            price: price,
            quantity: qty,
            img: img
        }]
    };
    
    console.log('[BuyNow] checkoutData:', JSON.stringify(checkoutData));
    sessionStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    window.location.href = 'thanhtoan.html';
}

// CLIENT DATA FETCHING (API)

async function loadClientCategories() {
    try {
        const res = await fetch(`${API_BASE}/categories`);
        const data = await res.json();
        const categories = Array.isArray(data) ? data : (data.data || []);
        
        const gridBongDa = document.getElementById('grid-bongda');
        const gridPickleball = document.getElementById('grid-pickleball');
        const gridCauLong = document.getElementById('grid-caulong');
        
        const sidebarBongDa = document.getElementById('sidebar-bongda');
        const sidebarPickleball = document.getElementById('sidebar-pickleball');
        const sidebarCauLong = document.getElementById('sidebar-caulong');
        
        // Tìm Mega menu trên mọi trang
        let megaBongDa, megaPickleball, megaCauLong;
        const megaCols = document.querySelectorAll('.mega-menu-column');
        megaCols.forEach(col => {
            const titleElement = col.querySelector('.mega-menu-title');
            if(!titleElement) return;
            const titleText = titleElement.textContent.toLowerCase();
            const ul = col.querySelector('ul');
            if(!ul) return;
            
            if(titleText.includes('bóng đá') || titleText.includes('thủ môn')) megaBongDa = ul;
            else if(titleText.includes('pickleball')) megaPickleball = ul;
            else if(titleText.includes('cầu lông')) megaCauLong = ul;
        });

        if(gridBongDa) gridBongDa.innerHTML = '';
        if(gridPickleball) gridPickleball.innerHTML = '';
        if(gridCauLong) gridCauLong.innerHTML = '';
        
        if(sidebarBongDa) sidebarBongDa.innerHTML = '';
        if(sidebarPickleball) sidebarPickleball.innerHTML = '';
        if(sidebarCauLong) sidebarCauLong.innerHTML = '';
        
        if(megaBongDa) megaBongDa.innerHTML = '';
        if(megaPickleball) megaPickleball.innerHTML = '';
        if(megaCauLong) megaCauLong.innerHTML = '';

        const isMobile = window.innerWidth <= 768;
        const isSanphamPage = window.location.pathname.includes('sanpham.html');
        const urlParams = new URLSearchParams(window.location.search);
        
        let currentCategoryId = urlParams.get('category_id');
        const catParam = urlParams.get('cat');
        if (!currentCategoryId && catParam) {
            const foundCat = categories.find(c => {
                const slug = c.name.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                return slug === catParam || catParam.includes(slug) || slug.includes(catParam);
            });
            if (foundCat) {
                currentCategoryId = foundCat.id;
            }
        }

        categories.forEach(cat => {
            if(cat.status !== 'active') return;
            
            const nameLower = cat.name.toLowerCase();
            const imgUrl = cat.image_url || 'assets/images/logo-sporthub.png';
            
            const cardHTML = `
                <a href="sanpham.html?category_id=${cat.id}" class="category-card" style="background: linear-gradient(135deg, #f26522, #ff8a00);">
                    <span class="category-card-icon"><img src="${imgUrl}" alt="Icon" class="ui-icon-category"></span>
                    <span class="category-card-name">${cat.name}</span>
                </a>
            `;
            
            const isActive = (currentCategoryId && currentCategoryId == cat.id) ? 'active' : '';
            
            const onClickAttr = isSanphamPage ? ` onclick="switchCategory(event, ${cat.id})"` : '';
            const liHTML = `<li><a href="sanpham.html?category_id=${cat.id}" data-category-id="${cat.id}" class="${isActive}"${onClickAttr}>${cat.name}</a></li>`;
            
            
            if (nameLower.includes('bóng đá') || nameLower.includes('thủ môn')) {
                if(gridBongDa) gridBongDa.innerHTML += cardHTML;
                if(sidebarBongDa) sidebarBongDa.innerHTML += liHTML;
                if(megaBongDa) megaBongDa.innerHTML += liHTML;
            } else if (nameLower.includes('pickleball')) {
                if(gridPickleball) gridPickleball.innerHTML += cardHTML;
                if(sidebarPickleball) sidebarPickleball.innerHTML += liHTML;
                if(megaPickleball) megaPickleball.innerHTML += liHTML;
            } else if (nameLower.includes('cầu lông')) {
                if(gridCauLong) gridCauLong.innerHTML += cardHTML;
                if(sidebarCauLong) sidebarCauLong.innerHTML += liHTML;
                if(megaCauLong) megaCauLong.innerHTML += liHTML;
            } else {
                if(gridBongDa) gridBongDa.innerHTML += cardHTML;
                if(sidebarBongDa) sidebarBongDa.innerHTML += liHTML;
                if(megaBongDa) megaBongDa.innerHTML += liHTML;
            }
        });

        // Thêm nút "✨ Xem tất cả sản phẩm" vào đầu mega menu trên mobile
        const megaMenuContainers = document.querySelectorAll('.mega-menu-container');
        megaMenuContainers.forEach(container => {
            let existingAll = container.querySelector('.mega-menu-mobile-all');
            if (!existingAll) {
                existingAll = document.createElement('div');
                existingAll.className = 'mega-menu-mobile-all';
                const onClickAll = isSanphamPage ? ' onclick="switchCategory(event, \'\')"' : '';
                existingAll.innerHTML = `
                    <a href="sanpham.html" class="all-products-link ${!currentCategoryId ? 'active' : ''}"${onClickAll}>
                        ✨ Xem tất cả sản phẩm
                    </a>
                `;
                container.insertBefore(existingAll, container.firstChild);
            }
        });

        // Cập nhật trạng thái active cho nút "Tất cả sản phẩm"
        const allProductsLinks = document.querySelectorAll('.all-products-link');
        allProductsLinks.forEach(link => {
            if (!currentCategoryId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    } catch (err) {
        console.error('Error loading categories:', err);
    }
}

async function loadClientProducts(targetCategoryId) {
    const isMobile = window.innerWidth <= 768;
    const urlParams = new URLSearchParams(window.location.search);
    const searchKeyword = urlParams.get('search');
    const productsList = document.getElementById('products-list');
    
    // Xác định categoryId cần tải:
    let categoryId;
    if (targetCategoryId !== undefined) {
        // Khi người dùng chủ động bấm chọn danh mục trên trang (thông qua switchCategory)
        categoryId = targetCategoryId;
    } else {
        // Tải category từ URL param (áp dụng cho cả desktop lẫn mobile khi bấm từ trang chủ)
        categoryId = urlParams.get('category_id');
        const catParam = urlParams.get('cat');
        if (!categoryId && catParam) {
            try {
                const catRes = await fetch(`${API_BASE}/categories`);
                const catData = await catRes.json();
                const categories = Array.isArray(catData) ? catData : (catData.data || []);
                const found = categories.find(c => {
                    const slug = c.name.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
                    return slug === catParam || catParam.includes(slug) || slug.includes(catParam);
                });
                if (found) categoryId = found.id;
            } catch (e) {}
        }
    }
    
    try {
        let url = `${API_BASE}/products`;
        if (searchKeyword) {
            url += `?search=${encodeURIComponent(searchKeyword)}`;
            const titleEl = document.getElementById('page-title');
            if (titleEl) titleEl.textContent = `Kết quả tìm kiếm: "${searchKeyword}"`;
            document.title = `Tìm kiếm: ${searchKeyword} - Sporhub`;
        } else if (categoryId) {
            url += `?category_id=${categoryId}`;
            fetch(`${API_BASE}/categories/${categoryId}`).then(r => r.json()).then(catRes => {
                const cat = catRes.data || catRes;
                if(cat && cat.name) {
                    const titleEl = document.getElementById('page-title');
                    if(titleEl) titleEl.textContent = cat.name;
                    document.title = cat.name + ' - Sporhub';
                }
            }).catch(e => {});

            // Trên mobile, khi truy cập từ trang chủ hoặc link danh mục:
            // Tự động cuộn mượt đến tiêu đề sản phẩm để xem ngay danh sách sản phẩm của mục đó
            if (isMobile && targetCategoryId === undefined) {
                setTimeout(() => {
                    const titleElem = document.getElementById('page-title') || document.querySelector('.content-header');
                    if (titleElem) {
                        titleElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }, 150);
            }
        } else if (urlParams.get('sport')) {
            const sportParam = urlParams.get('sport').toLowerCase();
            const titleEl = document.getElementById('page-title');
            let sportName = 'Sản phẩm Thể thao';
            if (sportParam === 'bongda') sportName = 'Sản phẩm Bóng đá';
            else if (sportParam === 'pickleball') sportName = 'Sản phẩm Pickleball';
            else if (sportParam === 'caulong') sportName = 'Sản phẩm Cầu lông';
            if (titleEl) titleEl.textContent = sportName;
            document.title = sportName + ' - Sporhub';
        } else {
            // Xem tất cả sản phẩm
            const titleEl = document.getElementById('page-title');
            if (titleEl) titleEl.textContent = 'Tất cả sản phẩm';
            document.title = 'Tất cả sản phẩm - Sporhub';
        }
        
        if (productsList) {
            productsList.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 50px; font-weight: bold; color: var(--text-medium);">Đang tải dữ liệu...</div>';
        }
        
        const res = await fetch(url);
        const data = await res.json();
        let products = Array.isArray(data) ? data : (data.data || []);
        
        const sportParam = urlParams.get('sport');
        if (sportParam && !categoryId && !searchKeyword) {
            const s = sportParam.toLowerCase();
            products = products.filter(p => {
                const cName = (p.category_name || '').toLowerCase();
                if (s === 'bongda') return cName.includes('bóng đá') || cName.includes('thủ môn') || cName.includes('bong da');
                if (s === 'pickleball') return cName.includes('pickleball');
                if (s === 'caulong') return cName.includes('cầu lông') || cName.includes('cau long');
                return true;
            });
        }
        
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            const sortValue = sortSelect.value;
            if (sortValue === 'newest') {
                products.sort((a, b) => (b.id || 0) - (a.id || 0));
            } else if (sortValue === 'price-asc') {
                products.sort((a, b) => (parseFloat(a.price) || 0) - (parseFloat(b.price) || 0));
            } else if (sortValue === 'price-desc') {
                products.sort((a, b) => (parseFloat(b.price) || 0) - (parseFloat(a.price) || 0));
            } else if (sortValue === 'name-asc') {
                products.sort((a, b) => (a.name || '').localeCompare(b.name || ''));
            }
        }
        
        if (productsList) {
            productsList.innerHTML = '';
            
            if(products.length === 0) {
                const noResultMsg = searchKeyword 
                    ? `Không tìm thấy sản phẩm nào cho từ khóa "${searchKeyword}". <br><a href="sanpham.html" style="color: var(--primary); text-decoration: underline;">Xem tất cả sản phẩm</a>`
                    : 'Không có sản phẩm nào.';
                productsList.innerHTML = `<p style="text-align:center; grid-column: 1/-1;">${noResultMsg}</p>`;
                return;
            }

            products.forEach(p => {
                if(p.status !== 'active') return;
                const priceFormatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price || 0);
                const imgUrl = p.image_url || 'assets/images/logo-sporthub.png';
                
                const isNew = true;
                
                productsList.innerHTML += `
                    <a href="chitietsanpham.html?id=${p.id}" class="product-card">
                        <div class="product-card-img">
                            ${isNew ? '<span class="product-badge">Mới</span>' : ''}
                            <img src="${imgUrl}" alt="${p.name}">
                        </div>
                        <div class="product-card-info">
                            <p class="product-card-name" style="font-weight:bold; margin-top:10px;">${p.name}</p>
                            <div class="product-card-price">
                                <span class="price-current">${priceFormatted}</span>
                            </div>
                        </div>
                    </a>
                `;
            });
        }
    } catch (err) {
        console.error('Error loading products:', err);
        if(productsList) productsList.innerHTML = '<p>Lỗi tải dữ liệu sản phẩm.</p>';
    }
}

async function loadNewProducts() {
    const list = document.getElementById('new-products-list');
    if (!list) return;
    
    try {
        list.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 20px;">Đang tải...</div>';
        const res = await fetch(`${API_BASE}/products`);
        const data = await res.json();
        let products = Array.isArray(data) ? data : (data.data || []);
        
        products = products.filter(p => p.status === 'active');
        products.sort((a, b) => (b.id || 0) - (a.id || 0));
        
        const top5 = products.slice(0, 5);
        
        list.innerHTML = '';
        if (top5.length === 0) {
            list.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">Chưa có sản phẩm nào.</p>';
            return;
        }
        
        top5.forEach(p => {
            const priceFormatted = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price || 0);
            const imgUrl = p.image_url || 'assets/images/logo-sporthub.png';
            
            list.innerHTML += `
                <a href="chitietsanpham.html?id=${p.id}" class="product-card">
                    <div class="product-card-img">
                        <span class="product-badge">Mới</span>
                        <img src="${imgUrl}" alt="${p.name}">
                    </div>
                    <div class="product-card-info">
                        <p class="product-card-name" style="font-weight:bold; margin-top:10px;">${p.name}</p>
                        <div class="product-card-price">
                            <span class="price-current">${priceFormatted}</span>
                        </div>
                    </div>
                </a>
            `;
        });
        
    } catch (err) {
        console.error('Error loading new products:', err);
        list.innerHTML = '<p style="grid-column: 1/-1; text-align: center;">Lỗi tải dữ liệu.</p>';
    }
}

async function loadProductDetail() {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if(!productId) {
        document.getElementById('detail-title').textContent = "Không tìm thấy sản phẩm";
        return;
    }

    try {
        // 1. Fetch Product Info
        const res = await fetch(`${API_BASE}/products/${productId}`);
        if(!res.ok) throw new Error("Sản phẩm không tồn tại");
        const productData = await res.json();
        const p = productData.data || productData;
        
        document.getElementById('detail-title').textContent = p.name || 'Sản phẩm';
        document.getElementById('detail-sku').textContent = 'SP' + p.id;
        document.getElementById('detail-status').textContent = p.status === 'active' ? 'Còn hàng' : 'Ngừng kinh doanh';
        
        window.currentProductData = p;
        
        const priceFmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price || 0);
        document.getElementById('detail-current-price').textContent = priceFmt;
        document.getElementById('detail-original-price').textContent = ''; // Có thể set bằng price * 1.2
        document.getElementById('main-product-image').src = p.image_url || 'assets/images/logo-sporthub.png';
        
        // Render Gallery Thumbnails
        const thumbnailList = document.getElementById('thumbnail-list');
        if (thumbnailList) {
            thumbnailList.innerHTML = '';
            if (p.image_url) {
                thumbnailList.innerHTML += `<img class="thumbnail active" src="${p.image_url}" alt="Thumb 1" onclick="changeMainImage(this.src, this)">`;
            }
            if (p.gallery && Array.isArray(p.gallery)) {
                p.gallery.forEach((url, index) => {
                    thumbnailList.innerHTML += `<img class="thumbnail" src="${url}" alt="Thumb ${index + 2}" onclick="changeMainImage(this.src, this)">`;
                });
            }
        }
        
        document.title = p.name + ' - Sporthub';

        // 2. Fetch Product Variants
        const varRes = await fetch(`${API_BASE}/product-variants/product/${productId}`);
        const varData = await varRes.json();
        const variants = Array.isArray(varData) ? varData : (varData.data || []);
        
        const colorOptionsContainer = document.getElementById('detail-color-options');
        const sizeOptionsContainer = document.getElementById('detail-size-options');
        
        colorOptionsContainer.innerHTML = '';
        sizeOptionsContainer.innerHTML = '';

        if(variants.length === 0) {
            colorOptionsContainer.innerHTML = '<p>Không có màu sắc.</p>';
            sizeOptionsContainer.innerHTML = '<p>Không có size.</p>';
            return;
        }

        window.currentProductVariants = variants;

        const uniqueColors = [...new Set(variants.map(v => v.color).filter(c => c))];
        
        // Render Colors
        uniqueColors.forEach((color, index) => {
            const displayCheck = index === 0 ? 'inline-block' : 'none';
            colorOptionsContainer.innerHTML += `
                <div class="color-option" onclick="selectColor(this)" data-color="${color}">
                    <div class="color-info" style="margin-left: 0;">
                        <span class="color-name">
                            <span class="ui-icon-check" style="display:${displayCheck}; color:green;">✔</span> 
                            ${color}
                        </span>
                    </div>
                </div>
            `;
        });

        const firstColor = colorOptionsContainer.querySelector('.color-option');
        if (firstColor) {
            selectColor(firstColor);
        }
        
    } catch (err) {
        console.error('Error loading product details:', err);
        document.getElementById('detail-title').textContent = "Lỗi khi tải chi tiết sản phẩm";
    }
}

// Xử lý chuyển danh mục không cần reload trang
function switchCategory(event, categoryId) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    const isMobile = window.innerWidth <= 768;
    
    // Cập nhật URL đồng bộ trên cả mobile và desktop để bookmark / refresh / chia sẻ được
    try {
        if (categoryId) {
            history.replaceState(null, '', `sanpham.html?category_id=${categoryId}`);
        } else {
            history.replaceState(null, '', `sanpham.html`);
        }
    } catch (e) {}
    
    const allLinks = document.querySelectorAll('.sidebar-list li a, .mega-menu-column ul li a, .all-products-link');
    allLinks.forEach(link => {
        link.classList.remove('active');
        const catIdAttr = link.getAttribute('data-category-id');
        if (categoryId && catIdAttr) {
            // So khớp chính xác tuyệt đối bằng ID
            if (String(catIdAttr) === String(categoryId)) {
                link.classList.add('active');
            }
        } else if (!categoryId && link.classList.contains('all-products-link')) {
            link.classList.add('active');
        } else if (!isMobile) {
            const href = link.getAttribute('href') || '';
            const match = href.match(/[?&]category_id=(\d+)(?:&|$)/);
            if (categoryId && match && String(match[1]) === String(categoryId)) {
                link.classList.add('active');
            }
        }
    });

    // Trên mobile: Tự động ẩn thanh điều hướng và mega menu sau khi người dùng chọn mục
    if (isMobile) {
        closeNavigationMenus();
    }

    // Nạp lại danh sách sản phẩm theo danh mục vừa chọn (hoặc tất cả sản phẩm nếu categoryId rỗng)
    loadClientProducts(categoryId);

    // Cuộn nhẹ nhàng đến phần tiêu đề danh sách sản phẩm trên mobile
    if (window.innerWidth <= 768) {
        setTimeout(() => {
            const titleElem = document.getElementById('page-title') || document.querySelector('.content-header');
            if (titleElem) {
                titleElem.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 50);
    }
}

async function loadPromotions() {
    const promoList = document.getElementById('promotions-list');
    if(!promoList) return;
    
    try {
        const res = await fetch(`${API_BASE}/promotions`);
        const data = await res.json();
        const promos = Array.isArray(data) ? data : (data.data || []);
        
        promoList.innerHTML = '';
        
        const activePromos = promos.filter(p => p.status === 'active' && (!p.is_deleted || p.is_deleted == 0));
        
        if (activePromos.length === 0) {
            promoList.innerHTML = '<div style="text-align: center; grid-column: 1/-1; padding: 30px; font-weight: bold; color: var(--text-medium);">Hiện tại chưa có mã khuyến mãi nào.</div>';
            return;
        }
        
        activePromos.forEach(p => {
            let title = '';
            if (p.discount_percent && p.discount_percent > 0) {
                title = `Giảm ${Math.round(p.discount_percent)}%`;
            } else if (p.discount_amount && p.discount_amount > 0) {
                const amt = p.discount_amount >= 1000 ? (p.discount_amount/1000) + 'kđ' : p.discount_amount + 'đ';
                title = `Giảm ${amt}`;
            } else {
                title = `Mã Khuyến Mãi`;
            }
            
            let minOrder = p.min_order_value >= 1000 ? (p.min_order_value/1000) + 'kđ' : p.min_order_value + 'đ';
            let desc = `Đơn Tối Thiểu ${minOrder}`;
            
            let startDateStr = p.start_date ? new Date(p.start_date).toLocaleDateString('vi-VN') : '';
            let endDateStr = p.end_date ? new Date(p.end_date).toLocaleDateString('vi-VN') : '';
            
            promoList.innerHTML += `
                <div class="promo-card">
                    <div class="promo-card-left">
                        <img src="assets/images/logo-sporthub.png" alt="Voucher">
                        <span>SALE</span>
                    </div>
                    <div class="promo-card-right">
                        <div class="promo-title">${title}</div>
                        <div class="promo-desc">${desc}</div>
                        <div class="promo-code">Mã code: <strong>${p.code}</strong></div>
                        <div class="promo-time">Thời gian: ${startDateStr} - ${endDateStr}</div>
                    </div>
                </div>
            `;
        });
        
    } catch(err) {
        console.error('Error loading promotions:', err);
        promoList.innerHTML = '<div style="text-align: center; grid-column: 1/-1; padding: 30px; color: red;">Lỗi kết nối hoặc không thể tải dữ liệu mã khuyến mãi.</div>';
    }
}

// CART LOGIC
function getCartKey() {
    const userStr = localStorage.getItem('user');
    if(userStr) {
        try {
            const user = JSON.parse(userStr);
            if(user && user.id) return 'cart_items_' + user.id;
        } catch(e) {}
    }
    return 'cart_items_guest';
}
function updateCartIcon() {
    const cartCountEl = document.querySelector('.cart-count');
    if (cartCountEl) {
        let count = 0;
        const cartStr = localStorage.getItem(getCartKey());
        if(cartStr) {
            try {
                const items = JSON.parse(cartStr);
                items.forEach(i => count += parseInt(i.quantity) || 0);
            } catch(e){}
        }
        cartCountEl.innerText = count;
    }
}

function renderCart() {
    const tbody = document.getElementById('cart-tbody');
    const emptyContent = document.querySelector('.cart-empty-content');
    const filledContent = document.querySelector('.cart-filled-content');
    if(!tbody) return;

    let cartItems = [];
    const cartStr = localStorage.getItem(getCartKey());
    if(cartStr) {
        try {
            cartItems = JSON.parse(cartStr);
        } catch(e) {}
    }

    if (cartItems.length === 0) {
        emptyContent.style.display = 'block';
        filledContent.style.display = 'none';
        return;
    }

    emptyContent.style.display = 'none';
    filledContent.style.display = 'block';
    
    tbody.innerHTML = '';
    cartItems.forEach((item, index) => {
        const priceFmt = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(item.price);
        tbody.innerHTML += `
            <tr>
                <td style="text-align:center;"><input type="checkbox" class="cart-item-checkbox" data-index="${index}" onchange="updateCartTotal()"></td>
                <td class="cart-product-col">
                    <img src="${item.img}" alt="${item.name}">
                    <span>${item.name} <br><small>(${item.color} - ${item.size})</small></span>
                </td>
                <td class="cart-qty-col">
                    <div class="qty-control">
                        <button class="qty-btn" onclick="changeCartQty(${index}, -1)">-</button>
                        <input type="number" value="${item.quantity}" min="1" readonly>
                        <button class="qty-btn" onclick="changeCartQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td class="cart-price-col">${priceFmt}</td>
                <td class="cart-remove-col"><button class="btn-remove" onclick="removeFromCart(${index})"><img src="assets/images/trash.png" alt="Xóa" class="ui-icon-small"></button></td>
            </tr>
        `;
    });

    const selectAll = document.getElementById('cart-select-all');
    if(selectAll) {
        selectAll.onchange = function() {
            const boxes = document.querySelectorAll('.cart-item-checkbox');
            boxes.forEach(b => b.checked = this.checked);
            updateCartTotal();
        }
    }
    updateCartTotal();
}

function changeCartQty(index, delta) {
    let cartItems = [];
    const cartStr = localStorage.getItem(getCartKey());
    if(cartStr) {
        cartItems = JSON.parse(cartStr);
    }
    
    if(cartItems[index]) {
        let newQty = parseInt(cartItems[index].quantity) + delta;
        if(newQty > 0) {
            cartItems[index].quantity = newQty;
            localStorage.setItem(getCartKey(), JSON.stringify(cartItems));
            updateCartIcon();
            
            const checkboxes = document.querySelectorAll('.cart-item-checkbox');
            const isChecked = checkboxes[index] && checkboxes[index].checked;
            
            renderCart();
            
            if(isChecked) {
                setTimeout(() => {
                    const newCheckboxes = document.querySelectorAll('.cart-item-checkbox');
                    if(newCheckboxes[index]) newCheckboxes[index].checked = true;
                    updateCartTotal();
                }, 10);
            }
        }
    }
}

function removeFromCart(index) {
    if(confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
        let cartItems = [];
        const cartStr = localStorage.getItem(getCartKey());
        if(cartStr) {
            cartItems = JSON.parse(cartStr);
        }
        
        cartItems.splice(index, 1);
        localStorage.setItem(getCartKey(), JSON.stringify(cartItems));
        updateCartIcon();
        renderCart();
    }
}

function updateCartTotal() {
    let cartItems = [];
    const cartStr = localStorage.getItem(getCartKey());
    if(cartStr) cartItems = JSON.parse(cartStr);

    let total = 0;
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    let allChecked = checkboxes.length > 0;
    
    checkboxes.forEach((cb) => {
        if(cb.checked) {
            const index = cb.getAttribute('data-index');
            const item = cartItems[index];
            if(item) {
                total += item.price * item.quantity;
            }
        } else {
            allChecked = false;
        }
    });

    const selectAll = document.getElementById('cart-select-all');
    if(selectAll) selectAll.checked = allChecked;

    const totalPriceElem = document.getElementById('cart-total-price');
    if(totalPriceElem) {
        totalPriceElem.textContent = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(total);
    }
}

function checkoutCart() {
    let cartItems = [];
    const cartStr = localStorage.getItem(getCartKey());
    if(cartStr) cartItems = JSON.parse(cartStr);

    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    let selectedItems = [];
    
    checkboxes.forEach((cb) => {
        if(cb.checked) {
            const index = cb.getAttribute('data-index');
            if(cartItems[index]) {
                selectedItems.push(cartItems[index]);
            }
        }
    });

    if(selectedItems.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng!');
        return;
    }

    const checkoutData = {
        type: 'cart',
        items: selectedItems
    };
    
    sessionStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    window.location.href = 'thanhtoan.html';
}
