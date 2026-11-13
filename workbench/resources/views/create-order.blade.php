@extends('layout')

@section('content')
    <h1 class="text-2xl font-semibold">New order</h1>

    <form action="/orders" method="GET" class="mt-6 flex flex-col gap-4">
        <label class="flex flex-col gap-1">
            Product
            <input type="text" class="rounded-md border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
        </label>

        <label class="flex flex-col gap-1">
            Quantity
            <input type="number" class="rounded-md border border-gray-300 bg-white px-3 py-2 dark:border-gray-700 dark:bg-gray-900">
        </label>

        <button type="submit" class="self-start rounded-md bg-indigo-600 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-500">Save</button>
    </form>
@endsection
