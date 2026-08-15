@props([
    'name'
])

@switch($name)

    {{-- DASHBOARD --}}
    @case('dashboard')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <rect x="3" y="3" width="7" height="7" rx="1.5"/>
            <rect x="14" y="3" width="7" height="7" rx="1.5"/>
            <rect x="3" y="14" width="7" height="7" rx="1.5"/>
            <rect x="14" y="14" width="7" height="7" rx="1.5"/>
        </svg>

        @break



    {{-- CUSTOMER --}}
    @case('customers')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="9" cy="8" r="3"/>
            <path d="M3.5 19c.7-3.2 2.6-5 5.5-5s4.8 1.8 5.5 5"/>
            <circle cx="17" cy="9" r="2.3"/>
            <path d="M15.5 14.5c2.8-.5 4.6 1 5 3.5"/>
        </svg>

        @break



    {{-- STAFF --}}
    @case('staff')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="12" cy="8" r="3.5"/>
            <path d="M5.5 20c.7-4 3-6 6.5-6s5.8 2 6.5 6"/>
            <path d="M17 4h4v4"/>
            <path d="M19 4v4"/>
        </svg>

        @break



    {{-- CATEGORY --}}
    @case('categories')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M4 6h6l2 2h8v10H4z"/>
            <path d="M4 9h16"/>
        </svg>

        @break



    {{-- PRODUCTS / EYEGLASSES --}}
    @case('products')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="7" cy="13" r="4"/>
            <circle cx="17" cy="13" r="4"/>
            <path d="M11 13h2"/>
            <path d="M3 12 5 7"/>
            <path d="m21 12-2-5"/>
        </svg>

        @break



    {{-- INVENTORY --}}
    @case('inventory')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="m4 7 8-4 8 4-8 4z"/>
            <path d="M4 7v10l8 4 8-4V7"/>
            <path d="M12 11v10"/>
        </svg>

        @break



    {{-- VOUCHER --}}
    @case('voucher')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4z"/>
            <circle cx="9" cy="10" r="1"/>
            <circle cx="15" cy="14" r="1"/>
            <path d="m9 15 6-6"/>
        </svg>

        @break



    {{-- ORDER --}}
    @case('orders')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M6 3h12v18l-2-1.5-2 1.5-2-1.5-2 1.5-2-1.5L6 21z"/>
            <path d="M9 8h6"/>
            <path d="M9 12h6"/>
            <path d="M9 16h4"/>
        </svg>

        @break



    {{-- PAYMENT --}}
    @case('payments')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <rect x="3" y="5" width="18" height="14" rx="2"/>
            <path d="M3 9h18"/>
            <path d="M7 15h4"/>
        </svg>

        @break



    {{-- APPOINTMENT --}}
    @case('appointments')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <rect x="3" y="5" width="18" height="16" rx="2"/>
            <path d="M7 3v4"/>
            <path d="M17 3v4"/>
            <path d="M3 10h18"/>
            <path d="m8 15 2 2 5-5"/>
        </svg>

        @break



    {{-- EYE PRESCRIPTION --}}
    @case('eye')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
            <circle cx="12" cy="12" r="3"/>
        </svg>

        @break



    {{-- WARRANTY --}}
    @case('warranty')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M12 3 20 6v6c0 4.7-3.1 7.6-8 9-4.9-1.4-8-4.3-8-9V6z"/>
            <path d="m8.5 12 2.2 2.2 4.8-5"/>
        </svg>

        @break



    {{-- REVIEW --}}
    @case('reviews')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3l-5.6 2.9 1.1-6.2L3 9.6l6.2-.9z"/>
        </svg>

        @break



    {{-- REPORT --}}
    @case('reports')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M4 20V10"/>
            <path d="M10 20V4"/>
            <path d="M16 20v-7"/>
            <path d="M22 20H2"/>
        </svg>

        @break



    {{-- WEBSITE --}}
    @case('website')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <circle cx="12" cy="12" r="9"/>
            <path d="M3 12h18"/>
            <path d="M12 3c3 3.2 3 14.8 0 18"/>
            <path d="M12 3c-3 3.2-3 14.8 0 18"/>
        </svg>

        @break



    {{-- LOGOUT --}}
    @case('logout')

        <svg
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="1.8"
            stroke-linecap="round"
            stroke-linejoin="round"
        >
            <path d="M10 5H5v14h5"/>
            <path d="M14 8l4 4-4 4"/>
            <path d="M8 12h10"/>
        </svg>

        @break

@endswitch