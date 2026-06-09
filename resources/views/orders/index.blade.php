<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Orders
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-6 bg-white p-4 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('orders.index') }}" class="flex gap-3 items-end">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">
                            Filter by status
                        </label>

                        <select name="status" id="status" class="mt-1 rounded-md border-gray-300">
                            <option value="">All statuses</option>

                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($selectedStatus === $status->value)>
                                    {{ ucfirst($status->value) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                        Apply
                    </button>

                    <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-gray-200 rounded-md">
                        Clear
                    </a>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Order</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Customer</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Items</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Total</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600">Created</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse ($orders as $order)
                            <tr>
                                <td class="px-4 py-3">
                                    {{ $order->order_number }}
                                </td>

                                <td class="px-4 py-3">
                                    <div>{{ $order->customer_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $order->customer_email }}</div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded bg-gray-100">
                                        {{ ucfirst($order->status->value) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    {{ $order->line_items_count }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $order->formattedTotal() }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $order->created_at->format('M d, Y') }}
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                    No orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>