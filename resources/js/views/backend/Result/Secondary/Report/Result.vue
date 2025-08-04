<template>
    <div v-if="!$root.spinner" class="col-12">
        <SearchParams :data="data" :result_type="true" :category="2" @search-event="search" :validation="validation" />

        <!-- Student List -->
        <div class="card border">
            <div class="card-header">
                <div class="row">
                    <div class="col-4">
                        <h6 class="mb-0 text-uppercase">Students Result</h6>
                    </div>
                    <div v-if="Object.keys(data.details).length > 0" class="col-8">
                        <PrintDownload :download_marksheet="true" />
                    </div>
                </div>
            </div>

            <div v-if="Object.keys(data.details).length > 0" id="printArea">
                <div class="card-body">
                    <InstitutionInfo :data="data" :title="data.exam ? data.exam.name : ''"
                        :sub_title="merit_types[type]" />

                    <div class="table-fixed printArea">
                        <table class="table table-bordered table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Software ID</th>
                                    <th>Student Name</th>
                                    <th class="text-center">Roll Number</th>
                                    <template v-if="type == 'unmerit'">
                                        <th class="text-center">Total Failed</th>
                                        <th>Failed Subjects</th>
                                    </template>
                                    <template v-else>
                                        <th class="text-center">Merit Position</th>
                                    </template>
                                    <th class="text-center">Total Mark</th>
                                    <th class="text-center">GRADE</th>
                                    <th class="text-center">GPA</th>
                                    <th class="text-center" style="width: 10%">Status</th>
                                </tr>
                            </thead>
                            <tbody v-if="data.details && Object.keys(data.details).length > 0">
                                <tr v-for="(std, key) in data.details" :key="key">
                                    <th class="text-center">{{ key + 1 }}</th>
                                    <td>
                                        <router-link :to="{
                                            name: 'secondaryResult.marksheet',
                                            params: { id: std.id },
                                        }" target="_blank">
                                            {{ std.software_id }}
                                        </router-link>
                                    </td>
                                    <td>{{ std.name_en }}</td>
                                    <td class="text-center">{{ std.roll_number }}</td>
                                    <template v-if="type == 'unmerit'">
                                        <td class="text-center">
                                            <span v-if="std.marks && Object.keys(std.marks).length > 0">
                                                {{ Object.keys(std.marks).length }}
                                            </span>
                                        </td>
                                        <td>
                                            <ul class="p-2" v-if="std.marks && Object.keys(std.marks).length > 0">
                                                <li class="p-0" v-for="(sub, markKey) in std.marks"
                                                    :key="'markKey' + markKey">
                                                    {{ sub.subject ? sub.subject.name_en : "" }}
                                                </li>
                                            </ul>
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td class="text-center">
                                            {{ type ? std[type] : std.merit_position_in_section }}
                                        </td>
                                    </template>
                                    <td class="text-center">
                                        {{ std.total_mark_without_additional }}
                                    </td>
                                    <td class="text-center">{{ std.letter_grade }}</td>
                                    <td class="text-center">{{ std.gpa }}</td>
                                    <td class="text-center">
                                        <span :class="std.result_status == 'PASSED'
                                                ? activeClass()
                                                : errorClass()
                                            ">
                                            <b>
                                                <i class="bx bxs-circle me-1"></i>
                                                {{ std.result_status }}
                                            </b>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                            <tbody v-else>
                                <tr>
                                    <td colspan="20" class="text-center">No data Found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <span v-else class="text-center py-5">No result found.</span>
        </div>
    </div>
</template>

