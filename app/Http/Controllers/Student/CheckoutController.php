<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Order;
use App\Services\XenditService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct(private XenditService $xendit) {}

    // ── Show checkout page ──────────────────────────────────────────
    public function show(Course $course)
    {
        $user = Auth::user();

        // Already enrolled
        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.learn', $course)->with('info', 'Anda sudah terdaftar di kursus ini.');
        }

        // Free course → enroll directly
        if ($course->price == 0) {
            return $this->enrollFree($course);
        }

        // Check for pending order (don't create duplicate)
        $pendingOrder = Order::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        return view('student.checkout', compact('course', 'pendingOrder'));
    }

    // ── Free course: instant enroll ─────────────────────────────────
    private function enrollFree(Course $course)
    {
        $user = Auth::user();
        Enrollment::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['enrolled_at' => now(), 'progress_percentage' => 0]
        );
        return redirect()->route('student.learn', $course)->with('success', 'Selamat datang di kursus gratis!');
    }

    // ── Enroll (called from detail page) ───────────────────────────
    public function enroll(Course $course)
    {
        $user = Auth::user();

        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.learn', $course)->with('info', 'Anda sudah terdaftar.');
        }

        if ($course->price == 0) {
            return $this->enrollFree($course);
        }

        // Paid course → go to checkout
        return redirect()->route('checkout.show', $course);
    }

    // ── Create Xendit invoice & redirect to payment page ───────────
    public function pay(Course $course)
    {
        $user = Auth::user();

        if (Enrollment::where('user_id', $user->id)->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.learn', $course);
        }

        // Reuse valid pending order
        $order = Order::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->first();

        if (!$order) {
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id'      => $user->id,
                'course_id'    => $course->id,
                'amount'       => $course->price,
                'status'       => 'pending',
            ]);
        }

        try {
            $invoice = $this->xendit->createInvoice($order);

            $order->update([
                'xendit_invoice_id'  => $invoice['invoice_id'],
                'xendit_invoice_url' => $invoice['invoice_url'],
                'expires_at'         => $invoice['expires_at'],
            ]);

            return redirect()->away($invoice['invoice_url']);
        } catch (\Exception $e) {
            Log::error('Checkout pay error', ['msg' => $e->getMessage()]);
            return back()->with('error', 'Gagal memproses pembayaran. Silakan coba lagi.');
        }
    }

    // ── Xendit success redirect ─────────────────────────────────────
    public function success(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Check real status from Xendit if not yet paid
        if (!$order->isPaid() && $order->xendit_invoice_id) {
            $status = $this->xendit->getInvoiceStatus($order->xendit_invoice_id);
            if (in_array($status, ['paid', 'settled'])) {
                $this->completeOrder($order, $status);
            }
        }

        $order->refresh();
        return view('student.payment-success', compact('order'));
    }

    // ── Xendit failure redirect ─────────────────────────────────────
    public function failed(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        return view('student.payment-failed', compact('order'));
    }

    // ── Xendit webhook callback ─────────────────────────────────────
    public function webhook(\Illuminate\Http\Request $request)
    {
        // Verify Xendit callback token
        $token = $request->header('x-callback-token');
        if (!$this->xendit->verifyWebhookToken($token)) {
            Log::warning('Xendit webhook: invalid token');
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $data   = $request->all();
        $status = strtolower($data['status'] ?? '');

        Log::info('Xendit webhook received', ['external_id' => $data['external_id'] ?? '', 'status' => $status]);

        $order = Order::where('order_number', $data['external_id'] ?? '')->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($status, ['paid', 'settled'])) {
            $this->completeOrder($order, $data['payment_method'] ?? null, $data);
        } elseif ($status === 'expired') {
            $order->update(['status' => 'expired']);
        }

        return response()->json(['message' => 'OK']);
    }

    // ── Internal: mark order paid & create enrollment ───────────────
    private function completeOrder(Order $order, mixed $paymentMethod = null, array $payload = []): void
    {
        if ($order->isPaid()) return;

        DB::transaction(function () use ($order, $paymentMethod, $payload) {
            $order->update([
                'status'         => 'paid',
                'payment_method' => is_string($paymentMethod) ? $paymentMethod : null,
                'paid_at'        => now(),
                'xendit_payload' => $payload ?: null,
            ]);

            Enrollment::firstOrCreate(
                ['user_id' => $order->user_id, 'course_id' => $order->course_id],
                ['enrolled_at' => now(), 'progress_percentage' => 0]
            );
        });
    }
}
