<template>
    <form v-if="!$root.spinner" v-on:submit.prevent="submit" id="form" class="row">
        <div class="col-xl-12">
            <div class="card border">
                <div class="card-body p-3">
                    <div class="row g-3">
                        <!------------ Single Input ------------>
                        <SelectCustom v-show="!$root.institution_id" title="Institution" field="institution_id"
                            :req="true" :datas="$root.global.institutions" col="3" val="id" val_title="name" />
                        <!------------ Single Input ------------>
                        <SelectCustom title="Campus" field="campus_id" :req="true"
                            :datas="campuses_filter(data.institution_id)" col="3" val="id" val_title="name" />
                        <!------------ Single Input ------------>
                        <SelectCustom title="Shift" field="shift_id" :req="true"
                            :datas="shift_filter(data.institution_id)" col="3" val="shift_id" val_title="shift_name" />
                        <!------------ Single Input ------------>
                        <SelectCustom title="Medium" field="medium_id" :req="true"
                            :datas="medium_filter(data.institution_id)" col="3" val="medium_id"
                            val_title="medium_name" />
                        <!------------ Single Input ------------>
                        <SelectCustom title="Academic Class" field="academic_class_id" :req="true"
                            :datas="category_classes_filter(data.institution_id, 3)" col="3" val="academic_class_id"
                            val_title="class_name" />

                        <!------------ Single Input ------------>
                        <SelectCustom title="Academic group" field="academic_group_id" :req="true"
                            :datas="availableGroups" col="3" val="id" val_title="name" />

                        <!------------ Single Input ------------>
                        <Input title="Main Subject" field="main_subject" type="number" :col="3" />
                        <!------------ Single Input ------------>
                        <Textarea title="Note" :fixheight="true" field="note" :col="12" />
                    </div>
                </div>
            </div>

            <div class="card border">
                <div class="card-body p-3">
                    <table class="table table-sm table-bordered table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th style="width: 100px" class="text-center">4th Subject</th>
                                <th style="width: 100px" class="text-center">Main Subject</th>
                                <th class="text-center" style="width: 80px">CT M.</th>
                                <th class="text-center" style="width: 80px">CT Pass</th>
                                <th class="text-center" style="width: 80px">CQ M.</th>
                                <th class="text-center" style="width: 80px">CQ Pass</th>
                                <th class="text-center" style="width: 90px">MCQ M.</th>
                                <th class="text-center" style="width: 90px">MCQ Pass</th>
                                <th class="text-center" style="width: 90px">Prac. M.</th>
                                <th class="text-center" style="width: 90px">Prac. Pass</th>
                                <th class="text-center" style="width: 90px">Total Mark</th>
                                <th class="text-center" style="width: 80px">Sorting</th>
                                <th style="width: 90px"></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(subject, key) in data.details" :key="key">
                                <td>
                                    <v-select v-model="subject.subject_id" label="name" :reduce="(obj) => obj.id"
                                        :options="extraData.subjects ? extraData.subjects : []"
                                        placeholder="--Select One--" :closeOnSelect="true" :class="validation.hasError('data.subject_id') ? 'required' : ''
                                            "></v-select>

                                    <v-select v-if="
                                        subject.fourth_subject == 1 || subject.main_subject == 1
                                    " v-model="subject.except_subject_id" label="name" :reduce="(obj) => obj.id"
                                        :options="extraData.subjects ? extraData.subjects : []"
                                        placeholder="--Select One--" :closeOnSelect="true" :class="validation.hasError('data.subject_id') ? 'required' : ''
                                            " class="mt-2"></v-select>
                                </td>
                                <td class="text-center">
                                    <input type="radio" v-model="subject.fourth_subject" value="1" />
                                    Yes <br />
                                    <input type="radio" v-model="subject.fourth_subject" value="0" />
                                    No
                                </td>
                                <td class="text-center">
                                    <input type="radio" v-model="subject.main_subject" value="1" />
                                    Yes <br />
                                    <input type="radio" v-model="subject.main_subject" value="0" />
                                    No
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.ct_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.ct_pass_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.cq_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.cq_pass_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.mcq_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.mcq_pass_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.practical_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.practical_pass_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="text"
                                        v-model="subject.total_mark" />
                                </td>
                                <td>
                                    <input class="form-control form-control-sm text-center" type="number"
                                        v-model="subject.sorting" />
                                </td>
                                <td class="text-center">
                                    <i v-if="Object.keys(data.details).length > 1" @click="data.details.splice(key, 1)"
                                        class="bx bx-minus btn btn-xs btn-danger">
                                    </i>
                                    <i v-if="Object.keys(data.details).length == key + 1" @click="
                                        data.details.push({
                                            subject_id: null,
                                            fourth_subject: 0,
                                            main_subject: 0,
                                            ct_mark: 0,
                                            ct_pass_mark: 0,
                                            cq_mark: 0,
                                            cq_pass_mark: 0,
                                            mcq_mark: 0,
                                            mcq_pass_mark: 0,
                                            practical_mark: 0,
                                            practical_pass_mark: 0,
                                            total_mark: 0,
                                        })
                                        " class="bx bx-plus btn btn-xs btn-primary ml-2">
                                    </i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON-->
        <div class="col-12 d-flex justify-content-center">
            <div class="col-xl-3 text-center my-3 mb-5">
                <button type="button" :disabled="$root.submit ? true : false" class="btn btn-success btn-sm px-3"
                    @click="submit()">
                    <span v-if="$root.submit">
                        <span class="spinner-border spinner-border-sm"></span>
                        <span>processing...</span>
                    </span>
                    <span v-else> <i class="bx bx-save"></i> Save</span>
                </button>
            </div>
        </div>
    </form>
