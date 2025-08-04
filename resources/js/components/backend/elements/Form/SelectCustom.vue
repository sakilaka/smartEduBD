<template>
    <div :class="'col-xl-' + (col ? col : 6)" class="col-12">
        <Label :title="title" :req="req" :condition="$parent.validation.hasError('data.' + field)"
            :msg="$parent.validation.firstError('data.' + field)" />
        <select v-model="$parent.data[field]" :name="field" :disabled="disable ? true : false"
            :readonly="readOnly ? true : false" class="form-select form-select-sm" :class="reqClass()">
            <option :value="null">--Select Any--</option>
            <option v-for="(value, index) in datas" :key="index" v-bind:value="value[val]">
                {{ value[val_title] }}
            </option>
        </select>
    </div>
</template>

<script>
    export default {
        props: [
            "title",
            "field",
            "col",
            "req",
            "datas",
            "val",
            "val_title",
            "disable",
            "readOnly",
        ],
        methods: {
            reqClass() {
                if (this.$parent.validation.hasError("data." + this.field) && this.req) {
                    return "form-invalid";
                } else if (this.$parent.data[this.field]) {
                    return "form-valid";
                }
            },
        },
    };
</script>
