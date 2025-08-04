<template>
    <div class="card">
        <div class="card-body min-height">
            <form v-on:submit.prevent="search" class="col-12 row mb-3">
                <!------------ Single Input ------------>
                <SelectSearch title="All Status" field="status" :datas="$root.global.status" loop_type="pluck" col="1"
                    class="mb-2" />
                <!------------ Single Input ------------>
                <SelectSearch v-if="!$root.institution_id" title="All Institution" field="institution_id"
                    :datas="$root.global.institutions" val="id" val_title="name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Institution Category" field="institution_category_id"
                    :datas="$root.global.institution_categories" val="id" val_title="name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Session" field="academic_session_id" :datas="sessions_filter(search_data.institution_category_id, 'search_data')
                    " val="id" val_title="name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Campus" field="campus_id" :datas="campuses_filter(search_data.institution_id)"
                    val="id" val_title="name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Shift" field="shift_id"
                    :datas="shift_filter(search_data.institution_id, 'search_data')" val="shift_id"
                    val_title="shift_name" col="1" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Medium" field="medium_id"
                    :datas="medium_filter(search_data.institution_id, 'search_data')" val="medium_id"
                    val_title="medium_name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Class" field="academic_class_id" :datas="category_classes_filter(
                    search_data.institution_id,
                    search_data.institution_category_id,
                    'search_data'
                )
                    " val="academic_class_id" val_title="class_name" />

                <!------------ Single Input ------------>
                <SelectSearch title="All Group" field="group_id"
                    :datas="group_filter(search_data.institution_id, 'search_data')" val="group_id"
                    val_title="group_name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Section" field="section_id"
                    :datas="section_filter(search_data.institution_id, 'search_data')" val="section_id"
                    val_title="section_name" />
                <!------------ Single Input ------------>
                <SelectSearch title="All Gender" field="gender" :datas="[
                    { id: 'Male', name: 'Male' },
                    { id: 'Female', name: 'Female' },
                    { id: 'Others', name: 'Others' },
                ]" val="id" val_title="name" />

                <!-- search -->
                <Search :fields_name="fields_name">
                    <!-- <li
            v-if="table.routes && $root.checkPermission(table.routes.destroy)"
          >
            <a
              href="javascript:void(0)"
              class="dropdown-item"
              @click="$bvModal.show('bulk-delete')"
            >
              - Bulk Delete
            </a>
          </li> -->
                    <!-- <li>
            <a
              href="javascript:void(0)"
              class="dropdown-item"
              @click="downloadZipImage()"
            >
              - Download ZIP Image
            </a>
          </li> -->
                </Search>
            </form>

            <!-- BaseTable -->
            <base-table :data="table.datas" :columns="table.columns" :routes="table.routes">
                <template v-slot:[`student_id`]="row">
                    <td class="text-center">
                        <template v-if="row.item">
                            <img class="img rounded" v-if="row.item.profile.profile" :src="row.item.profile.profile"
                                style="height: 35px" />
                            <img class="img rounded" v-else :src="$root.userimage" style="height: 35px" />
                            <br />
                            {{ row.item.software_id }}
                        </template>
                    </td>
                </template>
                <template v-slot:[`name_en`]="row">
                    <td>
                        <template v-if="row.item">
                            {{ row.item.name_en }} <br />
                            {{ row.item.profile.roll_number }}
                        </template>
                    </td>
                </template>
                <template v-slot:[`blood_group`]="row">
                    <td>
                        <template v-if="row.item">
                            {{ row.item.profile.blood_group }}
                        </template>
                    </td>
                </template>
                <template v-slot:[`guardian`]="row">
                    <td>
                        <template v-if="row.item && row.item.guardian">
                            {{ row.item.guardian.name_en }} <br />
                            {{ row.item.guardian.mobile }}
                        </template>
                    </td>
                </template>
                <template v-slot:[`campus_shift`]="row">
                    <td>
                        {{ row.item.campus.name }} ({{ row.item.shift.name }})
                        <br />
                        {{ row.item.medium.name }}
                    </td>
                </template>
                <template v-slot:[`class_section`]="row">
                    <td>
                        {{ row.item.academic_class?.name }} ({{
                        row.item.academic_session?.name
                        }})
                        <br />
                        {{ row.item.group.name }} ({{ row.item.section.name }})
                    </td>
                </template>
                <template v-slot:[`subjects_count`]="row">
                    <td class="text-center sub-td">
                        <i v-if="row.item.academic_class && (row.item.academic_class.id == 11 || row.item.academic_class.id == 12)"
                            @click="subjectChoiceModal(row.item)" class="bx bxs-edit-alt btn btn-xs btn-outline-primary"
                            title="Add / Edit Subject"></i>
                    </td>
                </template>
            </base-table>

            <!-- Pagination -->
            <div class="box-footer clearfix">
                <Pagination :url="model" :search_data="search_data" v-if="!$root.tableSpinner" />
            </div>
            <!-- Pagination -->
        </div>

        <!-- View Student -->
        <SubjectChoice v-if="Object.keys(student_info).length > 0" :student="student_info" :data="student_subjects" />

        <BulkDelete :lists="table.datas" />
    </div>
</template>

