# StakeCube-Node-SDK
Unofficial StakeCube API SDK for PHP - Available on Composer!

## Install
Composer: `composer require weoka/stakecube-php-sdk`

...or clone via git and import the module yourself!

## Setup
To start using the StakeCube SDK, you'll need to import the Stakecube class and initialice an instance of it with your public and private key as parameters. If you only intend to use public endpoints, you can just send random string as parameters.

If you don't have your pair keys, grab them from your [Profile](https://stakecube.net/app/profile/api-keys)

```php
<?php

require_once('vendor/autoload.php');

use Stakecube\Stakecube;

$public_key = "your public key here";
$private_key = "your private key here":

$stakecube = new Stakecube($public_key , $private_key);

var_dump($stakecube->getArbitrageInfo());
```

## Usage

### Note: If you're looking for Advanced REST API documentation, please use [this document](https://github.com/stakecube/DevCube/tree/master/rest-api) from DevCube!

---

### Get Arbitrage Info
> Gets arbitrage information for a chosen coin ticker.
- Method: `getArbitrageInfo($ticker);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) ticker | the ticker of a coin | DOGEC

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getArbitrageInfo('DOGEC');
// result: { 'coingecko-provided market info array' }
```

---

### Get Markets
> Gets a list of all StakeCube markets under the chosen base market, optionally sorted by `volume` or `change`, but by default sorted alphabetically.
- Method: `getMarkets($base, $orderBy);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) base | the chosen base coin | DOGEC
(optional) orderBy | the ordering of the list | `volume` or `change`

Example:
```js
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getMarkets('DOGEC', 'volume');
// result: { DOGEC_BTC: {}, DOGEC_DASH: {} ... }
```

---

### Get OHLC Data
> Gets an array of the last 500 candles for the chosen market pair and interval.
- Method: `getOhlcData($market, $interval);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) market | the chosen market pair | DOGEC_BTC
(required) interval | the per-candle timeframe / period | `1m`, `5m`, `15m`, `30m`, `1h`, `4h`, `1d`, `1w`, `1mo`

Example:
```PHP
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getOhlcData('DOGEC_BTC', '1d');
// result: { depth: { asks: [], bids: []}, lines: [], trades: [] }
```

---

### Get MineCube Info
> Gets the current real-time info for MineCube, such as total and available workers, the price of workers, and the payouts-in-progress status.
- Method: `getMineCubeInfo();`

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getMineCubeInfo();
// result: { totalWorker: 123, workerAvailable: 100, ... }
```

---

### Get MineCube Miners
> Gets a list of all Miners belonging to MineCube, you may optionally specify a coin to see miners for only that coin, such as 'ETH' which uses AMD GPUs, or 'DASH' which uses StrongU ASICs.
- Method: `getMineCubeMiners($coin);`

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getMineCubeMiners();
// result: { BTC: { minerCount: 200, miner: [ ... ], ... }, DASH: ...  }
```

---

### Get Rate Limits
> Gets the current global StakeCube rate-limits for APIs.
- Method: `getRatelimits();`

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getRatelimits();
// result: [ { rate_limit_type: "REQUEST_WEIGHT", interval: "DAY" ... } ... ]
```

---

### Get Trades
> Returns the last trades of a specified market pair, optionally with a custom results limit.

- Method: `getTrades($market);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) market | the chosen market pair | DOGEC_BTC
(optional) limit | the maximum returned trades | 100

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getTrades("DOGEC_BTC");
// result: [ { direction: "BUY", amount: "1.23", price: ... } ... ]
```

---

### Get Orderbook
> Gets the orderbook of a chosen market, optionally a specified side, but by default will load both orderbook sides.
- Method: `getOrderbook($market);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) market | the chosen market pair | DOGEC_BTC
(optional) side | the orderbook side | `buy` or `sell`

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getOrderbook("DOGEC_BTC");
// result: { asks: [], bids: [] }
```
---

## Private APIs

These APIs require you to be using real API keys with sufficient permissions to perform the private action. For example, withdrawals will work on any key with the Withdrawals Permission enabled, order placing/cancelling will work on a key with "Full Permissions", but will not work on a "Read only" key, be aware of this and customize your keys accordingly for security!

### Get Account (Auth Required)
> Returns general information about your StakeCube account, including wallets, balances, fee-rate in percentage and your account username.
- Method: `getAccount();`

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getAccount();
// result: { user: "weoka", exchangeFee: 0.1, wallets: [ ... ], ... }
```

