import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/sources/admin/style.scss',
        'resources/sources/main/home-first-view.scss',
        'resources/sources/main/home-non-first-view.scss',
        'resources/sources/main/category-money-first-view.scss',
        'resources/sources/main/category-money-non-first-view.scss',
        'resources/sources/main/product-first-view.scss',
        'resources/sources/main/product-non-first-view.scss',
        'resources/sources/main/cart-first-view.scss',
        'resources/sources/main/cart-non-first-view.scss',
        'resources/sources/main/page-first-view.scss',
        'resources/sources/main/page-non-first-view.scss',
        'resources/sources/main/category-free-first-view.scss',
        'resources/sources/main/category-free-non-first-view.scss',
        'resources/sources/main/freewallpaper-first-view.scss',
        'resources/sources/main/freewallpaper-non-first-view.scss',
        'resources/sources/main/confirm-first-view.scss',
        'resources/sources/main/confirm-non-first-view.scss',
        'resources/sources/main/category-blog-first-view.scss',
        'resources/sources/main/category-blog-non-first-view.scss',
      ],
      refresh: true,
    }),
  ],
  server: {
    watch: {
      usePolling: false, // Giúp Vite theo dõi thay đổi file
      interval: 1000, // Điều chỉnh thời gian polling
    },
    hmr: {
      overlay: false, // Vô hiệu hóa overlay để tránh lỗi khó chịu
    },
  },
  css: {
    devSourcemap: true, // Hỗ trợ sourcemap trong quá trình dev
  },
});