</template>

<script>
    // define model name
    const model = "higherSecondarySubjectAssign";

    // Add Or Back
    const addOrBack = {
        route: model + ".index",
        title: "Subject Assign",
        icon: "left-arrow-alt",
    };

    export default {
        components: {
            Textarea: () =>
                import("../../../../../components/backend/elements/Form/Textarea.vue"),
        },
        data() {
            return {
                model: model,
                submitType: "",
                data: {
                    institution_id: null,
                    campus_id: null,
                    shift_id: null,
                    medium_id: null,
                    academic_class_id: null,
                    academic_group_id: null,
                    main_subject: null,
                    details: [
                        {
                            subject_id: null,
                            fourth_subject: 0,
                            main_subject: 0,
                            ct_mark: 0,
                            ct_pass_mark: 0,
                            cq_mark: 0,
                            cq_pass_mark: 0,
                            mcq_mark: 0,
                            mcq_pass_mark: 0,
                            practical_mark: 0,
                            practical_pass_mark: 0,
                            total_mark: 0,
                        },
                    ],
                },
                extraData: {
                    subjects: [],
                },
                subjects: [],
                availableGroups: [],
            };
        },

        computed: {
            classes() {
                let classesArr = [];
                if (this.$root.global.classes) {
                    classesArr = this.$root.global.classes.filter(
                        (e) => e.institution_category_id == 3
                    );
                }
                return classesArr;
            },
        },

        methods: {
            submit: function () {
                this.$validate().then((res) => {
                    const error = this.validation.countErrors();
                    // If there is an error
                    if (error > 0) {
                        this.notify(error, "validate");
                        return false;
                    }

                    // subjects required
                    let subjectReq = false;
                    this.data.details.forEach((el) => {
                        if (!el.subject_id) {
                            this.notify("Please select subject", "error");
                            subjectReq = true;
                            return false;
                        }
                    });
                    if (subjectReq) {
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

            // Async Data
            asyncData() {
                let page = "create";
                if (this.$route.params.id) {
                    page = "edit";
                    this.get_data(this.model, this.$route.params.id, "data");
                }
                this.setBreadcrumbs(this.model, page, "Subject Assign", addOrBack);

                this.get_paginate_data("subject", { allData: true }, "subjects");
            },
            handleInstitutionOrClassChange() {
                // Reset group selection when institution or class changes
                // this.data.academic_group_id = null;

                // Only fetch groups if both fields have values
                if (this.data.institution_id && this.data.academic_class_id) {
                    this.fetchGroups();
                } else {
                    this.availableGroups = [];
                }
            },
            fetchGroups() {
                if (this.data.institution_id && this.data.academic_class_id) {
                    // this.$root.spinner = true;
                    axios.get(`/get-groups/${this.data.institution_id}/${this.data.academic_class_id}`)
                        .then(response => {
                            this.availableGroups = response.data;

                            // Reset academic_group_id if the current selection is not in available groups
                            if (this.data.academic_group_id &&
                                !this.availableGroups.some(g => g.id == this.data.academic_group_id)) {
                                this.data.academic_group_id = null;
                            }
                        })
                        .catch(error => {
                            console.error(error);
                            this.notify('Failed to load groups', 'error');
                            this.availableGroups = [];
                        })
                        .finally(() => {
                            this.$root.spinner = false;
                        });
                } else {
                    this.availableGroups = [];
                    this.data.academic_group_id = null;
                }
            },
        },
        watch: {
            $route: {
                handler: "asyncData",
                immediate: true,
            },
            'data.institution_id': function (newVal) {
                this.handleInstitutionOrClassChange();
            },

            'data.academic_class_id': function (newVal) {
                this.handleInstitutionOrClassChange();
            }
        },

        // ================== validation rule for form ==================
        validators: {
            "data.academic_class_id": function (value = null) {
                return Validator.value(value).required("Academic Class is required");
            },
        },
    };
</script>
