<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function process(Booking $booking, Request $request)
    {
        try {
            // Validate payment details
            $validated = $request->validate([
                'payment_method' => 'required|string|in:card,cash',
                'payment_details' => 'required_if:payment_method,card|array',
                'payment_details.card_number' => 'required_if:payment_method,card|string',
                'payment_details.expiry' => 'required_if:payment_method,card|string',
                'payment_details.cvv' => 'required_if:payment_method,card|string'
            ]);

            // Get or create payment record
            $payment = $booking->payment ?? Payment::create([
                'booking_id' => $booking->id,
                'amount' => $booking->estimated_fare,
                'transaction_id' => uniqid('TXN'),
                'status' => 'pending',
                'payment_method' => $validated['payment_method']
            ]);

            if ($validated['payment_method'] === 'card') {
                // Process card payment through payment gateway
                // This is where you'd integrate with your payment provider
                $paymentResult = $this->processCardPayment(
                    $payment,
                    $validated['payment_details']
                );

                if ($paymentResult['success']) {
                    $payment->update([
                        'status' => 'completed',
                        'payment_details' => json_encode([
                            'transaction_ref' => $paymentResult['transaction_ref'],
                            'last4' => substr($validated['payment_details']['card_number'], -4)
                        ])
                    ]);

                    // Notify admin about successful payment
                    // event(new PaymentCompleted($payment));

                    return redirect()->route('bookings.show', $booking)
                        ->with('success', 'Payment processed successfully.');
                }

                return back()->with('error', 'Payment processing failed: ' . $paymentResult['message']);
            } else {
                // For cash payments, mark as pending until ride completion
                $payment->update([
                    'status' => 'pending',
                    'payment_details' => json_encode(['method' => 'cash'])
                ]);

                return redirect()->route('bookings.show', $booking)
                    ->with('success', 'Cash payment will be collected after the ride.');
            }
        } catch (\Exception $e) {
            Log::error('Payment processing error: ' . $e->getMessage());
            return back()->with('error', 'Payment processing failed. Please try again.');
        }
    }

    public function refund(Payment $payment)
    {
        try {
            if ($payment->status !== 'completed') {
                return back()->with('error', 'Only completed payments can be refunded.');
            }

            // Process refund through payment gateway
            $refundResult = $this->processRefund($payment);

            if ($refundResult['success']) {
                $payment->update([
                    'status' => 'refunded',
                    'payment_details' => json_encode([
                        'refund_ref' => $refundResult['refund_ref'],
                        'refunded_at' => now()
                    ])
                ]);

                // Notify customer about refund
                // event(new PaymentRefunded($payment));

                return redirect()->back()
                    ->with('success', 'Payment refunded successfully.');
            }

            return back()->with('error', 'Refund processing failed: ' . $refundResult['message']);
        } catch (\Exception $e) {
            Log::error('Refund processing error: ' . $e->getMessage());
            return back()->with('error', 'Refund processing failed. Please try again.');
        }
    }

    private function processCardPayment($payment, $paymentDetails)
    {
        // Integrate with your payment gateway here
        // This is a mock implementation
        return [
            'success' => true,
            'transaction_ref' => uniqid('CARD_'),
            'message' => 'Payment processed successfully'
        ];
    }

    private function processRefund($payment)
    {
        // Integrate with your payment gateway here
        // This is a mock implementation
        return [
            'success' => true,
            'refund_ref' => uniqid('REF_'),
            'message' => 'Refund processed successfully'
        ];
    }
}
