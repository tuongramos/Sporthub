
const API_BASE = window.location.origin + '/Sporhub/api/public/api';

// Utilities
function getToken() {
    return localStorage.getItem('token') || ''; 
}

function getHeaders() {
    return {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${getToken()}`
    };
}

// UI LOGIC (Tabs & Modals)
document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initModals();
    initAdminMobileSidebar();
    
    loadDashboardStats();
    loadUsers();
    loadCategories();

    document.getElementById('logout-btn').addEventListener('click', (e) => {
        e.preventDefault();
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        window.location.href = 'taikhoan.html';
    });
    
    document.getElementById('btn-back-categories').addEventListener('click', () => {
        document.getElementById('view-products').style.display = 'none';
        document.getElementById('view-categories').style.display = 'block';
        currentCategoryId = null;
        searchKeyword = '';
        const searchInput = document.getElementById('admin-search-input');
        if (searchInput) searchInput.value = '';
    });

    initAdminSearch();
});

function initAdminMobileSidebar() {
    const toggleBtn = document.getElementById('admin-sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    
    if (!toggleBtn || !sidebar || !overlay) return;
    
    const openSidebar = () => {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    };
    
    const closeSidebar = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    };
    
    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    });
    
    overlay.addEventListener('click', closeSidebar);
    
    // Tự động đóng sidebar khi bấm chọn tab trên màn hình nhỏ
    document.querySelectorAll('.sidebar-nav .nav-item').forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth <= 992) {
                closeSidebar();
            }
        });
    });
}

// State
let currentCategoryId = null;
let currentProductId = null;
let searchKeyword = '';

function initAdminSearch() {
    const searchInput = document.getElementById('admin-search-input');
    if (!searchInput) {
        console.error('[AdminSearch] Không tìm thấy #admin-search-input!');
        return;
    }
    console.log('[AdminSearch] Đã gắn sự kiện keydown vào thanh tìm kiếm.');

    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchKeyword = searchInput.value.trim();
            console.log('[AdminSearch] Enter được nhấn. searchKeyword =', searchKeyword, '| currentCategoryId =', currentCategoryId);
            if (currentCategoryId !== null) {
                loadProducts(currentCategoryId);
            } else {
                console.warn('[AdminSearch] currentCategoryId là null - chưa vào danh mục nào!');
            }
        }
    });
}


function initTabs() {
    const navItems = document.querySelectorAll('.sidebar-nav .nav-item[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');

    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active classes
            navItems.forEach(nav => nav.classList.remove('active'));
            tabContents.forEach(tab => tab.classList.remove('active'));

            // Add active class to clicked tab
            item.classList.add('active');
            const targetId = item.getAttribute('data-tab');
            document.getElementById(targetId).classList.add('active');
            
            // Xóa giá trị ô tìm kiếm khi chuyển tab
            const searchInput = document.getElementById('admin-search-input');
            if (searchInput) searchInput.value = '';

            // Refresh data based on tab
            if (targetId === 'users-tab') loadUsers();
            if (targetId === 'products-tab') {
                document.getElementById('view-products').style.display = 'none';
                document.getElementById('view-categories').style.display = 'block';
                loadCategories();
            }
            if (targetId === 'dashboard-tab') loadDashboardStats();
            if (targetId === 'promotions-tab') loadPromotions();
        });
    });
}

function initModals() {
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.modal').forEach(m => m.classList.remove('show'));
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal')) {
            e.target.classList.remove('show');
        }
    });
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}
function hideModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

// DATA FETCHING & RENDERING
async function loadDashboardStats() {
    try {
        const [usersRes, productsRes] = await Promise.all([
            fetch(`${API_BASE}/users`, { headers: getHeaders() }),
            fetch(`${API_BASE}/products`, { headers: getHeaders() })
        ]);
        
        if (usersRes.ok && productsRes.ok) {
            const users = await usersRes.json();
            const products = await productsRes.json();
            document.getElementById('total-users').textContent = Array.isArray(users) ? users.length : (users.data ? users.data.length : 0);
            document.getElementById('total-products').textContent = Array.isArray(products) ? products.length : (products.data ? products.data.length : 0);
        }
    } catch (error) {
        console.error("Error loading stats:", error);
    }
}

// User
async function loadUsers() {
    try {
        const res = await fetch(`${API_BASE}/users`, { headers: getHeaders() });
        const data = await res.json();
        let users = Array.isArray(data) ? data : (data.data || []);
        users.sort((a, b) => parseInt(a.id) - parseInt(b.id));
        renderUsers(users);
    } catch (error) {
        console.error("Error loading users:", error);
        document.querySelector('#users-table tbody').innerHTML = '<tr><td colspan="6">Lỗi tải dữ liệu</td></tr>';
    }
}

function renderUsers(users) {
    const tbody = document.querySelector('#users-table tbody');
    tbody.innerHTML = '';
    
    users.forEach(user => {
        // Handle uppercase roles from DB
        const roleStr = user.role ? user.role.toUpperCase() : 'USER';
        const roleClass = roleStr === 'ADMIN' ? 'badge-admin' : 'badge-user';
        
        // Handle status
        const statusStr = user.status ? user.status.toLowerCase() : 'active';
        const statusClass = statusStr !== 'active' ? 'badge-banned' : 'badge-active';
        
        // Handle Name
        let fullName = (user.last_name || '') + ' ' + (user.first_name || '');
        if (!fullName.trim()) fullName = user.username || 'N/A';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${user.id}</td>
            <td>${fullName.trim()}</td>
            <td>${user.email || 'N/A'}</td>
            <td><span class="badge ${roleClass}">${roleStr}</span></td>
            <td><span class="badge ${statusClass}">${statusStr !== 'active' ? 'Đã khóa' : 'Hoạt động'}</span></td>
            <td>
                <div class="action-btns">
                    <button class="btn btn-sm btn-primary" onclick="openEditUserRole(${user.id}, '${roleStr}')" title="Đổi quyền">Sửa</button>
                    ${statusStr !== 'active'
                        ? `<button class="btn btn-sm btn-warning" onclick="toggleUserStatus(${user.id}, 'active')" title="Mở khóa">Mở</button>`
                        : `<button class="btn btn-sm btn-warning" onclick="toggleUserStatus(${user.id}, 'inactive')" title="Khóa tài khoản">Khóa</button>`
                    }
                    <button class="btn btn-sm btn-danger" onclick="deleteUser(${user.id})" title="Xóa">Xóa</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// User Actions
let currentUserEditId = null;
window.openEditUserRole = function(id, currentRole) {
    currentUserEditId = id;
    document.getElementById('user-role-select').value = currentRole;
    showModal('user-modal');
}

document.getElementById('btn-save-user-role').addEventListener('click', async () => {
    const newRole = document.getElementById('user-role-select').value;
    try {
        const res = await fetch(`${API_BASE}/users/${currentUserEditId}`, {
            method: 'PUT',
            headers: getHeaders(),
            body: JSON.stringify({ role: newRole })
        });
        if (res.ok) {
            hideModal('user-modal');
            loadUsers();
        } else {
            alert('Có lỗi xảy ra khi đổi quyền.');
        }
    } catch (error) {
        console.error(error);
    }
});

window.toggleUserStatus = async function(id, newStatus) {
    if(confirm(`Bạn có chắc chắn muốn ${newStatus === 'banned' ? 'khóa' : 'mở khóa'} tài khoản này?`)) {
        try {
            const res = await fetch(`${API_BASE}/users/${id}`, {
                method: 'PUT',
                headers: getHeaders(),
                body: JSON.stringify({ status: newStatus })
            });
            if (res.ok) loadUsers();
            else alert('Có lỗi xảy ra.');
        } catch (error) {
            console.error(error);
        }
    }
}

window.deleteUser = async function(id) {
    if(confirm('Bạn có chắc chắn muốn xóa tài khoản này? Hành động này không thể hoàn tác.')) {
        try {
            const res = await fetch(`${API_BASE}/users/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            });
            if (res.ok) loadUsers();
            else alert('Có lỗi xảy ra khi xóa.');
        } catch (error) {
            console.error(error);
        }
    }
}

