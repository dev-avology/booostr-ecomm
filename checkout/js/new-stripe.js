"use strict";

document.addEventListener("DOMContentLoaded", async function () {
  console.log("Stripe payment script loaded");

  const publishableKey = document.getElementById("publishable_key")?.value;
  const clientSecret = document.getElementById("client_secret")?.value;
  const returnUrl = document.getElementById("order_success_url")?.value;
  const loader = document.getElementById("page-loader");
  const form = document.getElementById("payment-form");
  const payment_url = document.getElementById("process_order_url")?.value;
  const submitButton = document.getElementById("submit_btn");

  if (!publishableKey || !clientSecret) {
    console.error("Missing Stripe publishable key or client secret");
    return;
  }

  // Initialize Stripe and Elements
  const stripe = Stripe(publishableKey);
  const elements = stripe.elements({
    clientSecret,
    appearance: {} // optional custom appearance config
  });

  // Create payment element (tab layout)
  const paymentElement = elements.create("payment", {
    layout: { type: "accordion", defaultCollapsed: false }
  });
  paymentElement.mount("#payment_method_area");

  // Handle form submission
  form?.addEventListener("submit", async (e) => {
    e.preventDefault();
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.textContent = "SUBMITTING ORDER";
      submitButton.style.backgroundColor = "#4a4a4a";
      submitButton.style.cursor = "not-allowed";
      submitButton.style.borderColor = "#4a4a4a";
    }
    if (loader) loader.style.display = "flex";

    try {
      const { error, paymentIntent } = await stripe.confirmPayment({
        elements,
        confirmParams: {
          return_url: returnUrl,
        },
        redirect: "if_required", // prevents unnecessary redirect
      });

      if (error) {
        console.error("Payment error:", error.message);
        alert(error.message);
      }else if (paymentIntent?.status === "requires_capture") {
        console.log("Payment succeeded:", paymentIntent.id);
        window.location.href = payment_url;
      }
      else if (paymentIntent?.status === "succeeded") {
        console.log("Payment succeeded:", paymentIntent.id);
        window.location.href = returnUrl;
      }
    } catch (err) {
      console.error("Unexpected error:", err);
    } finally {
      if (loader) loader.style.display = "none";
    }
  });
});

