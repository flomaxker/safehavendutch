# Contact & Payment Flow

This document summarizes how the contact form and payment checkout are implemented after the 2025 refactoring.

## Contact Form

- `contact-handler.php` now uses **PHPMailer** instead of the `mail()` function.
- HTML formatting is constructed directly in the handler and sent via PHPMailer's
  `isHTML(true)` mode.
- The handler expects a POST request with `name`, `email`, and `message` fields
  and returns a JSON response indicating success or validation errors.
- Sender and recipient addresses are configured at the top of the file. The
  Reply-To header is populated with the form submitter's email so replies go
  directly back to them.

## PaymentHandler

 - `app/Payment/PaymentHandler.php` no longer creates its own dependencies. Instead it
  receives a PDO connection, `Package` model, `Purchase` model and a
  `\Stripe\StripeClient` instance via the constructor.
- `createCheckoutSession()` builds a Stripe checkout session and records a
  corresponding `purchases` row with status `pending`.
- `handleWebhook()` processes `checkout.session.completed` events, marking the
  purchase `completed` and adding package credits to the user.

Helper scripts like `checkout.php` and `stripe_webhook.php` instantiate the
handler with the required objects and pass incoming requests directly to it.
