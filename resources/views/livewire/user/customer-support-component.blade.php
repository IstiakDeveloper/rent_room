<div>
    @if ($footer)
        <ul class="navbar-nav hover-menu main-menu px-0 mx-lg-n4">
            <li id="navbar-item-home" class="nav-item dropdown">
                <a class="nav-link px-2 border bg-secondary rounded-lg position-relative d-inline-flex align-items-center bg-light text-dark" href="tel:{{ $footer->contact_number }}">
                    <span class="fa fa-phone-alt"></span>
                        <span class="d-lg-inline d-none ml-lg-3">{{$footer->contact_number}}</span>
                </a>
            </li>
        </ul>
    @else
    <p>Hello</p>
    @endif
</div>
