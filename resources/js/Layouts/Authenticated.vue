<template>
  <div>
    <header class="navbar navbar-expand-md navbar-light d-print-none">
      <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu">
          <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
          <Link :href="route('dashboard')">
            <breeze-application-logo width="100"></breeze-application-logo>
          </Link>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
          <div class="nav-item dropdown">
            <a href="#"
               class="nav-link d-flex lh-1 text-reset p-0"
               data-bs-toggle="dropdown"
               aria-label="Open user menu"
            >
              <div class="d-none d-xl-block ps-2">
                <div>{{ $page.props.auth.user.name }}</div>
              </div>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
              <a href="#" class="dropdown-item">Profil</a>
              <div class="dropdown-divider"></div>
              <breeze-dropdown-link @click="logout" as="button">
                Wyloguj
              </breeze-dropdown-link>
            </div>
          </div>
        </div>
        <div class="collapse navbar-collapse" id="navbar-menu">
          <div class="d-flex flex-column flex-md-row flex-fill align-items-stretch align-items-md-center">
            <ul class="navbar-nav">
              <breeze-nav-link :href="route('dashboard')" :active="route().current('dashboard')">
                Dashboard
              </breeze-nav-link>
              <breeze-nav-link :href="route('clients.index')" :active="route().current('clients.*')">
                Kontrahenci
              </breeze-nav-link>
            </ul>
          </div>
        </div>
      </div>
    </header>
    <div class="page-wrapper">
      <div class="page-body">
        <div class="container-xl">
          <slot></slot>
        </div>
      </div>
    </div>
    <!--            <breeze-nav-link v-if="$page.props.roles.includes('admin')" :href="route('roles.index')"-->
    <!--                             :active="route().current('roles.*')">-->
    <!--              Roles-->
    <!--            </breeze-nav-link>-->
    <!--            <breeze-nav-link v-if="$page.props.roles.includes('admin')" :href="route('permissions.index')"-->
    <!--                             :active="route().current('permissions.*')">-->
    <!--              Permissions-->
    <!--            </breeze-nav-link>-->

    <!--    <header class="d-flex py-3 bg-white shadow-sm border-bottom">-->
    <!--      <div class="container">-->
    <!--        <div v-if="$page.props.flash.message" class="text-info">{{ $page.props.flash.message }}</div>-->
    <!--        <slot name="header"/>-->
    <!--      </div>-->
    <!--    </header>-->
  </div>
</template>

<script>
import BreezeApplicationLogo from '@/Components/ApplicationLogo.vue'
import BreezeDropdown from '@/Components/Dropdown.vue'
import BreezeDropdownLink from '@/Components/DropdownLink.vue'
import BreezeNavLink from '@/Components/NavLink.vue'
import {Link} from '@inertiajs/inertia-vue3'
import {Inertia} from '@inertiajs/inertia'

export default {
  components: {
    BreezeApplicationLogo,
    BreezeDropdown,
    BreezeDropdownLink,
    BreezeNavLink,
    Link,
  },
  data() {
    return {
      showingNavigationDropdown: false,
    }
  },
  methods: {
    logout() {
      Inertia.post(route("logout"));
    }
  },
}
</script>
