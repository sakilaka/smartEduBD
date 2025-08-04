<template>
    <form v-if="!$root.spinner" v-on:submit.prevent="submit" id="form">
        <div class="row">
            <div class="col-xl-9">
                <div class="card border">
                    <div class="card-body p-3">
                        <div class="row g-3">

                            <div class="col-6">
                                <Label title="Institution" :condition="validation.hasError('data.institution_id')"
                                    :msg="validation.firstError('data.institution_id')" />
                                <v-select v-model="data.institution_id" label="name" :reduce="(obj) => obj.id"
                                    :options="$root.global.institutions" placeholder="--Select Any--"
                                    :closeOnSelect="true" @input="handleInstitutionChange">
                                </v-select>
                            </div>

                            <Input title="Name" field="name" type="text" :req="true" />
                            <Input title="Father Name" field="father_name" type="text" :req="true" />
                            <Input title="Mobile" field="mobile" type="number" :req="false" />
                            <Input title="Email" field="email" type="email" :req="true" />
                            <Input title="Password" field="password" type="password" :req="true" />

                            <div class="col-6">
                                <Label title="Designation"
                                    :condition="validation.hasError('data.teacher.designation_id')"
                                    :msg="validation.firstError('data.teacher.designation_id')" />
                                <v-select v-model="data.teacher.designation_id" label="name" :reduce="(obj) => obj.id"
                                    :options="extraData.designations" placeholder="--Select Any--"
                                    :closeOnSelect="true"></v-select>
                            </div>

                            <div class="col-6">
                                <Label title="Index/ID Number" />
                                <input class="form-control form-control-sm" v-model="data.teacher.index_number"
                                    name="index_number" placeholder="Index/ID Number" />
                            </div>

                            <div class="col-6">
                                <Label title="Date of Birth" />
                                <date-picker v-model="data.teacher.date_of_birth" valueType="format"
                                    format="YYYY-MMM-DD" :formatter="momentFormat"
                                    placeholder="Select Date"></date-picker>
                            </div>

                            <div class="col-6">
                                <Label title="Present Address" />
                                <textarea class="form-control form-control-sm" v-model="data.teacher.present_address"
                                    name="present_address" placeholder="Present Address"></textarea>
                            </div>

                            <div class="col-6">
                                <Label title="Permanent Address" />
                                <textarea class="form-control form-control-sm" v-model="data.teacher.permanent_address"
                                    name="permanent_address" placeholder="Permanent Address"></textarea>
                            </div>

                            <div class="col-6">
                                <Label title="Joining Date As Lecturer" />
                                <date-picker v-model="data.teacher.joining_date_lecturer" valueType="format"
                                    format="YYYY-MMM-DD" :formatter="momentFormat"
                                    placeholder="Select Date"></date-picker>
                            </div>

                            <Radio title="Type" field="type" statusTitle1="Teacher" statusTitle2="Staff"
                                value1="Teacher" value2="Staff" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="col-xl-3">
                <ButtonStatus :save_and_edit="false" />
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <!-- Subjects Assign -->
                <div v-if="data.type == 'Teacher'" class="card">
                    <div class="card-header">
                        <h6 class="mb-0 text-uppercase">Subject Assign</h6>
                    </div>
                    <div class="card-body p-3">
                        <!-- <SubjectAssign :key="subjectAssignKey" :institutionId="data.institution_id" /> -->
                        <SubjectAssign
    ref="subjectAssign"
    :key="subjectAssignKey"
    :institutionId="data.institution_id"
/>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
    import SubjectAssign from "./SubjectAssign.vue";

    const model = "teacher";

    const addOrBack = {
        route: model + ".index",
        title: model,
        icon: "left-arrow-alt",
    };

    export default {
        components: {
            Radio: () => import("./../../../../components/backend/elements/Form/Radio"),
            SubjectAssign,
        },

        data() {
            return {
                institution_id: null,
                model: model,
                submitType: "",
                subjectAssignKey: 0,
                data: {
                    role_id: 9,
                    teacher: { designation_id: null },
                    status: "active",
                    subject_assigns: [{
                        campus_id: null,
                        shift_id: null,
                        medium_id: null,
                        group_id: null,
                        section_id: null,
                        institution_categories_id: null,
                        academic_class_id: null,
                        subject_id: null,
                        status: "active",
                        filteredSubjects: []
                    }],
                    type: "Teacher",
                    institution_id: null
                },
                extraData: {
                    designations: [],
                    subjects: [],
                },
            };
        },

        methods: {
            submit: function () {
                this.$validate().then((res) => {
                    const error = this.validation.countErrors();
                    if (error > 0) {
                        console.log(this.validation.allErrors());
                        this.notify(error, "validate");
                        return false;
                    }

                    if (res) {
                        if (this.data.id) {
                            this.update(this.model, this.data, this.data.id);
                        } else {
                            this.store(this.model, this.data, this.submitType);
                        }
                    }
                });
            },

            handleInstitutionChange(newVal) {
                this.subjectAssignKey++; // Force re-render of SubjectAssign component
                if (this.data.type === 'Teacher') {
                    this.initializeSubjectAssigns();
                }
            },

            initializeSubjectAssigns() {
                this.data.subject_assigns = [this.getDefaultSubjectAssign()];
            },

            getDefaultSubjectAssign() {
                return {
                    campus_id: null,
                    shift_id: null,
                    medium_id: null,
                    group_id: null,
                    section_id: null,
                    institution_categories_id: null,
                    academic_class_id: null,
                    subject_id: null,
                    status: "active",
                    filteredSubjects: []
                };
            },

            async asyncData() {
                let page = "create";
                if (this.$route.params.id) {
                    page = "edit";
                    await this.get_data(`${this.model}/${this.$route.params.id}`, "data");

                    // Ensure subject_assigns is properly initialized
                    if (!Array.isArray(this.data.subject_assigns) || this.data.subject_assigns.length === 0) {
                        this.initializeSubjectAssigns();
                    } else {
                        // For each existing subject assign, fetch the groups if class is set
                        for (const item of this.data.subject_assigns) {
                            if (item.academic_class_id) {
                                await this.$nextTick(); // Wait for the component to be ready
                                // Trigger the fetchGroups for each item
                                this.$refs.subjectAssign?.fetchGroups(item);
                            }
                        }
                    }
                } else {
                    this.initializeSubjectAssigns();
                }
                this.setBreadcrumbs(this.model, page, null, addOrBack);
            }
        },

        mounted() {
            this.get_paginate_data(
                "get-designation",
                { allData: true },
                "designations"
            );
            this.get_paginate_data("subject", { allData: true }, "subjects");
        },

        watch: {
            'data.type': {
                handler(newVal) {
                    if (newVal === 'Teacher') {
                        this.initializeSubjectAssigns();
                    } else {
                        this.data.subject_assigns = [];
                    }
                },
                immediate: true
            },
            $route: {
                handler: "asyncData",
                immediate: true,
            },
        },

        validators: {
            "data.type": function (value = null) {
                return Validator.value(value).required("Type is required");
            },
            "data.name": function (value = null) {
                return Validator.value(value).required("Name is required");
            },
            "data.email": function (value = null) {
                return Validator.value(value).required("Email is required").email();
            },
            "data.password": function (value = null) {
                if (!this.$route.params.id) {
                    return Validator.value(value)
                        .required("Password is required")
                        .minLength(6);
                }
                return Validator.value(value);
            },
            "data.mobile": function (value = null) {
                return Validator.value(value)
                    .digit()
                    .regex("01+[0-9+-]*$", "Must start with 01.")
                    .minLength(11)
                    .maxLength(11);
            },
        },
    };
</script>
