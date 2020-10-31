<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <li class="nav-item">
      <router-link to="/dashboard" class="nav-link">
        <i class="nav-icon fas fa-home blue"></i>
        <p>
          Dashboard
        </p>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/company" class="nav-link">
        <i class="fas fa-building nav-icon blue"></i>
        <p>Company</p>
      </router-link>
    </li>
   
    <li class="nav-item">
      <router-link to="/slider" class="nav-link">
        <i class="nav-icon fas fa-info-circle blue"></i>
        <p>
          Slider
        </p>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/banner" class="nav-link">
        <i class="nav-icon fas fa-flag blue"></i>
        <p>
          Banner
        </p>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/gallery" class="nav-link">
        <i class="nav-icon fas fa-images blue"></i>
        <p>
          Gallery
        </p>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/video" class="nav-link">
        <i class="nav-icon fas fa-video blue"></i>
        <p>
          Video
        </p>
      </router-link>
    </li>
    <li class="nav-item">
      <router-link to="/footer" class="nav-link">
        <i class="fas fa-list nav-icon blue"></i>
        <p>Footer</p>
      </router-link>
    </li>
    <li class="nav-item has-treeview">
      <a href="#" class="nav-link">
        <i class="nav-icon fas fa-cog green"></i>
        <p>
          Product Settings
          <i class="right fas fa-angle-left"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <router-link to="/products" class="nav-link">
            <i class="nav-icon fas fa-list orange"></i>
            <p>
              Product
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/product/category" class="nav-link">
            <i class="nav-icon fas fa-window-minimize green"></i>
            <p>
              Category
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/product/sub_category" class="nav-link">
            <i class="nav-icon fas fa-bars green"></i>
            <p>
              Sub Category
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/product/size" class="nav-link">
            <i class="nav-icon fas fa-window-restore green"></i>
            <p>
              Size
            </p>
          </router-link>
        </li>
        <li class="nav-item">
          <router-link to="/product/tag" class="nav-link">
            <i class="nav-icon fas fa-tags green"></i>
            <p>
              Tag
            </p>
          </router-link>
        </li>


        <li class="nav-item" style="display:none;">
          <router-link to="/developer" class="nav-link">
            <i class="nav-icon fas fa-cogs white"></i>
            <p>
              Developer
            </p>
          </router-link>
        </li>
      </ul>
    </li>
    @can('isAdmin')
    <li class="nav-item">
      <router-link to="/users" class="nav-link">
        <i class="fa fa-user nav-icon blue"></i>
        <p>Users</p>
      </router-link>
    </li>
    @endcan
    <li class="nav-item">
      <router-link to="/client" class="nav-link">
        <i class="nav-icon fas fa-users blue"></i>
        <p>
          Clients
        </p>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/social_link" class="nav-link">
        <i class="nav-icon fas fa-link blue"></i>
        <p>
          Social Link
        </p>
      </router-link>
    </li>

    <li class="nav-item">
      <router-link to="/faq" class="nav-link">
        <i class="nav-icon fas fa-question blue"></i>
        <p>
          FAQ
        </p>
      </router-link>
    </li>


    <li class="nav-item">
      <a href="#" class="nav-link" onclick="event.preventDefault();
          document.getElementById('logout-form').submit();">
        <i class="nav-icon fas fa-power-off red"></i>
        <p>
          {{ __('Logout') }}
        </p>
      </a>
      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
      </form>
    </li>
  </ul>
</nav>