<script>
    import BulkDelete from "@components/backend/elements/Table/BulkDelete.vue";
    import SubjectChoice from "./SubjectChoice.vue";

    // define model name
    const model = "student";

    // Add Or Back
    const addOrBack = {
        route: model + ".create",
        title: model,
        icon: "plus-circle",
    };

    // define table coloumn show in datatable / datalist
    const tableColumns = [
        { field: "student_id", title: "Software ID", width: "9%", align: "center" },
        { field: "name_en", title: "Student" },
        { field: "blood_group", title: "Blood Group" },
        { field: "guardian", title: "Guardian" },
        { field: "campus_shift", title: "Campus/Shift Info" },
        { field: "class_section", title: "Class/Section Info" },
        {
            field: "subjects_count",
            title: "Subjects",
            align: "center",
            width: "7%",
        },
        {
            field: "status",
            title: "Status",
            align: "center",
            width: "5%",
        },
    ];

    //json fields for export excel
    const json_fields = {
        Institution: "institution.name",
        Campus: "campus.name",
        Shift: "shift.name",
        "Medium/Version": "medium.name",
        "Academic Class": "academic_class.name",
        Group: "group.name",
        Section: "section.name",

        "Software ID": "software_id",
        "Roll Number": "profile.roll_number",
        "Name (en)": "name_en",
        "Name (bn)": "name_bn",
        "NID/Birth Reg No": "profile.nid_or_birth_reg",
        "Date of Birth": "profile.dob",
        Religion: "profile.religion",
        Gender: "profile.gender",
        Disability: "profile.disability",

        "Fathers Name (en)": "profile.fathers_name_en",
        "Fathers Name (bn)": "profile.fathers_name_bn",
        "Fathers Mobile": "profile.fathers_mobile",
        "Fathers NID/Birth Reg No": "profile.fathers_nid_or_birth_reg",

        "Mothers Name (en)": "profile.mothers_name_en",
        "Mothers Name (bn)": "profile.mothers_name_bn",
        "Mothers Mobile": "profile.mothers_mobile",
        "Mothers NID/Birth Reg No": "profile.mothers_nid_or_birth_reg",

        Mobile: "mobile",
        Email: "email",
        "Present Address": "profile.address",
        "Permanent Address": "profile.permanent_address",

        "Guardian Type": "guardian.type",
        "Guardian Relations": "guardian.relations",
        "Guardian Name (en)": "guardian.name_en",
        "Guardian Name (bn)": "guardian.name_bn",
        "Guardian NID/Birth Reg No": "guardian.nid_or_birth_reg",
        "Guardian Mobile": "guardian.mobile",
        "Guardian Email": "guardian.email",
    };

    export default {
        components: { BulkDelete, SubjectChoice },

        data() {
            return {
                model: model,
                json_fields: json_fields,
                download_spinner: false,
                fields_name: {
                    0: "Select One",
                    software_id: "Software ID",
                    name_en: "Name (en)",
                    blood_group: "Blood group",
                    mobile: "Student Mobile",
                    roll_number: "Roll",
                    guardian_mobile: "Guardian Mobile",
                },
                search_data: {
                    pagination: 10,
                    field_name: "software_id",
                    value: "",
                    institution_id: "",
                    institution_category_id: "",
                    academic_session_id: "",
                    campus_id: "",
                    medium_id: "",
                    group_id: "",
                    section_id: "",
                    shift_id: "",
                    academic_class_id: "",
                    gender: "",
                    status: "active",
                },
                table: {
                    columns: tableColumns,
                    routes: {},
                    datas: [],
                    meta: [],
                    links: [],
                },
                student_info: {},
                student_subjects: {},
            };
        },

        methods: {
            subjectChoiceModal(student) {
                this.student_info = student;
                console.log(student);

                this.getClasswiseSubject(student.id);
                setTimeout(() => {
                    this.$bvModal.show("student-subject-choice");
                }, 200);
            },

            getClasswiseSubject(id) {
                axios
                    .get(`get-student-subjects/${id}`)
                    .then((res) => {
                        console.log(res.data);
                        this.student_subjects = res.data;
                    })
                    .catch((error) => {
                        console.error('Error fetching student subjects:', error);
                    });
            },

            destroy(id) {
                this.destroy_data(this.model, id, this.search_data);
            },

            search() {
                this.get_paginate_data(this.model, this.search_data);
            },

            downloadZipImage() {
                if (confirm("Are you sure want to download ZIP image?")) {
                    this.notify(
                        "Please wait a moment, ZIP file is getting ready for download",
                        "success"
                    );
                    axios
                        .post("/download-student-zip-image", this.search_data)
                        .then((res) => {
                            if (res.status == 200) {
                                window.location = res.data;
                            }
                        })
                        .catch((error) => {
                            if (error.response.status === 422) {
                                if (error.response.data.exception) {
                                    this.$bvModal.show("validate-error");
                                    this.$root.exception_errors = error.response.data.exception;
                                }
                            }
                        });
                }
            },
        },

        created() {
            this.getRouteName(this.model);
            this.setBreadcrumbs(this.model, "index", null, addOrBack);
            this.get_paginate_data(this.model, this.search_data);
        },

        mounted() {
            if (this.$root.institution_id) {
                this.search_data.institution_id = this.$root.institution_id;
            }
        },
    };
</script>
