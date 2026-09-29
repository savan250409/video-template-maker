<nav class="navbar">
  <a href="#" class="sidebar-toggler">
    <i data-feather="menu"></i>
  </a>
  <div class="navbar-content">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link " href="{{ Route('custom-notification.create') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Set Notification">
              <img src="{{ asset('icons/add Notification.svg') }}" />
            </a>    
        </li>
        <li class="nav-item">
            <a class="nav-link " href="{{ Route('animated-template.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Add Template">
              <img src="{{ asset('icons/Add video Templates.svg') }}" />
            </a>    
        </li>
        <li class="nav-item">
            <a class="nav-link " href="{{ Route('banners.index') }}" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Add Banner">
              <img src="{{ asset('icons/Add Posters.svg') }}" />
            </a>    
        </li>

      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img class="wd-30 ht-30 rounded-circle" src="{{ !is_null(Auth::user()->logo) ? upload_url('profile/'. Auth::user()->logo) : 'https://via.placeholder.com/30x30' }}" alt="profile">
        </a>
        <div class="dropdown-menu p-0" aria-labelledby="profileDropdown">
          <div class="d-flex flex-column align-items-center border-bottom px-5 py-3">
            <div class="mb-3">
              <img class="wd-80 ht-80 rounded-circle" src="{{ !is_null(Auth::user()->logo) ? upload_url('profile/'. Auth::user()->logo) : 'https://via.placeholder.com/80x80' }}" alt="">
            </div>
            <div class="text-center">
              <p class="tx-16 fw-bolder">{{ Auth::user()->name }}</p>
              <p class="tx-12 text-muted">{{ Auth::user()->email }}</p>
            </div>
          </div>
          <ul class="list-unstyled p-1">
            <li class="dropdown-item py-2">
              <a href="{{ Route('profile') }}" class="text-body ms-0">
                <i class="me-2 icon-md" data-feather="edit"></i>
                <span>Edit Profile</span>
              </a>
            </li>
            <a href="{{ Route('logout') }}" class="text-body ms-0">
            <li class="dropdown-item py-2">
                <i class="me-2 icon-md" data-feather="log-out"></i>
                <span>Log Out</span>
              </li>
            </a>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</nav>