//CATEGORIES
async function loadCategories() {
    try {
        const res = await fetch(`${API_BASE}/categories`, { headers: getHeaders() });
        const data = await res.json();
        let categories = Array.isArray(data) ? data : (data.data || []);
        categories.sort((a, b) => parseInt(a.id) - parseInt(b.id));
        renderCategories(categories);
    } catch (error) {
        console.error("Error loading categories:", error);
    }
}

function renderCategories(categories) {
    const grid = document.getElementById('category-grid');
    grid.innerHTML = '';
    
    categories.forEach(cat => {
        const imgUrl = cat.image_url || 'assets/images/logo.png';
        const card = document.createElement('div');
        card.className = 'category-card';
        card.innerHTML = `
            <img src="${imgUrl}" alt="icon">
            <h3>${cat.name}</h3>
            <button class="btn-edit-category" onclick="event.stopPropagation(); openEditCategory(${cat.id}, '${cat.name.replace(/'/g, "\\'")}', '${imgUrl}')">Sửa</button>
            <button class="btn-delete-category" onclick="event.stopPropagation(); deleteCategory(${cat.id})">Xóa</button>
        `;
        card.onclick = () => {
            currentCategoryId = cat.id;
            document.getElementById('current-category-title').textContent = `Sản phẩm thuộc: ${cat.name}`;
            document.getElementById('view-categories').style.display = 'none';
            document.getElementById('view-products').style.display = 'block';
            loadProducts(currentCategoryId);
        };
        grid.appendChild(card);
    });
}

