<template>
  <ul class="mm-collapse" :class="pmenu.looks_type">
    <slot v-for="(smenu, cindex) in sub_menus">
      <!-- Megamenu -->
      <ul
        v-if="smenu.route_name && $root.checkPermission(smenu.route_name)"
        :key="cindex"
      >
        <slot
          v-if="smenu.child_menus && Object.keys(smenu.child_menus).length > 0"
        >
          <li
            v-if="smenu.route_name && $root.checkPermission(smenu.route_name)"
          >
            <h6>{{ smenu.menu_name ? smenu.menu_name : "" }}</h6>
          </li>

          <slot v-for="(cmenu, cIndex) in smenu.child_menus">
            <li
              v-if="cmenu.route_name && $root.checkPermission(cmenu.route_name)"
              :key="cIndex"
            >
              <router-link
                v-if="cmenu.params"
                :to="{
                  name: cmenu.route_name,
                  params: { slug: cmenu.params },
                  query: { param: smenu.params },
                }"
              >
                <i class="bx bx-right-arrow-alt"></i>
                <span>{{ cmenu.menu_name }}</span>
              </router-link>
              <router-link v-else :to="{ name: cmenu.route_name }">
                <i class="bx bx-right-arrow-alt"></i>
                <span>{{ cmenu.menu_name }}</span>
              </router-link>
            </li>
          </slot>
        </slot>
        <slot v-else>
          <!-- Single Menu -->
          <li
            v-if="smenu.route_name && $root.checkPermission(smenu.route_name)"
          >
            <router-link
              v-if="smenu.params"
              :to="{
                name: smenu.route_name,
                params: { slug: smenu.params },
                query: { param: smenu.params },
              }"
            >
              <i class="bx bx-right-arrow-alt"></i>
              <span>{{ smenu.menu_name }}</span>
            </router-link>
            <router-link v-else :to="{ name: smenu.route_name }">
              <i class="bx bx-right-arrow-alt"></i>
              <span>{{ smenu.menu_name }}</span>
            </router-link>
          </li>
        </slot>
      </ul>
    </slot>
  </ul>
</template>

<script>
export default {
  name: "RecursiveNav",
  components: {
    RecursiveChildMenu: () => import("./RecursiveNav.vue"),
  },
  props: ["sub_menus", "pmenu"],
};
</script>