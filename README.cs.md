# Project name

🌍 **Jazyky:**  
[English](README.md) | [Česky](README.cs.md)

## Podpořte tento projekt

Tento projekt je zdarma a open-source a takový vždy zůstane.

Pokud vám pomáhá šetřit čas nebo dodávat rychleji, můžete podpořit jeho další vývoj a údržbu prostřednictvím GitHub Sponsors:

➡️ https://github.com/sponsors/fefrik

Děkuji — i malý příspěvek pomáhá udržet projekt při životě! 🚀

> **Upozornění:** Jedná se o komunitně udržovanou integraci, nikoli o oficiální produkt společnosti CodeWholesale  
> Musíte používat vlastní CodeWholesale API klíč a vlastní účet.

# CodesWholesale API – PHP SDK

PHP SDK pro práci s **CodesWholesale API**  
(produkty, objednávky, licenční klíče, synchronizace, bezpečnost).

- ✅ PHP **7.4+**
- ✅ bez frameworků
- ✅ automatická OAuth autentizace
- ✅ bezpečná paginace (resume pomocí continuation tokenu)
- ✅ připravené pro dlouhé synchronizace a cron běhy

---

## Obsah
1. Instalace
2. Základní konfigurace
3. Přehled funkcí SDK
4. Produkty
5. Synchronizace produktů (reálné časy)
6. Objednávky
7. Licenční klíče (Codes)
8. Account
9. Bezpečnost (Security)
10. Statické seznamy
11. Best practices

---

## 1) Instalace

```bash
composer require your-vendor/codeswholesale-api
```

---

## 2) Základní konfigurace

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

---

## 3) Přehled funkcí SDK

### Produkty
- Product::getAll()
- Product::getAllWithContinuationStorage()
- Product::getById()

### Objednávky
- Orders::getAll()
- Orders::getById()
- Orders::create()

### Kódy
- Codes::getById()

### Account
- Account::getCurrent()

### Bezpečnost
- Security::check()

### Statické seznamy
- Platforms::getAll()
- Regions::getAll()
- Languages::getAll()
- Territory::getAll()

---

## 4) Produkty

### Načtení všech produktů

```php
Product::getAll($client, function (array $items) {
    foreach ($items as $row) {
        echo $row['productId'] . PHP_EOL;
    }
});
```

---

## 5) Synchronizace produktů

Používej `updatedSince` + `continuationToken`.

Doporučeno spouštět přes cron.

---

## 6) Objednávky

### Vytvoření objednávky

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

## 7) Licenční klíče

```php
$code = Codes::getById($client, 'CODE_ID');
echo $code->getCode();
```

---

## 8) Account

```php
$account = Account::getCurrent($client);
var_export($account->toArray());
```

---

## 9) Bezpečnost

```php
$result = Security::check($client, [
    'customerEmail' => 'customer@example.com',
]);
```

---

## 10) Statické seznamy

```php
Platforms::getAll($client);
Regions::getAll($client);
Languages::getAll($client);
Territory::getAll($client);
```

---

## 11) Best practices

- continuationToken ≠ business filtr
- ukládej token až po zpracování stránky
- odděluj OAuth token a continuation token

---

## Závěr

SDK je navrženo pro produkční e‑commerce použití.

