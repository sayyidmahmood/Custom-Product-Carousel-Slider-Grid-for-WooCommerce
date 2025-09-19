# 🛒 Custom Product Grid Slider for WooCommerce

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg)](https://wordpress.org/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-7.0%2B-purple.svg)](https://woocommerce.com/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-orange.svg)](https://www.php.net/)

A lightweight and customizable **WooCommerce product grid + slider plugin** for WordPress.  
It displays products in a **responsive carousel** with brand logos, descriptions, prices, add-to-cart buttons, and a “Learn More” link.  

---

## ✨ Features

- 🔥 **Responsive product slider** powered by [Slick Carousel](https://kenwheeler.github.io/slick/).  
- 🎨 Clean **grid layout** and **carousel layout** options.  
- 🏷️ Custom **brand logo field** in WooCommerce product editor.  
- 📝 Displays product:  
  - Featured image  
  - Brand logo  
  - Title  
  - Short description  
  - Price  
  - Add to Cart button  
  - Learn More → Product page link  
- 🎯 Custom navigation arrows (styled, no Font Awesome required).  
- ⚡ Easy integration with a **shortcode**.  

---

## 🚀 Installation

1. Download or clone this repository.  
2. Upload the folder to `/wp-content/plugins/custom-product-grid-slider`.  
3. Activate the plugin from **WordPress Admin → Plugins**.  

---

## 🛠️ Usage

Use the shortcode in your posts, pages, or templates:

### 🔲 1. Default Grid Layout
```php
[product_grid layout="grid" columns="3" per_page="12" category="your-category-slug"]

### 🔲 2. Carousel Grid Layout
```php
[product_grid layout="slider" columns="3" per_page="12" category="your-category-slug"]
