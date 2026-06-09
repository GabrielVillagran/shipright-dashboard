<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order {{ $order->order_number }}
            </h2>

            <a href="{{ route('orders.index') }}" class="text-sm text-blue-600 hover:underline">
                Back to orders
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Order Summary</h3>

                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm text-gray-500">Customer</dt>
                        <dd>{{ $order->customer_name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Email</dt>
                        <dd>{{ $order->customer_email }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Status</dt>
                        <dd>{{ ucfirst($order->status->value) }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Subtotal</dt>
                        <dd>{{ $order->formattedSubtotal() }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Shipping</dt>
                        <dd>{{ $order->formattedShipping() }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Tax</dt>
                        <dd>{{ $order->formattedTax() }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Total</dt>
                        <dd class="font-semibold">{{ $order->formattedTotal() }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Available Actions</h3>

                @if (count($availableStatuses) > 0)
                    <div class="space-y-3">
                        @foreach ($availableStatuses as $status)
                            <form method="POST" action="{{ route('orders.transition', $order) }}" class="border rounded p-4">
                                @csrf

                                <input type="hidden" name="status" value="{{ $status->value }}">

                                <label class="block text-sm text-gray-600 mb-1">
                                    Reason optional
                                </label>

                                <input
                                    type="text"
                                    name="reason"
                                    class="w-full rounded-md border-gray-300 mb-3"
                                    placeholder="Example: Approved after payment review"
                                >

                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                                    Move to {{ ucfirst($status->value) }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500">
                        No actions available for this order.
                    </p>
                @endif
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Line Items</h3>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="py-2 text-left">Product</th>
                            <th class="py-2 text-left">SKU</th>
                            <th class="py-2 text-left">Unit Price</th>
                            <th class="py-2 text-left">Quantity</th>
                            <th class="py-2 text-left">Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($order->lineItems as $lineItem)
                            <tr>
                                <td class="py-2">{{ $lineItem->product_name }}</td>
                                <td class="py-2">{{ $lineItem->product_sku }}</td>
                                <td class="py-2">{{ $lineItem->formattedUnitPrice() }}</td>
                                <td class="py-2">{{ $lineItem->quantity }}</td>
                                <td class="py-2">{{ $lineItem->formattedTotal() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="text-lg font-semibold mb-4">Audit History</h3>

                @forelse ($order->auditLogs->sortByDesc('created_at') as $auditLog)
                    <div class="border-b py-3">
                        <div class="text-sm text-gray-500">
                            {{ $auditLog->created_at->format('M d, Y h:i A') }}
                            @if ($auditLog->user)
                                by {{ $auditLog->user->name }}
                            @else
                                by system
                            @endif
                        </div>

                        <div>
                            {{ $auditLog->action }}
                        </div>

                        <div class="text-sm text-gray-600">
                            From:
                            {{ $auditLog->old_values['status'] ?? 'n/a' }}
                            →
                            To:
                            {{ $auditLog->new_values['status'] ?? 'n/a' }}
                        </div>

                        @if ($auditLog->reason)
                            <div class="text-sm text-gray-600">
                                Reason: {{ $auditLog->reason }}
                            </div>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500">
                        No audit history yet.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>