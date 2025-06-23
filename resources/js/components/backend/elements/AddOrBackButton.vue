<template>
  <div
    v-if="$parent.addOrBack && Object.keys($parent.addOrBack).length > 0"
    class="box-tools pull-right"
  >
    <!-- custom route -->
    <slot v-if="$parent.addOrBack.extraButtons">
      <slot v-for="(rt, index) in $parent.addOrBack.extraButtons">
        <router-link
          :key="index"
          :to="{ name: rt.route, params: { id: rt.slug ? rt.slug : '' } }"
          :class="'btn btn-xs btn-' + rt.btnColor"
          class="pull-left text-white me-2"
        >
          <i :class="'bx bx-' + rt.icon" class="fw-bold"></i>
          <span class="text-capitalize">{{ rt.title }}</span>
        </router-link>
      </slot>
    </slot>

    <router-link
      v-if="$root.checkPermission($parent.addOrBack.route)"
      :to="{
        name: $parent.addOrBack.route,
        query: {
          page:
            $parent.addOrBack.icon != 'plus-circle' ? $route.query.page : '',
        },
      }"
      class="btn btn-xs btn-success pull-left text-white"
      :title="
        $parent.addOrBack.icon == 'plus-circle' ? 'Add New' : 'Back to list'
      "
    >
      <i :class="'bx bx-' + $parent.addOrBack.icon" class="fw-bold"></i>
      <span
        v-if="$parent.addOrBack.icon == 'plus-circle'"
        class="text-capitalize"
      >
        <span v-if="$parent.addOrBack.cusTitle">
          {{ $parent.addOrBack.cusTitle }}</span
        >
        <span v-else>Add {{ $parent.addOrBack.title }}</span>
      </span>
      <span v-else>Back</span>
    </router-link>
  </div>
</template>
