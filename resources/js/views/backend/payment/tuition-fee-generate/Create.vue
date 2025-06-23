<template>
    <div v-if="!$root.spinner" class="col-xl-12">
        <form v-if="!$root.spinner" v-on:submit.prevent="submit">
            <div class="row">
                <div class="col-6">
                    <div class="card border">
                        <div class="card-header">
                            <h6 class="mb-0 text-uppercase">Generate tuition Fees</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3 mb-2">
                                <label class="col-6 col-xl-3 text-center"> Is Active ? </label>
                                <label class="col-6 col-xl-4">Month</label>
                                <label class="col-6 col-xl-3 text-center">Amount</label>
                            </div>

                            <div v-for="(item, key) in data.details" :key="key" class="row g-3 mb-3">
                                <!------------ Single Input ------------>
                                <div class="col-6 col-xl-3 text-center pt-1">
                                    <input type="checkbox" v-model="item.status" value="1" />
                                </div>
                                <!------------ Single Input ------------>
                                <div class="col-6 col-xl-4">
                                    <input type="text" readonly :value="monthName(item.month)"
                                        class="form-control form-control-sm" />
                                </div>
                                <!------------ Single Input ------------>
                                <div class="col-6 col-xl-3">
                                    <input type="number" v-model="item.amount" placeholder="100"
                                        class="form-control form-control-sm text-center" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border">
                        <div class="card-header">
                            <h6 class="mb-0 text-uppercase">Academic Information</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3">
                                <!------------ Single Input ------------>
                                <SelectCustom v-if="!$root.institution_id" title="Institution" field="institution_id"
                                    :req="true" :datas="$root.global.institutions" col="6" val="id" val_title="name" />
                                <!------------ Single Input ------------>
                                <SelectCustom title="Shift" field="shift_id" :req="true" :datas="$root.global.shifts"
                                    col="6" val="id" val_title="name" />
                                <!------------ Single Input ------------>
                                <SelectCustom title="Medium/Version" field="medium_id" :req="true"
                                    :datas="$root.global.mediums" col="6" val="id" val_title="name" />
                                <!------------ Single Input ------------>
                                <SelectCustom title="Academic Class" field="academic_class_id" :req="true"
                                    :datas="$root.global.classes" col="6" val="id" val_title="name" />
                                <!------------ Single Input ------------>
                                <SelectCustom title="Group" field="group_id" :req="true" :datas="$root.global.groups"
                                    val="id" val_title="name" />
                                <!------------ Single Input ------------>
                                <SelectCustom title="Account Head" field="account_head_id" :req="true"
                                    :datas="accountHeads" val="id" val_title="name_en" />
                            </div>

                            <!-- SUBMIT BUTTON-->
                            <div class="col-12 d-flex justify-content-center">
                                <div class="col-xl-3 text-center my-5">
                                    <button type="button" :disabled="$root.submit ? true : false"
                                        class="btn btn-success btn-sm px-3" @click="submit()">
                                        <span v-if="$root.submit">
                                            <span class="spinner-border spinner-border-sm"></span>
                                            <span>processing...</span>
                                        </span>
                                        <span v-else>
                                            <i class="bx bx-save"></i> Generate Fees</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import moment from "moment";

    // define model name
    const model = "tuitionFeeGenerate";

    // Add Or Back
    const addOrBack = {
        route: model + ".index",
        title: "tuition Fee Generate",
        icon: "left-arrow-alt",
    };

    export default {
        data() {
            return {
                model: model,
                setup_exist: false,
                data: {
                    institution_id: null,
                    shift_id: null,
                    medium_id: null,
                    academic_class_id: null,
                    account_head_id: null,
                    group_id: null,
                    details: [],
                },
            };
        },

        watch: {
            "data.shift_id": function (id) {
                if (id) this.checkExist();
            },
            "data.group_id": function (id) {
                if (id) this.checkExist();
            },
            "data.medium_id": function (id) {
                if (id) this.checkExist();
            },
            "data.academic_class_id": function (id) {
                if (id) {
                    this.checkExist();
                    if (!this.data.id) {
                        this.feesGenerate(); // Only regenerate in create mode
                    }
                }
            }

        },

        computed: {
            accountHeads() {
                if (this.$root.global.account_heads) {
                    return this.$root.global.account_heads.filter(
                        (e) => e.type == "tuition"
                    );
                }
            },
        },

        methods: {
            submit: function () {
                if (this.setup_exist) {
                    this.notify("Sorry!! Already Generated This Class", "error");
                    return false;
                }

                this.$validate().then((res) => {
                    const error = this.validation.countErrors();
                    // If there is an error
                    if (error > 0) {
                        this.notify(error, "validate");
                        return false;
                    }

                    // If there is no error
                    if (res) {
                        if (this.data.id) {
                            this.update(this.model, this.data, this.data.id);
                        } else {
                            this.store(this.model, this.data, this.submitType);
                        }
                    }
                });
            },

            // Check Exist  Fee Setup
            checkExist() {
                if (
                    !this.data.institution_id ||
                    !this.data.medium_id ||
                    !this.data.academic_class_id ||
                    !this.data.group_id ||
                    !this.data.shift_id
                ) {
                    return false;
                }

                let data = {
                    institution_id: this.data.institution_id,
                    shift_id: this.data.shift_id,
                    medium_id: this.data.medium_id,
                    group_id: this.data.group_id,
                    academic_class_id: this.data.academic_class_id,
                    id: this.data.id,
                };
                axios.get("exist-tuitionFeeSetup", { params: data }).then((res) => {
                    if (res.data) {
                        this.notify("Sorry!! Already Generated This Class", "error");
                    }
                    this.setup_exist = res.data;
                });
            },

            // feesGenerate() {
            //     for (let i = 0; i < 12; i++) {
            //         this.pushData(i);
            //     }
            // },
            feesGenerate() {
                this.data.details = [];

                let isCollege = [13, 14].includes(this.data.academic_class_id);
                let startMonth = isCollege ? 6 : 0; // July = 6, January = 0

                for (let i = 0; i < 12; i++) {
                    let monthIndex = (startMonth + i) % 12;
                    this.pushData(monthIndex);
                }
            },


            pushData(i) {
                let year = new Date().getFullYear();
                this.data.details.push({
                    date: moment(String(`${i + 1}/1/${year}`)).format("YYYY-MM-DD"),
                    month: 1 + i,
                    amount: 0,
                    status: 1,
                });
            },

            monthName(key) {
                let monthName = "";
                if (this.$root.global.months) {
                    let month = this.$root.global.months.find((e) => e.key == key);
                    monthName = month ? month.name : "";
                }
                return monthName;
            },
        },

        created() {
            let page = "create";
            if (this.$route.params.id) {
                page = "edit";
                this.get_data(`${this.model}/${this.$route.params.id}`);
            } else {
                // moved feesGenerate to watcher to wait for academic_class_id selection
            }

            this.setBreadcrumbs(this.model, page, "Tuition Fee Generate", addOrBack);
        },


        mounted() {
            if (this.$root.institution_id) {
                this.data.institution_id = this.$root.institution_id;
            }
        },

        // ================== validation rule for form ==================
        validators: {
            "data.account_head_id": function (value = null) {
                return Validator.value(value).required("account Head  is required");
            },
            "data.academic_class_id": function (value = null) {
                return Validator.value(value).required("Academic Class is required");
            },
            "data.medium_id": function (value = null) {
                return Validator.value(value).required("Medium is required");
            },
            "data.group_id": function (value = null) {
                return Validator.value(value).required("Group is required");
            },
            "data.shift_id": function (value = null) {
                return Validator.value(value).required("Shift is required");
            },
            "data.institution_id": function (value = null) {
                return Validator.value(value).required("Institution is required");
            },
        },
    };
</script>
