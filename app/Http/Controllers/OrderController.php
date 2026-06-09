<?php

namespace App\Http\Controllers;

use App\Actions\Orders\TransitionOrderStatusAction;
use App\Enums\OrderStatus;
use App\Exceptions\InvalidOrderTransitionException;
use App\Http\Requests\TransitionOrderRequest;
use App\Models\Order;
use App\Services\Orders\OrderStatusMachine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View 
    {
        $selectedStatus = $request->query('status');

        $ordersQuery = Order::query()
            ->withCount('lineItems')
            ->latest();

            if($selectedStatus && OrderStatus::tryForm($selectedStatus)) {
                $ordersQuery->where('status', $selectedStatus);

            }

        return view('orders.index', [
            'orders' => $ordersQuery->paginate(15)->withQueryString(),
            'statuses' => OrderStatus::cases(),
            'selectedStatus' => $selectedStatus,
        ]);
    }
    
    public function show(Order $order, OrderStatusMachine $statusMachine): View
    {
        $order->load(['lineItems', 'auditLogs.user']);
        return view('orders.show',[
            'order' => $order,
            'availableStatuses'=> $statusMachine->allowedTransitions($order->status),
        ]);
    }

    public function transition(TransitionOrderRequest $request, Order $order, TransitionOrderStatusAction $transitionOrderStatus): RedirectResponse
    {
        $newStatus = OrderStatus::from($request->validated('status'));

        try {
            $transitionOrderStatus->execute(
                order: $order,
                newStatus: $newStatus,
                actor: $request->user(),
                reason: $request->validated('reason') ?? null,
            );

            return redirect()
                ->route('orders.show', $order)
                ->with('success', "Order moved to {$newStatus->value}.");
        } catch (InvalidOrderTransitionException $exception) {
            return redirect()
                ->route('orders.show', $order)
                ->with('error', $exception->getMessage());
        }
    }

}
