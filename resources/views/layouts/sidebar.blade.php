<div>
    <h2 class="text-xl font-bold mb-6">Menu</h2>

    <ul class="space-y-4">
        <li>
            <a href="{{ route('products.index') }}"
                class="block py-2 px-3 rounded hover:bg-gray-700
                       {{ request()->routeIs('products.index') ? 'bg-gray-700' : '' }}">
                Products
            </a>
        </li>

        <li>
            <a href="{{ route('users.index') }}"
                class="block py-2 px-3 rounded hover:bg-gray-700
                       {{ request()->routeIs('users.index') ? 'bg-gray-700' : '' }}">
                Users
            </a>
        </li>
        @if(auth()->user()->hasRole('Admin'))
            <li>
                <a href="{{ route('roles.index') }}"
                    class="block py-2 px-3 rounded hover:bg-gray-700
                        {{ request()->routeIs('roles.index') ? 'bg-gray-700' : '' }}">
                    Roles
                </a>
            </li>

            <li>
                <a href="{{ route('blogs.index') }}"
                    class="block py-2 px-3 rounded hover:bg-gray-700
                        {{ request()->routeIs('blogs.index') ? 'bg-gray-700' : '' }}">
                    Blogs
                </a>
            </li>

            <li>
                <a href="{{ route('invoices.index') }}"
                    class="block py-2 px-3 rounded hover:bg-gray-700
                        {{ request()->routeIs('invoices.index') ? 'bg-gray-700' : '' }}">
                    Invoices
                </a>
            </li>

            <li>
                <a href="{{ route('activity-logs.index') }}"
                    class="block py-2 px-3 rounded hover:bg-gray-700
                        {{ request()->routeIs('activity-logs.*') ? 'bg-gray-700' : '' }}">
                    Activity Logs
                </a>
            </li>
        @endif
        
    </ul>
</div>
