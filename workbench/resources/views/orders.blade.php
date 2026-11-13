@extends('layout')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Orders</h1>

        <a href="/orders/create" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">New order</a>
    </div>

    <table class="mt-6 w-full border border-gray-200 bg-white text-left text-sm dark:border-gray-800 dark:bg-gray-900">
        <thead>
            <tr class="border-b border-gray-200 dark:border-gray-800">
                <th class="px-4 py-2 font-medium">Order</th>
                <th class="px-4 py-2 font-medium">Product</th>
                <th class="px-4 py-2 font-medium">Quantity</th>
                <th class="px-4 py-2 font-medium">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ([['#1004', 'Demo chair', 2, 'New'], ['#1003', 'Demo lamp', 1, 'Paid'], ['#1002', 'Demo desk', 1, 'Shipped'], ['#1001', 'Demo table', 1, 'Shipped']] as [$order, $product, $quantity, $status])
                <tr class="border-b border-gray-100 dark:border-gray-800">
                    <td class="px-4 py-2">{{ $order }}</td>
                    <td class="px-4 py-2">{{ $product }}</td>
                    <td class="px-4 py-2">{{ $quantity }}</td>
                    <td class="px-4 py-2">{{ $status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
