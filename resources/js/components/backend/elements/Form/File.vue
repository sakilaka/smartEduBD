<template>
  <div :class="'col-xl-' + (col ? col : 6)" class="col-12">
    <Label
      :title="title"
      :req="req"
      :condition="$parent.validation.hasError('data.' + field)"
      :msg="$parent.validation.firstError('data.' + field)"
    />
    <div class="row">
      <div class="col-2">
        <img
          class="img-responsive"
          :src="fileSrc()"
          alt="picture"
          style="height: 31px; width: 40px; border-radius: 3px"
        />
      </div>
      <div class="col-10">
        <b-form-file
          v-if="crop"
          :accept="mime == 'img' ? 'image/*' : '.pdf'"
          :id="'file-small ' + fileClassName"
          size="sm"
          :name="field"
          :class="reqClass()"
          v-on:change="showCropImage($event, field, fileClassName)"
          drop-placeholder="Drop file here"
        ></b-form-file>

        <b-form-file
          v-else
          :accept="mime == 'img' ? 'image/*' : '.pdf'"
          :id="'file-small ' + fileClassName"
          size="sm"
          :name="field"
          :class="reqClass()"
          v-on:change="onFileChange($event, field, fileClassName)"
          drop-placeholder="Drop file here"
        ></b-form-file>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["title", "field", "col", "req", "mime", "fileClassName", "crop"],
  methods: {
    onFileChange(e, model, fileClass) {
      let pdf = this.mime == "pdf" ? "pdf" : null;
      this.showImageGlobal(e, model, model, fileClass, pdf);
    },

    reqClass() {
      if (this.$parent.validation.hasError("data." + this.field) && this.req) {
        return "form-invalid";
      } else if (this.$parent.data[this.field]) {
        return "form-valid";
      }
    },
    fileSrc() {
      let field = this.field;
      let data = this.$parent.data;
      let images = this.$parent.images;

      if (this.mime == "pdf" && data[field]) {
        return this.$root.attach;
      } else {
        return images[field]
          ? images[field]
          : data[field]
          ? data[field]
          : this.$root.noimage;
      }
    },
  },
};
</script>