<template>
  <div class="card border">
    <div class="card-header">
      <h6 class="mb-0">
        Search Engine Optimize
        <a
          @click="showMeta = !showMeta"
          href="javascript:void(0)"
          class="float-end fs-7"
        >
          Edit SEO Meta
        </a>
      </h6>
    </div>
    <div class="card-body p-3">
      <p v-if="!$parent.data.meta.title || showMeta">
        Setup meta title & description to make your site easy to discovered on
        search engines such as Google
      </p>
      <slot v-else>
        <h5 style="color: #1a0dab; font-weight: 200" class="mb-0">
          {{ $parent.data.meta.title }}
        </h5>
        <a
          style="color: #006621"
          target="_blank"
          :href="$root.baseurl + '/product-details/' + $parent.data.slug"
        >
          {{ $root.baseurl + "/product-details/" + $parent.data.slug }}
        </a>
        <p>
          <span style="color: #70757a">
            {{ $parent.data.created_at | formatDate }} -
          </span>
          <span v-html="$parent.data.meta.description.substring(0, 240)"></span>
        </p>
      </slot>
      <slot v-if="showMeta">
        <hr />
        <div class="col-12">
          <label for="">SEO Title</label>
          <input
            name="meta[title]"
            v-model="$parent.data.meta.title"
            type="text"
            class="form-control form-control-sm"
            placeholder="SEO Title"
          />
        </div>
        <div class="col-12 mt-3">
          <label for="">SEO Description</label>
          <textarea
            name="meta[description]"
            v-model="$parent.data.meta.description"
            class="form-control"
            placeholder="SEO Description"
            rows="6"
          ></textarea>
        </div>
      </slot>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showMeta: false,
    };
  },
};
</script>