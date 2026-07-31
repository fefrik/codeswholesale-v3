🌍 **Languages:**  
[English](README.md) | [Česky](README.cs.md)

# CodesWholesale PHP SDK

PHP SDK for working with the **CodesWholesale API v3**
(products, orders, license keys, synchronization, security).

Designed for real-world e‑commerce integrations and long-running background jobs.

✅ PHP 7.4+  
✅ No framework required  
✅ Automatic OAuth authentication  
✅ Safe pagination (resume using continuation token)  
✅ Designed for long-running syncs and cron jobs

---

## Support This Project ❤️

This project is **free and open-source** and will always remain so.

If it helps you save time or ship faster, you can support ongoing maintenance via GitHub Sponsors:

➡️ https://github.com/sponsors/fefrik

Thank you — even a small contribution keeps the project going! 🚀

---

## Requirements

- PHP **7.4+**
- **cURL** extension
- **JSON** extension

---

## Installation

```bash
composer require codeswholesale-v3/sdk
```

---

## Basic Usage

### Creating the Client and SDK

```php
use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Sdk\Sdk;
use CodesWholesaleApi\Storage\OAuth2\TokenSessionOAuthStorage;

$oauthStorage = new TokenSessionOAuthStorage();

$client = new Client(
    Config::live(),
    $oauthStorage,
    'CLIENT_ID',
    'CLIENT_SECRET'
);

$sdk = new Sdk($client);
```

---

## Architecture Overview

```
Client
 └── Endpoint (Products, Orders, Codes, …)
       └── Resource (ProductItem, OrderItem, …)
```

### Client
- Handles HTTP communication, OAuth2, retries, and errors
- **Always returns `stdClass`**

### Endpoint
- Represents a REST API group (`/v3/products`, `/v3/orders`, …)
- Converts responses into **Resource objects**

### Resource
- Immutable DTO (read‑only)
- Typed getters
- No business logic

---

## SDK Contents (by API area)

### Products
- List products (paged, resumable)
- Fetch product details
- Fetch product descriptions
- Fetch product images
- Safe synchronization for large catalogs (50k+ products)

### Orders
- Create orders
- Fetch order history
- Fetch order details
- Extract license keys from completed orders

### Codes (License Keys)
- Fetch ordered license keys
- Download text or image-based codes
- Base64 image handling

### Account
- Fetch account balance
- Fetch account details

### Security
- Fraud / risk checks
- IP and domain reputation
- Risk score evaluation

### Metadata
- Platforms
- Regions
- Languages
- Territories

---

## Products

### Fetching a single page of products

```php
$page = $sdk->products()->getPage([
    'updatedSince' => '2024-01-01T00:00:00Z'
]);

foreach ($page['items'] as $product) {
    echo $product->getName();
}
```

---

## Iterating over all products

```php
$sdk->products()->getAll(
    function (array $items) {
        foreach ($items as $product) {
            saveProduct($product);
        }
    }
);
```

---

## Product Synchronization (recommended)

Safe and resumable synchronization using continuation tokens.

```php
$runner->runForSeconds(
    fn(ProductItem $p) => upsertProduct($p),
    30
);
```

✔ Safe for web requests  
✔ Safe for cron jobs  
✔ Continues exactly where it stopped

---

## Disclaimer

This is a **community-maintained integration** and **not an official CodesWholesale product**.

You must use your **own CodesWholesale API key and account**.
All trademarks belong to their respective owners.