// Category Actions
document.getElementById('btn-add-category').addEventListener('click', () => {
    document.getElementById('category-form').reset();
    document.getElementById('category-id').value = '';
    document.getElementById('category-modal-title').textContent = 'Thêm Danh Mục';
    showModal('category-modal');
});

window.openEditCategory = function(id, name, imgUrl) {
    document.getElementById('category-id').value = id;
    document.getElementById('category-name').value = name;
    document.getElementById('category-image').value = imgUrl;
    document.getElementById('category-modal-title').textContent = 'Sửa Danh Mục';
    showModal('category-modal');
}

document.getElementById('btn-save-category').addEventListener('click', async (e) => {
    e.preventDefault();
    const id = document.getElementById('category-id').value;
    const isEdit = !!id;
    const data = {
        name: document.getElementById('category-name').value,
        image_url: document.getElementById('category-image').value,
        status: document.getElementById('category-status').value,
        description: document.getElementById('category-desc').value
    };

    const url = isEdit ? `${API_BASE}/categories/${id}` : `${API_BASE}/categories`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, { method, headers: getHeaders(), body: JSON.stringify(data) });
        if(res.ok) {
            hideModal('category-modal');
            loadCategories();
        } else alert('Lỗi khi lưu danh mục');
    } catch(err) { console.error(err); }
});

window.deleteCategory = async function(id) {
    if(confirm('Xóa danh mục này?')) {
        try {
            const res = await fetch(`${API_BASE}/categories/${id}`, { method: 'DELETE', headers: getHeaders() });
            if (res.ok) loadCategories();
        } catch(err) { console.error(err); }
    }
}