---

### Withdraw (Auth Required)
> Creates a withdrawal request with a specified coin, address and amount.
- Method: `withdraw($ticker, $address, $amount);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) ticker | the withdrawal coin | DOGEC
(required) address | the withdrawal address | dWdSgX...
(required) amount | the withdrawal amount | 100

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->withdraw("DOGEC", "dWdSgX...", 100);
// result: { success: true }
```

---

### Get Open Orders (Auth Required)
> Returns a list of all open orders for all StakeCube Exchange markets.
- Method: `getOpenOrders();`


Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getOpenOrders();
// result: [ { market: "DOGEC_BTC", id: 123, side: "BUY", ... }, ... ]
```

---

### Get My Trades (Auth Required)
> Returns a list of all trades, you can leave the market empty ("") to return all trades, or specify a market such as "DOGEC_BTC" to return those market orders, you may also specify a limit of the amount of returned trades, of which the default is 100 trades.
- Method: `getMyTrades($market, $limit);`

Parameter | Description | Example
------------ | ------------- | -------------
(optional) market | the market pair | DOGEC_BTC
(optional) limit | the maximum returned trades | 100

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getMyTrades();
// result: [ { market: "DOGEC_BTC", id: 123, direction: "BUY", ... }, ... ]
```

---

### Get Order History (Auth Required)
> Returns a list of all orders from a specified market such as "DOGEC_BTC", you may also specify a limit of the amount of returned orders, of which the default is 100 orders.
- Method: `getOrderHistory($market, $limit);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) market | the market pair | DOGEC_BTC
(optional) limit | the maximum returned trades | 100

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->getOrderHistory("DOGEC_BTC");
// result: [ { market: "DOGEC_BTC", type: "MARKET", side: "BUY", ... }, ... ]
```

---

### Post Order (Auth Required)
> Creates an exchange order for the specified market pair, orderbook side, price and amount.
- Method: `postOrder($market, $side, $price, $amount);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) market | the market pair | DOGEC_BTC
(required) side | the trade side | BUY
(required) price | the price in the base coin | 0.00002000 (BTC)
(required) amount | the amount of the trade coin | 1000 (DOGEC)

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->postOrder("DOGEC_BTC", "BUY", 0.00002000, 1000);
// result: { orderId: 123, executedAmount: 0, fills: [], ... }
```

---

### Cancel Order (Auth Required)
> Cancels an open order by it's orderId
- Method: `cancel($orderId);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) orderId | the ID of your order | 123

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->cancel(123);
// result: { originalAmount: 1000, executedAmount: 0, canceledAmount: 0.02, ... }
```

---

### Cancel All (Auth Required)
> Cancels all orders in a specified market pair.
- Method: `cancelAll($market);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) market | the market pair | DOGEC_BTC

Example:
```php
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->cancelAll("DOGEC_BTC");
// result: [ { originalAmount: 1000, executedAmount: 0, canceledAmount: 0.02, ... }, ... ]
```

---

### Set MineCube Payout Coin (Auth Required)
> Sets a coin as the preferred payout coin to receive upon future MineCube payouts.
- Method: `setMineCubePayoutCoin($coin);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) coin | the MineCube payout coin | DOGE

Example:
```js
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->setMineCubePayoutCoin("DOGE");
// result: { success: true, ... }
```

---

### Buy MineCube Workers (Auth Required)
> Buys a specified amount of MineCube workers using the chosen payment method.
- Method: `buyMineCubeWorkers($method, $workers);`

Parameter | Description | Example
------------ | ------------- | -------------
(required) method | the payment method | `SCC` or `CREDITS`
(required) workers | the worker quantity | 10

Example:
```js
$stakecube = new Stakecube($public_key , $private_key);
$stakecube->buyMineCubeWorkers("SCC", 10);
// result: { success: true, ... }
```
