# Project name

🌍 **Languages:**  
[English](README.md) | [Česky](README.cs.md)

## Support this project

This project is free and open-source and will always remain so.

If it helps you save time or ship faster, you can support ongoing maintenance via **GitHub Sponsors**:

➡️ https://github.com/sponsors/fefrik

Thank you — even a small contribution keeps the project going! 🚀

> **Disclaimer:** This is a community-maintained integration and not an official CodeWholesale product.  
> You must use your own CodeWholesale API key and account.

# CodesWholesale API – PHP SDK

PHP SDK for working with the **CodesWholesale API**  
(products, orders, license keys, synchronization, security).

- ✅ PHP **7.4+**
- ✅ No framework required
- ✅ Automatic OAuth authentication
- ✅ Safe pagination (resume using continuation token)
- ✅ Designed for long-running syncs and cron jobs

---

## Contents
1. Installation
2. Basic configuration
3. SDK feature overview
4. Products
5. Product synchronization (real-world timing)
6. Orders
7. License keys (Codes)
8. Account
9. Security
10. Static reference data
11. Best practices

---

## 1) Installation

```bash
composer require your-vendor/codeswholesale-api
```

---

## 2) Basic configuration

```php
use CodesWholesaleApi\Api\Client;
use CodesWholesaleApi\Auth\TokenNormalizer;
use CodesWholesaleApi\Config\Config;
use CodesWholesaleApi\Storage\FileStorage;

$config = new Config([]);

$oauthStorage = new FileStorage(__DIR__ . '/oauth_token.json');

$client = new Client(
    $config,
    $oauthStorage,
    'CLIENT_ID',
    'CLIENT_SECRET',
    new TokenNormalizer()
);
```

OAuth token handling is fully automatic:
- token is requested when needed
- token is refreshed automatically
- token is persisted in storage

---

## 3) SDK feature overview

### Products
- `Product::getAll()`
- `Product::getAllWithContinuationStorage()`
- `Product::getById()`

### Orders
- `Orders::getAll()`
- `Orders::getById()`
- `Orders::create()`

### Codes
- `Codes::getById()`

### Account
- `Account::getCurrent()`

### Security
- `Security::check()`

### Static reference data
- `Platforms::getAll()`
- `Regions::getAll()`
- `Languages::getAll()`
- `Territory::getAll()`

---

## 4) Products

### Fetch all products

```php
use CodesWholesaleApi\Resource\Product;

Product::getAll($client, function (array $items) {
    foreach ($items as $row) {
        echo $row['productId'] . PHP_EOL;
    }
});
```

---

### Fetch products with continuation token (resume-safe)

Recommended for long-running jobs or cron-based syncs.

```php
use CodesWholesaleApi\Resource\Product;
use CodesWholesaleApi\Storage\FileContinuationTokenStorage;

$filters = [
    'updatedSince' => '2025-12-20T00:00:00Z',
];

$key = md5(json_encode($filters));

Product::configureContinuationTokenStorage(
    new FileContinuationTokenStorage(__DIR__ . "/products_token_{$key}.txt")
);

Product::getAllWithContinuationStorage(
    $client,
    function (array $items, ?string $nextToken) {
        foreach ($items as $row) {
            // process product
        }
    },
    $filters
);
```

How it works:
- `continuationToken` is a technical cursor
- token is saved after each successfully processed page
- on crash, the next run resumes automatically
- when the last page is reached, token is cleared

---

## 5) Product synchronization (real-world timing)

Recommended sync model:

| Element | Purpose |
|------|--------|
| `updatedSince` | Logical checkpoint (time) |
| `continuationToken` | Technical cursor (pagination resume) |

Typical setup:
- cron job every X minutes
- `updatedSince = lastSyncAt`
- continuation token only for crash recovery

---

## 6) Orders

### Fetch order history

```php
use CodesWholesaleApi\Resource\Orders;

$orders = Orders::getAll(
    $client,
    '2025-12-01T00:00:00Z',
    '2025-12-31T23:59:59Z'
);

foreach ($orders as $o) {
    echo $o->getOrderId() . ' ' . $o->getStatus() . PHP_EOL;
}
```

---

### Fetch order details

```php
$order = Orders::getById($client, 'ORDER_ID');

foreach ($order->getProducts() as $product) {
    foreach ($product->getCodes() as $code) {
        echo $code->getCode() . PHP_EOL;
    }
}
```

---

### Create order

```php
$order = Orders::create($client, [
    'allowPreOrder' => true,
    'orderId' => 'MY-ORDER-123',
    'products' => [
        [
            'productId' => 'PRODUCT_ID',
            'quantity' => 1,
            'price' => 9.99,
        ],
    ],
]);
```

---

### Create order and fetch license keys

```php
use CodesWholesaleApi\Service\OrderFulfillmentService;

$service = new OrderFulfillmentService($client);

$result = $service->createAndFetchCodes($orderRequest, true);

foreach ($result['codes'] as $code) {
    echo $code->getCode();
}
```

---

## 7) License keys (Codes)

```php
use CodesWholesaleApi\Resource\Codes;

$code = Codes::getById($client, 'CODE_ID');
echo $code->getCode();
```

---

## 8) Account

```php
use CodesWholesaleApi\Resource\Account;

$account = Account::getCurrent($client);
var_export($account->toArray());
```

---

## 9) Security

```php
use CodesWholesaleApi\Resource\Security;

$result = Security::check($client, [
    'customerEmail' => 'customer@example.com',
    'customerIpAddress' => '203.0.113.10',
]);

echo $result->getRiskScore();
```

---

## 10) Static reference data

```php
Platforms::getAll($client);
Regions::getAll($client);
Languages::getAll($client);
Territory::getAll($client);
```

These endpoints are ideal for caching (they change rarely).

---

## 11) Best practices

- ❌ Do not use `continuationToken` as a business filter
- ✅ Save continuation token only after successful page processing
- ❌ Do not mix OAuth token storage with continuation token storage
- ✅ Use separate continuation storage per filter set

---

## Conclusion

This SDK is designed for:
- production e-commerce workflows
- long-running and resumable jobs
- safe and consistent data synchronization

If you need:
- extended documentation
- flow diagrams
- CLI tooling examples

feel free to extend this SDK or documentation as needed.