//PRODUCTS
function removeVietnameseTone(str) {
    return str
        .replace(/[àáạảãâầấậẩẫăằắặẳẵ]/g, 'a')
        .replace(/[èéẹẻẽêềếệểễ]/g, 'e')
        .replace(/[ìíịỉĩ]/g, 'i')
        .replace(/[òóọỏõôồốộổỗơờớợởỡ]/g, 'o')
        .replace(/[ùúụủũưừứựửữ]/g, 'u')
        .replace(/[ỳýỵỷỹ]/g, 'y')
        .replace(/đ/g, 'd')
        .replace(/[ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ]/g, 'A')
        .replace(/[ÈÉẸẺẼÊỀẾỆỂỄ]/g, 'E')
        .replace(/[ÌÍỊỈĨ]/g, 'I')
        .replace(/[ÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ]/g, 'O')
        .replace(/[ÙÚỤỦŨƯỪỨỰỬỮ]/g, 'U')
        .replace(/[ỲÝỴỶỸ]/g, 'Y')
        .replace(/Đ/g, 'D');
}

async function loadProducts(categoryId) {
    try {
        // Luôn tải theo category_id từ API
        let url = `${API_BASE}/products`;
        if (categoryId) url += `?category_id=${categoryId}`;

        const res = await fetch(url, { headers: getHeaders() });
        const data = await res.json();
        let products = Array.isArray(data) ? data : (data.data || []);

        // Lọc theo từ khóa tìm kiếm phía client (hỗ trợ tiếng Việt có/không dấu)
        if (searchKeyword) {
            const kwNorm = removeVietnameseTone(searchKeyword).toLowerCase();
            products = products.filter(p => {
                const nameNorm = removeVietnameseTone(p.name || '').toLowerCase();
                const descNorm = removeVietnameseTone(p.description || '').toLowerCase();
                return nameNorm.includes(kwNorm) || descNorm.includes(kwNorm);
            });
        }

        products.sort((a, b) => parseInt(a.id) - parseInt(b.id));
        renderProducts(products);

        // Hiển thị thông báo nếu không tìm thấy kết quả
        if (products.length === 0 && searchKeyword) {
            document.querySelector('#products-table tbody').innerHTML =
                `<tr><td colspan="8" style="text-align:center; padding: 30px; color: #999;">Không tìm thấy sản phẩm nào với từ khóa "<strong>${searchKeyword}</strong>"</td></tr>`;
        }
    } catch (error) {
        console.error("Error loading products:", error);
        document.querySelector('#products-table tbody').innerHTML = '<tr><td colspan="8">Lỗi tải dữ liệu</td></tr>';
    }
}

function renderProducts(products) {
    const tbody = document.querySelector('#products-table tbody');
    tbody.innerHTML = '';
    
    products.forEach(p => {
        const price = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price || 0);
        const imgUrl = p.image_url || 'assets/images/logo.png';
        const statusClass = p.status === 'active' ? 'badge-active' : 'badge-banned';
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${p.id}</td>
            <td><img src="${imgUrl}" class="product-img-sm" alt="img"></td>
            <td>${p.name}</td>
            <td>${price}</td>
            <td>${p.category_id || 'N/A'}</td>
            <td><span class="badge ${statusClass}">${p.status === 'active' ? 'Active' : 'Inactive'}</span></td>
            <td>
                <button class="btn btn-sm btn-info" onclick="openManageVariants(${p.id}, '${p.name.replace(/'/g, "\\'")}')" title="Biến thể">Biến thể</button>
            </td>
            <td>
                <div class="action-btns">
                    <button class="btn btn-sm btn-primary" onclick='openEditProduct(${JSON.stringify(p).replace(/'/g, "&#39;")})' title="Sửa">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})" title="Xóa">Xóa</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Product Actions
// Gallery state
let galleryUrls = [];

function renderGalleryPreview() {
    const container = document.getElementById('gallery-preview');
    container.innerHTML = '';
    galleryUrls.forEach((url, index) => {
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position: relative; display: inline-block;';
        wrapper.innerHTML = `
            <img src="${url}" alt="Gallery ${index+1}" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
            <button type="button" onclick="removeGalleryImage(${index})" style="position: absolute; top: -6px; right: -6px; background: #e53935; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; cursor: pointer; line-height: 20px; padding: 0;">✕</button>
        `;
        container.appendChild(wrapper);
    });
    document.getElementById('product-gallery').value = JSON.stringify(galleryUrls);
}

