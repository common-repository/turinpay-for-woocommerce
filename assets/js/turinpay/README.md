 [![NPM](https://img.shields.io/npm/v/@turinlabs/turinpay)](https://www.npmjs.com/package/@turinlabs/turinpay)

### 🏠 [Homepage](https://turinpay.com)
TurinPay provides a powerful web component (button) that supports bitcoin lightning network and standard on-chain operations with action events associated allowing you to receive and send microtransactions instantly.

## Install

The `<turinpay-button>` web component can be installed from [NPM](https://npmjs.org):

```sh
npm i @turinlabs/turinpay
```

It can also be used directly from various free CDNs such as [unpkg.com](https://unpkg.com):

```html
<script type="module" src="https://unpkg.com/@turinlabs/turinpay@[latest|version]/build/index.js"></script>
```

For more detailed usage documentation and live examples, please visit our docs
at [API Docs](https://turinlabs.gitbook.io/turinlabs/api-docs).

## How to use the imported html tag?

Simply by using the `<turinpay-button>` tag with the properly configured properties.

A **payment intent** must be request to our API to get the `paymentIntent` property to use in `<turinpay-button>`.

The properties in `<turinpay-button>`:

* paymentIntent [`<uuid>`]: **Required** the identifier registered on the platform that identifies the payment intent
* size ['big'|'normal'|'small']: Button size to be displayed. Default 'normal'.
* titleText [`<string>`]: Text added to the title of the button. Default 'Pay with TurinPay'
* paidText [`<string>`]: Text added to the title of the button when it is already paid. Default 'Payment completed!!'
* timeout [`<millis>`]: Waiting time to close the mode after payment has been made. Default 3000
* env ['pro'|'sta'|'int']: Environment to use. Field only for turinlabs's developers. Use with caution. Default 'pro'

```html
  <turinpay-button
    size='small'
    paymentIntent="<uuid>"
    paidText='¡Paid 🚀!'
  />
```

## Button Customization (CSS)

You can customize the look of the button by assigning values to the CSS variables.

```css
  button {
    font-size: var( --pb-button-size, 1rem );
    font-weight: var( --pb-button-weight, 400 );
    background-color: var( --pb-button-background, transparent );
    color: var( --pb-button-color, #0c8588 );
    border: var( --pb-button-border, 1px solid #0c8588 );
    border-radius: var( --pb-button-border-radius, 12px );
    width: var( --pb-button-width, auto );
    height: var( --pb-button-height, auto );
  }
  button.paid {
    cursor: not-allowed;
    background-color: var( --pb-button-background-paid, #f0f0f0);
  }
  button:hover {
    font-size: var( --pb-button-hover-size, 1rem );
    font-weight: var( --pb-button-hover-weight, 400 );
    background-color: var( --pb-button-hover-background, #f0f0f0 );
    color: var( --pb-button-hover-color, #fff );
  }
```

## Frontend event

By listening to the `paid` event you can schedule actions, while the purchase is confirmed through an IPN from server

```js
  (function () {
    var elem = document.querySelector('#pay-button-id');

    elem.addEventListener('paid', function (elem) {
        // Layout changes...?
    }, false);
  })()
```

## Author

👤 **@turinpay**

* Website: [https://www.turinpay.com](https://www.turinpay.com)
* Docs: [https://turinlabs.gitbook.io/turinlabs/api-docs](https://turinlabs.gitbook.io/turinlabs/api-docs)

## 📝 License

Copyright © 2022 [TURINPAY](https://www.turinpay.com).