<script>
    import InstitutionInfo from "@views/backend/Result/elements/InstitutionInfo";
    import SearchParams from "@views/backend/Result/elements/SearchParams";
    import PrintDownload from "@views/backend/Result/elements/PrintDownload";

    // define model name
    const model = "secondaryResult";

    // Add Or Back
    const addOrBack = {
        route: model + ".index",
        title: model,
        icon: "left-arrow-alt",
    };

    //json fields for export excel
    const json_fields = {
        "Software ID": "software_id",
        "Roll Number": "roll_number",
        Name: "name_en",
        "Total Mark": "total_mark",
        gpa: "gpa",
        "Letter Grade": "letter_grade",
        "Total Failed": {
            callback: (std) => {
                let failed = 0;
                if (std.marks && Object.keys(std.marks).length > 0) {
                    failed = Object.keys(std.marks).length;
                }
                return `${failed} `;
            },
        },
        "Failed Subjects": {
            callback: (std) => {
                let subjects = "";
                if (std.marks && Object.keys(std.marks).length > 0) {
                    std.marks.forEach((sub) => {
                        let subjectName = sub.subject ? sub.subject.name_en : "";
                        subjects += subjectName + ", ";
                    });
                }
                return `${subjects}`;
            },
        },
    };

    export default {
        components: { InstitutionInfo, SearchParams, PrintDownload },

        data() {
            return {
                model: model,
                search_filter: true,
                excel_header: [],
                json_fields: json_fields,
                fields_name: {
                    software_id: "Software ID",
                    name_en: "Name",
                    roll_number: "Roll Number",
                },
                type: "",
                field_name: "roll_number",
                search_keyword: "",
                data: {
                    academic_session_id: null,
                    institution_id: null,
                    campus_id: null,
                    shift_id: null,
                    medium_id: null,
                    academic_class_id: null,
                    group_id: null,
                    section_id: null,
                    exam_id: null,
                    details: [],
                },
                exam_lists: [],
                subjects: [],
                merit_types: {
                    merit_position_in_shift: "Shift Wise Merit",
                    merit_position_in_class: "Class Wise Merit",
                    merit_position_in_section: "Section Wise Merit",
                    unmerit: "Unmerit",
                },
            };
        },

        methods: {
            search() {
                if (this.$root.submit) return false;

                this.$validate().then((res) => {
                    if (res) {
                        this.$root.submit = true;

                        let params = {
                            institution_id: this.data.institution_id,
                            academic_session_id: this.data.academic_session_id,
                            campus_id: this.data.campus_id,
                            shift_id: this.data.shift_id,
                            medium_id: this.data.medium_id,
                            academic_class_id: this.data.academic_class_id,
                            group_id: this.data.group_id,
                            section_id: this.data.section_id,
                            exam_id: this.data.exam_id,
                            type: this.type,
                            field_name: this.field_name,
                            search_keyword: this.search_keyword,
                        };

                        axios
                            .get("/secondaryResult-report", { params: params })
                            .then((res) => {
                                if (res.status == 200) {
                                    this.search_filter = false;
                                    this.data = res.data.result;

                                    if (res.data.excel_header) {
                                        this.excel_header = res.data.excel_header;
                                    }
                                }
                            })
                            .catch((err) => console.log(err))
                            .finally((alw) => (this.$root.submit = false));
                    }
                });
            },

            downloadBulkMarksheet() {
                this.$validate().then((res) => {
                    if (res) {
                        this.$root.submit = true;
                        if (confirm("Are you sure want to download?")) {
                            let params = {
                                institution_id: this.data.institution_id,
                                academic_session_id: this.data.academic_session_id,
                                campus_id: this.data.campus_id,
                                shift_id: this.data.shift_id,
                                medium_id: this.data.medium_id,
                                academic_class_id: this.data.academic_class_id,
                                group_id: this.data.group_id,
                                section_id: this.data.section_id,
                                exam_id: this.data.exam_id,
                                type: this.type,
                            };

                            let jsonStr = JSON.stringify(params);
                            let url = `${this.$root.baseurl}/admin/secondary-download-bulk-marksheet?search_params=${jsonStr}`;
                            window.location.href = url;
                            this.notify("Please wait a moment", "success");
                            setTimeout(() => (this.$root.submit = false), 2500);
                        }
                    }
                });
            },

            activeClass() {
                return "badge rounded-pill text-success bg-light-success p-1 text-uppercase px-3 w-100";
            },
            errorClass() {
                return "badge rounded-pill text-danger bg-light-danger p-1 text-uppercase px-3 w-100";
            },
        },

        created() {
            this.setBreadcrumbs(this.model, "view", "Secondary Result", addOrBack);

            this.get_data("get-exam", "exam_lists", {
                allData: true,
                exam_type: "term",
                institution_category_id: 2,
            });
        },

        async mounted() {
            if (this.$root.institution_id) {
                this.data.institution_id = this.$root.institution_id;
            }

            if (this.$route.query.result_id) {
                this.$root.spinner = true;
                let url = `${this.model}/${this.$route.query.result_id}`;
                const res = await this.callApi("get", url);
                if (res.status == 200) {
                    this.data = res.data;
                    this.data.details = [];
                }
                this.$root.spinner = false;
            }
        },

        // ================== validation rule for form ==================
        validators: {
            "data.academic_session_id": function (value = null) {
                return Validator.value(value).required("Session is required");
            },
            "data.institution_id": function (value = null) {
                return Validator.value(value).required("Institution is required");
            },
            "data.campus_id": function (value = null) {
                return Validator.value(value).required("Campus is required");
            },
            "data.shift_id": function (value = null) {
                return Validator.value(value).required("Shift is required");
            },
            "data.medium_id": function (value = null) {
                return Validator.value(value).required("Medium is required");
            },
            "data.academic_class_id": function (value = null) {
                return Validator.value(value).required("Academic Class is required");
            },
            "data.group_id": function (value = null) {
                return Validator.value(value).required("Group is required");
            },
            "data.section_id": function (value = null) {
                return Validator.value(value).required("Section is required");
            },
            "data.exam_id": function (value = null) {
                return Validator.value(value).required("Exam is required");
            },
        },
    };
</script>