window.removeGalleryImage = function(index) {
    galleryUrls.splice(index, 1);
    renderGalleryPreview();
}

document.getElementById('btn-add-product').addEventListener('click', () => {
    document.getElementById('product-modal-title').textContent = 'Thêm sản phẩm mới';
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
    
    // Clear gallery
    document.getElementById('product-image-preview').style.display = 'none';
    document.getElementById('upload-status').style.display = 'none';
    galleryUrls = [];
    renderGalleryPreview();
    document.getElementById('gallery-upload-status').style.display = 'none';
    document.getElementById('product-modal-title').textContent = 'Thêm Sản Phẩm';
    showModal('product-modal');
});

window.openEditProduct = function(product) {
    document.getElementById('product-id').value = product.id;
    document.getElementById('product-name').value = product.name || '';
    document.getElementById('product-price').value = product.price || '';
    document.getElementById('product-category').value = product.category_id || '';
    document.getElementById('product-image').value = product.image_url || '';
    document.getElementById('product-image-file').value = '';
    document.getElementById('upload-status').style.display = 'none';
    const previewImg = document.getElementById('product-image-preview');
    if (product.image_url) {
        previewImg.src = product.image_url;
        previewImg.style.display = 'block';
    } else {
        previewImg.src = '';
        previewImg.style.display = 'none';
    }
    // Load gallery
    galleryUrls = Array.isArray(product.gallery) ? [...product.gallery] : [];
    renderGalleryPreview();
    document.getElementById('product-gallery-file').value = '';
    document.getElementById('gallery-upload-status').style.display = 'none';
    
    document.getElementById('product-status').value = product.status || 'active';
    document.getElementById('product-desc').value = product.description || '';
    
    document.getElementById('product-modal-title').textContent = 'Cập Nhật Sản Phẩm';
    showModal('product-modal');
}

document.getElementById('product-image-file').addEventListener('change', async function(e) {
    const file = e.target.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('file', file);
    formData.append('upload_preset', 'sportshop');
    formData.append('cloud_name', 'ddqhrx3sc');

    const previewImg = document.getElementById('product-image-preview');
    const uploadStatus = document.getElementById('upload-status');
    
    if (uploadStatus) {
        uploadStatus.textContent = 'Đang tải ảnh lên Cloudinary, vui lòng đợi...';
        uploadStatus.style.display = 'block';
        uploadStatus.style.color = 'blue';
    }

    try {
        const res = await fetch('https://api.cloudinary.com/v1_1/ddqhrx3sc/image/upload', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.secure_url) {
            document.getElementById('product-image').value = data.secure_url;
            previewImg.src = data.secure_url;
            previewImg.style.display = 'block';
            if (uploadStatus) {
                uploadStatus.textContent = 'Tải ảnh thành công!';
                uploadStatus.style.color = 'green';
            }
        } else {
            const errorMsg = data.error ? data.error.message : 'Unknown error';
            if (uploadStatus) {
                uploadStatus.textContent = 'Lỗi: ' + errorMsg;
                uploadStatus.style.color = 'red';
            }
            alert('Lỗi khi tải ảnh lên Cloudinary: ' + errorMsg);
        }
    } catch (err) {
        console.error(err);
        if (uploadStatus) {
            uploadStatus.textContent = 'Lỗi kết nối!';
            uploadStatus.style.color = 'red';
        }
        alert('Lỗi kết nối khi tải ảnh: ' + err.message);
    }
});

