<template>
  <div class="nav-container">
    <div v-if="$root.site" class="mobile-topbar-header">
      <div>
        <img :src="$root.site.logo" class="logo-icon" alt="logo icon" />
      </div>
      <div>
        <h4 class="logo-text" v-if="$root.site.title">
          {{ $root.site.title.substring(0, 8) }}
        </h4>
      </div>
      <div class="toggle-icon ms-auto"><i class="bx bx-arrow-to-left"></i></div>
    </div>
    <nav class="topbar-nav">
      <ul class="metismenu" id="menu">
        <slot v-for="(pmenu, pIndex) in $root.menus">
          <!-- ===================CHILDREN MENU=================== -->
          <slot
            v-if="
              pmenu.child_menus && Object.keys(pmenu.child_menus).length > 0
            "
          >
            <li :key="pIndex">
              <a href="javascript:;" class="has-arrow">
                <div class="parent-icon">
                  <em v-if="pmenu.icon" v-html="pmenu.icon"></em>
                </div>
                <div class="menu-title">{{ pmenu.menu_name }}</div>
              </a>
              <RecursiveNav
                :key="pIndex + 'A'"
                :pmenu="pmenu"
                :sub_menus="pmenu.child_menus"
              />
            </li>
          </slot>

          <slot v-else>
            <li
              :key="pIndex"
              v-if="pmenu.route_name && $root.checkPermission(pmenu.route_name)"
            >
              <!-- MENU  WITH PARAMS-->
              <router-link
                v-if="pmenu.params"
                :to="{
                  name: pmenu.route_name,
                  params: { slug: pmenu.params },
                }"
              >
                <div class="parent-icon">
                  <em v-if="pmenu.icon" v-html="pmenu.icon"></em>
                </div>
                <div class="menu-title">{{ pmenu.menu_name }}</div>
              </router-link>

              <!-- SINGLE MENU -->
              <router-link v-else :to="{ name: pmenu.route_name }">
                <div class="parent-icon">
                  <em v-if="pmenu.icon" v-html="pmenu.icon"></em>
                </div>
                <div class="menu-title">{{ pmenu.menu_name }}</div>
              </router-link>
            </li>
          </slot>
        </slot>
      </ul>
    </nav>
  </div>
</template>

<script>
import RecursiveNav from "./RecursiveNav";

export default {
  components: { RecursiveNav },
  updated() {
    $(".metismenu")
      .children("li")
      .hover(function () {
        $(".mm-collapse").removeClass("mm-show");
        $(".has-arrow").attr("aria-expanded", false);
        $(this).children("a").attr("aria-expanded", true);
        $(this).children("ul").addClass("mm-show");
      })
      .mouseleave(function () {
        $(".mm-collapse").removeClass("mm-show");
        $(".has-arrow").attr("aria-expanded", false);
      });

    // Mobile Menu
    $(".mobile-toggle-menu").on("click", function () {
      $(".wrapper").addClass("toggled");
    });

    // toggle menu button
    $(".toggle-icon").click(function () {
      if ($(".wrapper").hasClass("toggled")) {
        // unpin sidebar when hovered
        $(".wrapper").removeClass("toggled");
        $(".sidebar-wrapper").unbind("hover");
      } else {
        $(".wrapper").addClass("toggled");
        $(".sidebar-wrapper").hover(
          function () {
            $(".wrapper").addClass("sidebar-hovered");
          },
          function () {
            $(".wrapper").removeClass("sidebar-hovered");
          }
        );
      }
    });

    $(".mobile-search-icon").on("click", function () {
      $(".search-bar").addClass("full-search-bar");
    });
    $(".search-close").on("click", function () {
      $(".search-bar").removeClass("full-search-bar");
    });
  },
};
</script>
<style>
.megamenu {
  display: flex;
}
.megamenu ul {
  width: 240px;
  border-right: 1px solid #eee;
}
.megamenu ul h6 {
  padding-left: 16px;
  padding-top: 10px;
  font-weight: 600;
  padding-bottom: 5px;
  font-size: 15px;
}
</style>
