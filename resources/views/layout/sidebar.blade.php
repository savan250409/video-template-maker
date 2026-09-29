<nav class="sidebar">
  <div class="sidebar-header">
    <a href="{{ url('admin/dashboard') }}" class="sidebar-brand">
        <img src="{{ upload_url('setting/'. App\Models\Setting::getSettingValue('app_logo')) }}" style="height:auto;width:45px;" />
    </a>
    <div class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <div class="sidebar-body">
    <ul class="nav">
      <li class="nav-item nav-category">Main</li>
      <li class="nav-item {{ active_class(['admin/dashboard']) }}">
        <a href="{{ url('admin/dashboard') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Dashboard</span>
        </a>
      </li>
      <li class="nav-item nav-category">web apps</li>
      @can('animated-template-list')
      <li class="nav-item {{ active_class(['admin/template-category*', 'admin/animated-template*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#template" role="button" aria-expanded="{{ is_active_route(['admin/template/category*']) }}" aria-controls="template">
          <img src="{{ asset('icons/sidebar - Video Templates.svg') }}" class="link-icon" />
          <span class="link-title">Video Template</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['admin/template-category*', 'admin/animated-template*']) }}" id="template">
          <ul class="nav sub-menu">
            @can('template-category-list')
            <li class="nav-item">
              <a href="{{ Route('template-category.index') }}" class="nav-link {{ active_class(['admin/template-category*']) }}">Category</a>
            </li>
            @endcan
            @can('animated-template-list')
            <li class="nav-item">
              <a href="{{ Route('animated-template.index') }}" class="nav-link {{ active_class(['admin/animated-template*']) }}">Template</a>
            </li>
            @endcan
          </ul>
        </div>
      </li>
      @endcan
      @can('musics-list')
      <li class="nav-item {{ active_class(['admin/music-category*', 'admin/musics*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#music" role="button" aria-expanded="{{ is_active_route(['admin/music-category']) }}" aria-controls="music">
          <img src="{{ asset('icons/sidebar - Music Templates.svg') }}" class="link-icon" />
          <span class="link-title">Music</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['admin/music-category*', 'admin/musics*']) }}" id="music">
          <ul class="nav sub-menu">
            @can('music-category-list')
            <li class="nav-item">
              <a href="{{ Route('music-category.index') }}" class="nav-link {{ active_class(['admin/music-category*']) }}">Category</a>
            </li>
            @endcan
            @can('musics-list')
            <li class="nav-item">
              <a href="{{ Route('musics.index') }}" class="nav-link {{ active_class(['admin/musics*']) }}">Music</a>
            </li>
            @endcan
          </ul>
        </div>
      </li>
      @endcan
      @can('banners-list')
      <li class="nav-item {{ active_class(['admin/banner*']) }}">
        <a href="{{ Route('banners.index') }}" class="nav-link">
          <img src="{{ asset('icons/sidebar - Banners.svg') }}" class="link-icon" />
          <span class="link-title">Banner</span>
        </a>
      </li>
      @endcan
      <li class="nav-item nav-category">General</li>
      @can('notification')
      <li class="nav-item {{ active_class(['admin/custom-notification*', 'admin/schedule-notifications*']) }}">
        <a class="nav-link" data-bs-toggle="collapse" href="#notification" role="button" aria-expanded="{{ is_active_route(['admin/custom-notification*']) }}" aria-controls="notification">
          <img src="{{ asset('icons/add_notification.svg') }}" class="link-icon" />
          <span class="link-title">Notification</span>
          <i class="link-arrow" data-feather="chevron-down"></i>
        </a>
        <div class="collapse {{ show_class(['admin/custom-notification*', 'admin/schedule-notification*']) }}" id="notification">
          <ul class="nav sub-menu">
            @can('notification')
            <li class="nav-item">
              <a href="{{ Route('custom-notification.create') }}" class="nav-link {{ active_class(['admin/custom-notification*']) }}">Custom</a>
            </li>
            @endcan
            @can('schedule-notification-list')
            <!--<li class="nav-item">-->
            <!--  <a href="{{ Route('schedule-notifications.index') }}" class="nav-link {{ active_class(['admin/schedule-notifications*']) }}">Schedule</a>-->
            <!--</li>-->
            @endcan
          </ul>
        </div>
      </li>
      @endcan

      @can('settings')
      <li class="nav-item {{ active_class(['admin/setting']) }}">
        <a href="{{ url('admin/setting') }}" class="nav-link">
          <img src="{{ asset('icons/sidebar Settings.svg') }}" class="link-icon" />
          <span class="link-title">Setting</span>
        </a>
      </li>
      @endcan
      @can('report-list')
      <li class="nav-item {{ active_class(['admin/reported']) }}">
        <a href="{{ url('admin/reported') }}" class="nav-link">
          <img src="{{ asset('icons/sidebar - Reported Video.svg') }}" class="link-icon" />
          <span class="link-title">Reported</span>
        </a>
      </li>
      @endcan
      
    </ul>
  </div>
</nav>