// Gallery file upload handler
document.getElementById('product-gallery-file').addEventListener('change', async function(e) {
    const files = Array.from(e.target.files);
    if (!files.length) return;

    const remaining = 4 - galleryUrls.length;
    if (remaining <= 0) {
        alert('Đã đạt tối đa 4 ảnh phụ. Hãy xóa bớt ảnh cũ trước.');
        return;
    }
    const filesToUpload = files.slice(0, remaining);
    if (files.length > remaining) {
        alert(`Chỉ có thể thêm ${remaining} ảnh nữa. Đã chọn ${remaining} ảnh đầu tiên.`);
    }

    const statusEl = document.getElementById('gallery-upload-status');
    statusEl.style.display = 'block';
    statusEl.style.color = 'blue';

    for (let i = 0; i < filesToUpload.length; i++) {
        statusEl.textContent = `Đang tải ảnh ${i+1}/${filesToUpload.length} lên Cloudinary...`;
        const formData = new FormData();
        formData.append('file', filesToUpload[i]);
        formData.append('upload_preset', 'sportshop');
        formData.append('cloud_name', 'ddqhrx3sc');

        try {
            const res = await fetch('https://api.cloudinary.com/v1_1/ddqhrx3sc/image/upload', {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            if (data.secure_url) {
                galleryUrls.push(data.secure_url);
                renderGalleryPreview();
            } else {
                const errorMsg = data.error ? data.error.message : 'Unknown error';
                statusEl.textContent = 'Lỗi ảnh ' + (i+1) + ': ' + errorMsg;
                statusEl.style.color = 'red';
                return;
            }
        } catch (err) {
            console.error(err);
            statusEl.textContent = 'Lỗi kết nối khi tải ảnh ' + (i+1);
            statusEl.style.color = 'red';
            return;
        }
    }
    statusEl.textContent = 'Tải tất cả ảnh phụ thành công!';
    statusEl.style.color = 'green';
    this.value = ''; // reset file input
});

document.getElementById('btn-save-product').addEventListener('click', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('product-id').value;
    const isEdit = !!id;
    
    const productData = {
        name: document.getElementById('product-name').value,
        price: document.getElementById('product-price').value,
        category_id: document.getElementById('product-category').value,
        image_url: document.getElementById('product-image').value,
        gallery: JSON.stringify(galleryUrls),
        status: document.getElementById('product-status').value,
        description: document.getElementById('product-desc').value,
    };

    const url = isEdit ? `${API_BASE}/products/${id}` : `${API_BASE}/products`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method: method,
            headers: getHeaders(),
            body: JSON.stringify(productData)
        });
        
        if (res.ok) {
            hideModal('product-modal');
            loadProducts(currentCategoryId);
            if(!isEdit) loadDashboardStats();
        } else {
            alert('Có lỗi xảy ra khi lưu sản phẩm.');
        }
    } catch (error) {
        console.error(error);
        alert('Lỗi kết nối Server.');
    }
});

