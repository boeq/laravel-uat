@extends('layout')

@section('content')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Orders</h1>

        <a href="/orders/create" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white">New order</a>
    </div>

    <table class="mt-6 w-full border border-gray-200 bg-white text-left text-sm">
        <thead>
            <tr class="border-b border-gray-200">
                <th class="px-4 py-2">Order</th>
                <th class="px-4 py-2">Product</th>
                <th class="px-4 py-2">Quantity</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="px-4 py-2">#1001</td>
                <td class="px-4 py-2">Demo table</td>
                <td class="px-4 py-2">1</td>
            </tr>
        </tbody>
    </table>
@endsection
