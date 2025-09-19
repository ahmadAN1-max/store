<ul class="account-nav">
    <li><a href="{{ route('user.index') }}"
            class="menu-link menu-link_us-s {{ Route::is('user.index') ? 'menu-link_active' : '' }}">Dashboard</a>
    </li>

    <li><a href="{{ route('user.account.orders') }}"
            class="menu-link menu-link_us-s {{ Route::is('user.account.orders') ? 'menu-link_active' : '' }}">Orders</a>
    </li>

    {{-- <li><a href="#"
            class="menu-link menu-link_us-s {{ Route::is('user.account.addresses') ? 'menu-link_active' : '' }}">Addresses</a>
    </li>
    <li><a href="#"
            class="menu-link menu-link_us-s {{ Route::is('user.account.details') ? 'menu-link_active' : '' }}">Account
            Details</a></li>
    <li><a href="#"
            class="menu-link menu-link_us-s {{ Route::is('user.account.wishlists') ? 'menu-link_active' : '' }}">Wishlist</a>
    </li> --}}
    <li>
        <form method="POST" action="{{ route('logout') }}" id="logout-form-1">
            @csrf
            <a href="{{ route('logout') }}" class="menu-link menu-link_us-s"
                onclick="event.preventDefault(); document.getElementById('logout-form-1').submit();">Logout</a>
        </form>
    </li>
</ul>
