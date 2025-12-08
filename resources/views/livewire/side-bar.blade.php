<ul class="nav nav-secondary">
    <li class="nav-item">
        <a href="{{ route('invoices.index') }}">
            <i class="fas fa-file"></i>
            <p>Invoices</p>
            <span class="badge badge-secondary invoice-badge">{{ $invoicesCount }}</span>
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('clients.index') }}">
            <i class="fas fa-file"></i>
            <p>Clients</p>
            <span class="badge badge-secondary client-badge">{{ $clientsCount }}</span>
        </a>
    </li>

</ul>
