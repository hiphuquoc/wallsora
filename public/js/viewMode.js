// Lấy chế độ xem từ localStorage và áp dụng class vào thẻ <html> => đặt trên head và gọi ngay (gọi bất đồng bộ)
(function applyViewMode() {
    // Lấy chế độ xem từ localStorage
    const savedViewMode = localStorage.getItem('viewMode') || "light"; // Mặc định là light

    // Cập nhật class của thẻ <html>
    document.documentElement.className = savedViewMode;
})();