window.deleteProduct = async function(id) {
    if(confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        try {
            const res = await fetch(`${API_BASE}/products/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            });
            if (res.ok) {
                loadProducts(currentCategoryId);
                loadDashboardStats();
            } else {
                alert('Có lỗi xảy ra khi xóa.');
            }
        } catch (error) {
            console.error(error);
        }
    }
}

// --- VARIANTS ---
window.openManageVariants = function(productId, productName) {
    currentProductId = productId;
    document.getElementById('variant-modal-title').textContent = `Biến thể: ${productName}`;
    document.getElementById('variant-form').reset();
    document.getElementById('variant-id').value = '';
    document.getElementById('btn-cancel-variant').style.display = 'none';
    showModal('variant-modal');
    loadVariants();
}

async function loadVariants() {
    if(!currentProductId) return;
    try {
        const res = await fetch(`${API_BASE}/product-variants/product/${currentProductId}`, { headers: getHeaders() });
        const data = await res.json();
        let variants = Array.isArray(data) ? data : (data.data || []);
        variants.sort((a, b) => parseInt(a.id) - parseInt(b.id));
        renderVariants(variants);
    } catch(err) {
        console.error(err);
        document.querySelector('#variants-table tbody').innerHTML = '<tr><td colspan="5">Lỗi tải dữ liệu</td></tr>';
    }
}

function renderVariants(variants) {
    const tbody = document.querySelector('#variants-table tbody');
    tbody.innerHTML = '';
    
    variants.forEach(v => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${v.id}</td>
            <td>${v.size}</td>
            <td>${v.color}</td>
            <td>${v.stock_quantity}</td>
            <td>
                <div class="action-btns">
                    <button class="btn btn-sm btn-primary" onclick="editVariant(${v.id}, '${v.size}', '${v.color}', ${v.stock_quantity})" title="Sửa">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deleteVariant(${v.id})" title="Xóa">Xóa</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

window.editVariant = function(id, size, color, stock) {
    document.getElementById('variant-id').value = id;
    document.getElementById('variant-size').value = size;
    document.getElementById('variant-color').value = color;
    document.getElementById('variant-stock').value = stock;
    document.getElementById('btn-cancel-variant').style.display = 'inline-block';
}

document.getElementById('btn-cancel-variant').addEventListener('click', () => {
    document.getElementById('variant-form').reset();
    document.getElementById('variant-id').value = '';
    document.getElementById('btn-cancel-variant').style.display = 'none';
});

document.getElementById('variant-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('variant-id').value;
    const isEdit = !!id;
    
    const data = {
        product_id: currentProductId,
        size: document.getElementById('variant-size').value,
        color: document.getElementById('variant-color').value,
        stock_quantity: parseInt(document.getElementById('variant-stock').value) || 0
    };

    const url = isEdit ? `${API_BASE}/product-variants/${id}` : `${API_BASE}/product-variants`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, { method, headers: getHeaders(), body: JSON.stringify(data) });
        if(res.ok) {
            document.getElementById('variant-form').reset();
            document.getElementById('variant-id').value = '';
            document.getElementById('btn-cancel-variant').style.display = 'none';
            loadVariants();
        } else alert('Lỗi khi lưu biến thể');
    } catch(err) { console.error(err); }
});

window.deleteVariant = async function(id) {
    if(confirm('Xóa biến thể này?')) {
        try {
            const res = await fetch(`${API_BASE}/product-variants/${id}`, { method: 'DELETE', headers: getHeaders() });
            if (res.ok) loadVariants();
        } catch(err) { console.error(err); }
    }
}

//PROMOTIONS
async function loadPromotions() {
    try {
        const res = await fetch(`${API_BASE}/promotions`, { headers: getHeaders() });
        const data = await res.json();
        let promotions = Array.isArray(data) ? data : (data.data || []);
        promotions.sort((a, b) => parseInt(a.id) - parseInt(b.id));
        renderPromotions(promotions);
    } catch (error) {
        console.error("Error loading promotions:", error);
        document.querySelector('#promotions-table tbody').innerHTML = '<tr><td colspan="9">Lỗi tải dữ liệu</td></tr>';
    }
}

function renderPromotions(promotions) {
    const tbody = document.querySelector('#promotions-table tbody');
    tbody.innerHTML = '';
    
    promotions.forEach(p => {
        const statusClass = p.status === 'active' ? 'badge-active' : 'badge-banned';
        
        // Format dates
        const startDate = p.start_date ? new Date(p.start_date).toLocaleString('vi-VN') : 'N/A';
        const endDate = p.end_date ? new Date(p.end_date).toLocaleString('vi-VN') : 'N/A';
        
        // Format money
        const discountAmount = p.discount_amount ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.discount_amount) : '0';
        const minOrder = p.min_order_value ? new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.min_order_value) : '0';

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>#${p.id}</td>
            <td><strong>${p.code}</strong></td>
            <td>${p.discount_percent ? p.discount_percent + '%' : '0%'}</td>
            <td>${discountAmount}</td>
            <td>${minOrder}</td>
            <td>${startDate}</td>
            <td>${endDate}</td>
            <td><span class="badge ${statusClass}">${p.status === 'active' ? 'Active' : 'Inactive'}</span></td>
            <td>
                <div class="action-btns">
                    <button class="btn btn-sm btn-primary" onclick='openEditPromotion(${JSON.stringify(p).replace(/'/g, "&#39;")})' title="Sửa">Sửa</button>
                    <button class="btn btn-sm btn-danger" onclick="deletePromotion(${p.id})" title="Xóa">Xóa</button>
                </div>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Promotion Actions
document.getElementById('btn-add-promotion')?.addEventListener('click', () => {
    document.getElementById('promotion-form').reset();
    document.getElementById('promotion-id').value = '';
    document.getElementById('promotion-modal-title').textContent = 'Thêm Khuyến Mãi';
    showModal('promotion-modal');
});

window.openEditPromotion = function(promotion) {
    document.getElementById('promotion-id').value = promotion.id;
    document.getElementById('promotion-code').value = promotion.code || '';
    document.getElementById('promotion-discount-percent').value = promotion.discount_percent || '';
    document.getElementById('promotion-discount-amount').value = promotion.discount_amount || '';
    document.getElementById('promotion-min-order').value = promotion.min_order_value || '';
    
    if (promotion.start_date) {
        const d = new Date(promotion.start_date);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        document.getElementById('promotion-start').value = d.toISOString().slice(0, 16);
    } else {
        document.getElementById('promotion-start').value = '';
    }
    
    if (promotion.end_date) {
        const d = new Date(promotion.end_date);
        d.setMinutes(d.getMinutes() - d.getTimezoneOffset());
        document.getElementById('promotion-end').value = d.toISOString().slice(0, 16);
    } else {
        document.getElementById('promotion-end').value = '';
    }

    document.getElementById('promotion-status').value = promotion.status || 'active';
    
    document.getElementById('promotion-modal-title').textContent = 'Cập Nhật Khuyến Mãi';
    showModal('promotion-modal');
}

document.getElementById('btn-save-promotion')?.addEventListener('click', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('promotion-id').value;
    const isEdit = !!id;
    
    const data = {
        code: document.getElementById('promotion-code').value,
        discount_percent: document.getElementById('promotion-discount-percent').value ? parseFloat(document.getElementById('promotion-discount-percent').value) : null,
        discount_amount: document.getElementById('promotion-discount-amount').value ? parseFloat(document.getElementById('promotion-discount-amount').value) : null,
        min_order_value: document.getElementById('promotion-min-order').value ? parseFloat(document.getElementById('promotion-min-order').value) : 0,
        start_date: document.getElementById('promotion-start').value,
        end_date: document.getElementById('promotion-end').value,
        status: document.getElementById('promotion-status').value,
    };

    if (!data.code || !data.start_date || !data.end_date) {
        alert("Vui lòng điền đủ các trường bắt buộc (Mã, Ngày bắt đầu, Ngày kết thúc).");
        return;
    }

    const url = isEdit ? `${API_BASE}/promotions/${id}` : `${API_BASE}/promotions`;
    const method = isEdit ? 'PUT' : 'POST';

    try {
        const res = await fetch(url, {
            method: method,
            headers: getHeaders(),
            body: JSON.stringify(data)
        });
        
        if (res.ok) {
            hideModal('promotion-modal');
            loadPromotions();
        } else {
            const errorData = await res.json().catch(() => ({}));
            alert(errorData.message || 'Có lỗi xảy ra khi lưu khuyến mãi.');
        }
    } catch (error) {
        console.error(error);
        alert('Lỗi kết nối Server.');
    }
});

window.deletePromotion = async function(id) {
    if(confirm('Bạn có chắc chắn muốn xóa mã khuyến mãi này?')) {
        try {
            const res = await fetch(`${API_BASE}/promotions/${id}`, {
                method: 'DELETE',
                headers: getHeaders()
            });
            if (res.ok) {
                loadPromotions();
            } else {
                alert('Có lỗi xảy ra khi xóa.');
            }
        } catch (error) {
            console.error(error);
        }
    }
}
