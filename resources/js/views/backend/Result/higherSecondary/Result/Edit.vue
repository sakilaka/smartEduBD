<template>
    <div v-if="!$root.spinner" class="col-xl-12">
        <form v-if="data.status != 'published'" id="form" class="row">
            <div class="card border">
                <div class="card-header">
                    <h6 class="mb-0 text-uppercase">Search Students</h6>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        <!------------ Single Input ------------>
                        <SelectCustom title="Session" field="academic_session_id" :datas="sessions_filter(3)"
                            :req="true" col="3" val="id" val_title="name" />
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
                            :datas="category_classes_filter(data.institution_id, 2)" col="3" val="academic_class_id"
                            val_title="class_name" />
                        <!------------ Single Input ------------>
                        <SelectCustom title="Group" field="group_id" :req="true"
                            :datas="group_filter(data.institution_id)" col="3" val="group_id" val_title="group_name" />
                        <!------------ Single Input ------------>
                        <SelectCustom title="Section" field="section_id" :req="true"
                            :datas="section_filter(data.institution_id)" col="3" val="section_id"
                            val_title="section_name" />
                        <div class="w-100 m-0"></div>
                        <!------------ Single Input ------------>
                        <Select title="Exam" field="exam_id" :req="true" :datas="extraData.exam_lists" :col="2"
                            :disable="disable_master_data" />
                        <!------------ Single Input ------------>
                        <Input title="Exam Subjects" type="number" field="total_exam_subjects" :col="2" :req="true" />
                        <div class="col-1 text-end pt-4">
                            <button type="button" v-if="$root.checkPermission('result.syncResult')"
                                @click="syncResult($route.params.id)" title="Sync" class="btn btn-success btn-xs"
                                :disabled="sync_spinner">
                                <span v-if="sync_spinner" class="spinner-border spinner-border-sm"></span>
                                <span v-else>
                                    <i class="bx bx-sync me-0"></i>
                                    SYNC
                                </span>
                            </button>
                        </div>
                        <div class="w-100"></div>
                        <!------------ Single Input ------------>
                        <div class="col-2">
                            <Label title="Convert Mark" :req="false" />
                            <input v-model="convert_mark" type="radio" value="1" />
                            Yes &nbsp; &nbsp;
                            <input v-model="convert_mark" type="radio" value="0" />
                            No
                        </div>
                        <!------------ Single Input ------------>
                        <div class="col-2">
                            <Label title="Select Subject" :req="true" :condition="validation.hasError('subject_id')"
                                :msg="validation.firstError('subject_id')" />
                            <select @change="selectSubject($event.target.value)" v-model="subject_id"
                                class="form-select form-select-sm" :class="reqClass('subject_id')">
                                <option :value="null">--Select Any--</option>
                                <option v-for="(subject, key) in extraData.subjects" :key="key"
                                    v-bind:value="subject.subject_id">
                                    {{ subject.subject ? subject.subject.name_en : "" }}
                                </option>
                            </select>
                        </div>
                        <!------------ Button ------------>
                        <div class="col-1 pt-3">
                            <button type="button" @click="getStudents()" class="btn btn-sm btn-success"
                                :disabled="$root.tableSpinner ? true : false">
                                <span v-if="$root.tableSpinner">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                                <span v-else> <i class="bx bx-search mx-0 fw-bold"></i></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student List -->
            <!-- Student List -->
            <div class="card border">
                <div class="card-header">
                    <div class="row">
                        <div class="col-2">
                            <h6 class="mb-0 text-uppercase">Marks Entry</h6>
                        </div>
                        <div class="col-10">
                            <button v-if="subject_id" type="button" @click="getStudents()"
                                class="btn btn-xs btn-primary ms-3">
                                <span v-if="$root.tableSpinner">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                                <span class="float-end" v-else>
                                    <i class="bx bx-refresh"></i>
                                    Refresh Marks
                                </span>
                            </button>

                            <!-- <button v-if="subject_id" type="button" @click="submit()"
                                class="btn btn-xs btn-success float-end">
                                <span v-if="$root.submit">
                                    <span class="spinner-border spinner-border-sm"></span>
                                </span>
                                <span> <i class="bx bx-save"></i> SUBMIT MARKS </span>
                            </button> -->
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row g-3 table-fixed">
                        <table class="table table-bordered table-hover table-striped mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Software ID</th>
                                    <th>Student Name</th>
                                    <th>College Roll</th>
                                    <th>Subject Name</th>
                                    <th class="text-center">
                                        CT <small>({{ subject.ct_mark }})</small>
                                    </th>
                                    <th class="text-center">
                                        CQ
                                        <small>({{ subject.cq_mark }})</small>
                                    </th>
                                    <th class="text-center">
                                        MCQ
                                        <small>({{ subject.mcq_mark }})</small>
                                    </th>
                                    <th class="text-center">
                                        PRAC
                                        <small>({{ subject.practical_mark }})</small>
                                    </th>
                                    <th class="text-center">OBTAINED</th>
                                    <th class="text-center">TOTAL</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center"></th>
                                </tr>
                            </thead>


                            <tbody v-if="Object.keys(data.details).length > 0 && subject_id">
                                <template v-for="(student, studentIndex) in data.details">
                                    <!-- For subjects with child subjects -->
                                    <template v-if="hasChildSubjects">
                                        <!-- First row with student info and first child subject -->
                                        <tr :key="'student-' + studentIndex + '-child-0'">
                                            <td class="text-center" :rowspan="childSubjects.length">{{ studentIndex + 1
                                                }}</td>
                                            <td :rowspan="childSubjects.length">
                                                {{ student.student ? student.student.student_id : student.student_id }}
                                            </td>
                                            <td :rowspan="childSubjects.length">
                                                {{ student.student ? student.student.name : student.name }}
                                            </td>
                                            <td :rowspan="childSubjects.length">
                                                {{ student.student ? student.student.college_roll : student.college_roll
                                                }}
                                            </td>
                                            <td>
                                                {{ childSubjects[0].name }}
                                            </td>
                                            <td class="align-center">
                                                <input v-if="student.marks"
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, 0)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, 0)"
                                                    @keyup="marksCalculate(studentIndex, 'ct_mark', 0)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'ct_mark', 0)"
                                                    :id="`ct_mark_${studentIndex}_0`" type="text"
                                                    v-model="student.marks[0].ct_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :disabled="class_test_marks_added ? true : false"
                                                    :readonly="parseInt(subject.ct_mark) && convert_mark ? false : true" />
                                            </td>
                                            <!-- Other mark columns for first child subject -->
                                            <td class="align-center">
                                                <input
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, 0)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, 0)"
                                                    @keyup="marksCalculate(studentIndex, 'cq_mark', 0)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'cq_mark', 0)"
                                                    :id="`cq_mark_${studentIndex}_0`" type="text"
                                                    v-model="student.marks[0].cq_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :readonly="parseInt(subject.cq_mark) && convert_mark ? false : true" />
                                            </td>
                                            <td class="align-center">
                                                <input
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, 0)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, 0)"
                                                    @keyup="marksCalculate(studentIndex, 'mcq_mark', 0)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'mcq_mark', 0)"
                                                    :id="`mcq_mark_${studentIndex}_0`" type="text"
                                                    v-model="student.marks[0].mcq_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :readonly="parseInt(subject.mcq_mark) && convert_mark ? false : true" />
                                            </td>
                                            <td class="align-center">
                                                <input
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, 0)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, 0)"
                                                    @keyup="marksCalculate(studentIndex, 'practical_mark', 0)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'practical_mark', 0)"
                                                    :id="`practical_mark_${studentIndex}_0`" type="text"
                                                    v-model="student.marks[0].practical_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :readonly="parseInt(subject.practical_mark) && convert_mark ? false : true" />
                                            </td>
                                            <td class="align-center">
                                                <input type="text" v-model="student.marks[0].obtained_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    readonly />
                                            </td>
                                            <td class="align-center">
                                                <input type="text" v-model="student.marks[0].total_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    readonly />
                                            </td>
                                            <td class="text-center">
                                                <input
                                                    @click="mark_submit_icon_key = studentIndex; activeChildIndex = 0"
                                                    :value="1" type="radio" :name="`is_absent_${studentIndex}`"
                                                    v-model="student.marks[0].is_absent" />
                                                Yes &nbsp;
                                                <input
                                                    @click="mark_submit_icon_key = studentIndex; activeChildIndex = 0"
                                                    :value="0" type="radio" :name="`is_absent_${studentIndex}`"
                                                    v-model="student.marks[0].is_absent" />
                                                No
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    v-if="$root.submit && spinner_key == studentIndex && activeChildIndex == 0">
                                                    <span class="spinner-border spinner-border-sm"></span>
                                                </span>
                                                <i v-else-if="parseInt(mark_submit_icon_key) == parseInt(studentIndex) && activeChildIndex == 0"
                                                    @click="marksSubmit(studentIndex, 0)"
                                                    class="bx bxs-check-square btn btn-xs text-white btn-success"></i>
                                            </td>
                                        </tr>

                                        <!-- Additional rows for remaining child subjects -->
                                        <tr v-for="(child, childIndex) in childSubjects.slice(1)"
                                            :key="'student-' + studentIndex + '-child-' + (childIndex + 1)">
                                            <td>
                                                {{ child.name }}
                                            </td>
                                            <td class="align-center">
                                                <input v-if="student.marks"
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, childIndex + 1)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, childIndex + 1)"
                                                    @keyup="marksCalculate(studentIndex, 'ct_mark', childIndex + 1)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'ct_mark', childIndex + 1)"
                                                    :id="`ct_mark_${studentIndex}_${childIndex + 1}`" type="text"
                                                    v-model="student.marks[childIndex + 1].ct_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :disabled="class_test_marks_added ? true : false"
                                                    :readonly="parseInt(subject.ct_mark) && convert_mark ? false : true" />
                                            </td>
                                            <!-- Other mark columns for additional child subjects -->
                                            <td class="align-center">
                                                <input
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, childIndex + 1)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, childIndex + 1)"
                                                    @keyup="marksCalculate(studentIndex, 'cq_mark', childIndex + 1)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'cq_mark', childIndex + 1)"
                                                    :id="`cq_mark_${studentIndex}_${childIndex + 1}`" type="text"
                                                    v-model="student.marks[childIndex + 1].cq_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :readonly="parseInt(subject.cq_mark) && convert_mark ? false : true" />
                                            </td>
                                            <td class="align-center">
                                                <input
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, childIndex + 1)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, childIndex + 1)"
                                                    @keyup="marksCalculate(studentIndex, 'mcq_mark', childIndex + 1)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'mcq_mark', childIndex + 1)"
                                                    :id="`mcq_mark_${studentIndex}_${childIndex + 1}`" type="text"
                                                    v-model="student.marks[childIndex + 1].mcq_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :readonly="parseInt(subject.mcq_mark) && convert_mark ? false : true" />
                                            </td>
                                            <td class="align-center">
                                                <input
                                                    v-tab-pressed="(event) => handleTabPressed(studentIndex, event, childIndex + 1)"
                                                    v-on:keyup.enter="marksSubmit(studentIndex, childIndex + 1)"
                                                    @keyup="marksCalculate(studentIndex, 'practical_mark', childIndex + 1)"
                                                    @keydown="handleArrowNavigation($event, studentIndex, 'practical_mark', childIndex + 1)"
                                                    :id="`practical_mark_${studentIndex}_${childIndex + 1}`" type="text"
                                                    v-model="student.marks[childIndex + 1].practical_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    :readonly="parseInt(subject.practical_mark) && convert_mark ? false : true" />
                                            </td>
                                            <td class="align-center">
                                                <input type="text" v-model="student.marks[childIndex + 1].obtained_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    readonly />
                                            </td>
                                            <td class="align-center">
                                                <input type="text" v-model="student.marks[childIndex + 1].total_mark"
                                                    class="form-control form-control-sm text-center" style="width: 80px"
                                                    readonly />
                                            </td>
                                            <td class="text-center">
                                                <input
                                                    @click="mark_submit_icon_key = studentIndex; activeChildIndex = childIndex + 1"
                                                    :value="1" type="radio"
                                                    :name="`is_absent_${studentIndex}_${childIndex + 1}`"
                                                    v-model="student.marks[childIndex + 1].is_absent" />
                                                Yes &nbsp;
                                                <input
                                                    @click="mark_submit_icon_key = studentIndex; activeChildIndex = childIndex + 1"
                                                    :value="0" type="radio"
                                                    :name="`is_absent_${studentIndex}_${childIndex + 1}`"
                                                    v-model="student.marks[childIndex + 1].is_absent" />
                                                No
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    v-if="$root.submit && spinner_key == studentIndex && activeChildIndex == childIndex + 1">
                                                    <span class="spinner-border spinner-border-sm"></span>
                                                </span>
                                                <i v-else-if="parseInt(mark_submit_icon_key) == parseInt(studentIndex) && activeChildIndex == childIndex + 1"
                                                    @click="marksSubmit(studentIndex, childIndex + 1)"
                                                    class="bx bxs-check-square btn btn-xs text-white btn-success"></i>
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- For subjects without child subjects -->
                                    <tr v-else :key="'student-' + studentIndex">
                                        <td class="text-center">{{ studentIndex + 1 }}</td>
                                        <td>
                                            {{ student.student ? student.student.student_id : student.student_id }}
                                        </td>
                                        <td>
                                            {{ student.student ? student.student.name : student.name }}
                                        </td>
                                        <td>
                                            {{ student.student ? student.student.college_roll : student.college_roll }}
                                        </td>
                                        <td>
                                            {{ subject.subject ? subject.subject.name_en : "" }}
                                        </td>
                                        <td class="align-center">
                                            <input v-if="student.marks"
                                                v-tab-pressed="(event) => handleTabPressed(studentIndex, event)"
                                                v-on:keyup.enter="marksSubmit(studentIndex)"
                                                @keyup="marksCalculate(studentIndex, 'ct_mark')"
                                                @keydown="handleArrowNavigation($event, studentIndex, 'ct_mark')"
                                                :id="`ct_mark_${studentIndex}`" type="text"
                                                v-model="student.marks[0].ct_mark"
                                                class="form-control form-control-sm text-center" style="width: 80px"
                                                :disabled="class_test_marks_added ? true : false"
                                                :readonly="parseInt(subject.ct_mark) && convert_mark ? false : true" />
                                        </td>
                                        <td class="align-center">
                                            <input v-tab-pressed="(event) => handleTabPressed(studentIndex, event)"
                                                v-on:keyup.enter="marksSubmit(studentIndex)"
                                                @keyup="marksCalculate(studentIndex, 'cq_mark')"
                                                @keydown="handleArrowNavigation($event, studentIndex, 'cq_mark')"
                                                :id="`cq_mark_${studentIndex}`" type="text"
                                                v-model="student.marks[0].cq_mark"
                                                class="form-control form-control-sm text-center" style="width: 80px"
                                                :readonly="parseInt(subject.cq_mark) && convert_mark ? false : true" />
                                        </td>
                                        <td class="align-center">
                                            <input v-tab-pressed="(event) => handleTabPressed(studentIndex, event)"
                                                v-on:keyup.enter="marksSubmit(studentIndex)"
                                                @keyup="marksCalculate(studentIndex, 'mcq_mark')"
                                                @keydown="handleArrowNavigation($event, studentIndex, 'mcq_mark')"
                                                :id="`mcq_mark_${studentIndex}`" type="text"
                                                v-model="student.marks[0].mcq_mark"
                                                class="form-control form-control-sm text-center" style="width: 80px"
                                                :readonly="parseInt(subject.mcq_mark) && convert_mark ? false : true" />
                                        </td>
                                        <td class="align-center">
                                            <input v-tab-pressed="(event) => handleTabPressed(studentIndex, event)"
                                                v-on:keyup.enter="marksSubmit(studentIndex)"
                                                @keyup="marksCalculate(studentIndex, 'practical_mark')"
                                                @keydown="handleArrowNavigation($event, studentIndex, 'practical_mark')"
                                                :id="`practical_mark_${studentIndex}`" type="text"
                                                v-model="student.marks[0].practical_mark"
                                                class="form-control form-control-sm text-center" style="width: 80px"
                                                :readonly="parseInt(subject.practical_mark) && convert_mark ? false : true" />
                                        </td>
                                        <td class="align-center">
                                            <input type="text" v-model="student.marks[0].obtained_mark"
                                                class="form-control form-control-sm text-center" style="width: 80px"
                                                readonly />
                                        </td>
                                        <td class="align-center">
                                            <input type="text" v-model="student.marks[0].total_mark"
                                                class="form-control form-control-sm text-center" style="width: 80px"
                                                readonly />
                                        </td>
                                        <td class="text-center">
                                            <input @click="mark_submit_icon_key = studentIndex" :value="1" type="radio"
                                                :name="`is_absent_${studentIndex}`"
                                                v-model="student.marks[0].is_absent" />
                                            Yes &nbsp;
                                            <input @click="mark_submit_icon_key = studentIndex" :value="0" type="radio"
                                                :name="`is_absent_${studentIndex}`"
                                                v-model="student.marks[0].is_absent" />
                                            No
                                        </td>
                                        <td class="text-center">
                                            <span v-if="$root.submit && spinner_key == studentIndex">
                                                <span class="spinner-border spinner-border-sm"></span>
                                            </span>
                                            <i v-else-if="parseInt(mark_submit_icon_key) == parseInt(studentIndex)"
                                                @click="marksSubmit(studentIndex)"
                                                class="bx bxs-check-square btn btn-xs text-white btn-success"></i>
                                        </td>
                                    </tr>
                                </template>
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
        </form>

        <h4 class="text-center my-4" v-else>:) You are not authorized person</h4>
    </div>
</template>

<script>
    import Radio from "../../../../../components/backend/elements/Form/Radio";

    // define model name
    const model = "higherSecondaryResult";

    // Add Or Back
    const addOrBack = {
        route: model + ".index",
        title: model,
        icon: "left-arrow-alt",
    };

    export default {
        components: { Radio },

        data() {
            return {
                class_test_marks_added: true,
                disable_master_data: true,
                sync_spinner: false,
                spinner_key: "",
                model: model,
                convert_mark: "",
                mark_submit_icon_key: "",
                subject_id: null,
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
                subject: {},
                extraData: { exam_lists: [], subjects: [] },
                hasChildSubjects: false,
                activeChildIndex: 0,
                child_subjects: [],
                loadingChildSubjects: false,
            };
        },

        directives: {
            "tab-pressed": {
                bind(el, binding, vnode) {
                    el._tabHandler = (event) => {
                        if (event.key === 'Tab') {
                            event.preventDefault();
                            // Call the bound method first
                            binding.value(event);

                            // Then manually focus the next input
                            const inputs = Array.from(document.querySelectorAll('input[type="text"]'));
                            const currentIndex = inputs.indexOf(el);
                            if (currentIndex > -1 && currentIndex < inputs.length - 1) {
                                inputs[currentIndex + 1].focus();
                            }
                        }
                    };
                    el.addEventListener('keydown', el._tabHandler);
                },
                unbind(el) {
                    el.removeEventListener('keydown', el._tabHandler);
                }
            },
        },

        methods: {
            submit() {
                this.spinner_key = "";
                if (this.$root.submit) {
                    return false;
                }

                const filteredDetails = this.data.details.filter((item) =>
                    item.marks.some((mark) => mark.total_mark > 0)
                );

                if (Object.keys(filteredDetails).length == 0) {
                    this.notify(`Please enter marks`, "warning");
                    return false;
                }

                if (confirm("Are you sure want to submit marks")) {
                    this.$root.submit = true;
                    this.$validate().then((res) => {
                        const error = this.validation.countErrors();
                        // If there is an error
                        if (error > 0) {
                            this.notify(error, "validate");
                            return false;
                        }

                        // If there is no error
                        if (res) {
                            let resultData = {
                                details: filteredDetails,
                                subject_id: this.subject_id,
                                subject: this.subject,
                                type: "all",
                            };
                            this.update(this.model, resultData, this.data.id, null, "no");
                        }
                    });
                }
            },

            syncResult(id) {
                if (!this.convert_mark) {
                    this.notify(`Please select convert mark`, "warning");
                    return false;
                }

                if (this.sync_spinner) return false;

                if (confirm("Are you sure want to sync result")) {
                    this.sync_spinner = true;
                    let convert = this.convert_mark;
                    axios
                        .get(`${this.model}-sync/${id}/${convert}`, {
                            params: { subject_id: this.subject_id },
                        })
                        .then((res) => {
                            if (Object.keys(this.subject).length > 0) {
                                this.getStudents();
                            }
                            this.notify(res.data.message, "success");
                        })
                        .finally((alw) => (this.sync_spinner = false));
                }
            },

            handleTabPressed(studentIndex, event, childIndex = 0) {
                this.marksSubmit(studentIndex, childIndex);
            },

            marksSubmit(key, childKey = 0) {
                if (this.$root.submit) {
                    return false;
                }

                this.spinner_key = key;
                this.activeChildIndex = childKey;

                // Store the current focused element
                const activeElement = document.activeElement;

                // If absent is checked, set marks to 0
                if (this.hasChildSubjects && this.data.details[key].marks[childKey].is_absent === 1) {
                    this.data.details[key].marks.forEach(mark => {
                        mark.ct_mark = '0';
                        mark.cq_mark = '0';
                        mark.mcq_mark = '0';
                        mark.practical_mark = '0';
                        mark.obtained_mark = '0';
                        mark.total_mark = '0';
                        mark.is_absent = 1;
                    });
                }

                this.$validate().then((res) => {
                    const error = this.validation.countErrors();
                    if (error > 0) {
                        this.notify(error, "validate");
                        // Restore focus if validation fails
                        if (activeElement) activeElement.focus();
                        return false;
                    }

                    if (res) {
                        let resultData = {
                            details: this.hasChildSubjects ?
                                { ...this.data.details[key], marks: [this.data.details[key].marks[childKey]] } :
                                this.data.details[key],
                            subject_id: this.hasChildSubjects ?
                                this.data.details[key].marks[childKey].subject_id :
                                this.subject_id,
                            subject: this.hasChildSubjects ?
                                this.childSubjects[childKey] :
                                this.subject,
                            type: "single",
                        };

                        this.update(this.model, resultData, this.data.id, null, "no")
                            .finally(() => {
                                this.mark_submit_icon_key = "";
                                this.activeChildIndex = 0;
                                // Restore focus after submission
                                if (activeElement) activeElement.focus();
                            });
                    }
                });
            },

            // fetchChildSubjects() {
            //     axios.get('/all-child-subjects')
            //         .then(response => {
            //             this.child_subjects = response.data;
            //             // console.log(this.child_subjects);
            //         })
            //         .catch(error => {
            //             console.error("Error fetching Child subjects:", error);
            //         });
            // },

            getSubjects() {
                let params = {
                    institution_id: this.data.institution_id,
                    group_id: this.data.group_id,
                    academic_class_id: this.data.academic_class_id,
                };
                this.get_paginate_data("classwise-subjects", params, "subjects");
            },

            selectSubject(id) {
                let subject = this.extraData.subjects.find((e) => e.subject_id == id);
                this.subject = subject ? { ...subject } : {}; // Create a copy to avoid reference issues

                // Reset child subjects
                this.childSubjects = [];
                this.hasChildSubjects = false;

                // Check if this subject has any child subjects
                if (this.child_subjects && this.child_subjects.length) {
                    this.childSubjects = this.child_subjects.filter(child => child.parent_id == id);
                    this.hasChildSubjects = this.childSubjects.length > 0;

                    // If it has children, we need to ensure they have proper marks
                    if (this.hasChildSubjects) {
                        this.childSubjects = this.childSubjects.map(child => {
                            // Find the full child subject data from extraData.subjects
                            const fullChild = this.extraData.subjects.find(s => s.subject_id == child.id) || child;
                            return {
                                ...fullChild,
                                // Inherit marks from parent if not set in child
                                ct_mark: fullChild.ct_mark || this.subject.ct_mark || 0,
                                cq_mark: fullChild.cq_mark || this.subject.cq_mark || 0,
                                mcq_mark: fullChild.mcq_mark || this.subject.mcq_mark || 0,
                                practical_mark: fullChild.practical_mark || this.subject.practical_mark || 0,
                                total_mark: fullChild.total_mark || this.subject.total_mark || 0
                            };
                        });
                    }
                }

                // If this is a child subject, ensure it has all required marks
                if (this.subject.parent_id) {
                    const parentSubject = this.extraData.subjects.find(s => s.subject_id == this.subject.parent_id);

                    if (parentSubject) {
                        this.subject = {
                            ...this.subject,
                            ct_mark: this.subject.ct_mark || parentSubject.ct_mark || 0,
                            cq_mark: this.subject.cq_mark || parentSubject.cq_mark || 0,
                            mcq_mark: this.subject.mcq_mark || parentSubject.mcq_mark || 0,
                            practical_mark: this.subject.practical_mark || parentSubject.practical_mark || 0,
                            total_mark: this.subject.total_mark || parentSubject.total_mark || 0
                        };
                    }
                }

                // Initialize marks for all students
                if (this.data.details.length > 0) {
                    this.data.details.forEach(student => {
                        if (this.hasChildSubjects) {
                            student.marks = this.childSubjects.map(child => ({
                                subject_id: child.id,
                                ct_mark: child.ct_mark || '',
                                cq_mark: child.cq_mark || '',
                                mcq_mark: child.mcq_mark || '',
                                practical_mark: child.practical_mark || '',
                                obtained_mark: '',
                                total_mark: '',
                                is_absent: 0
                            }));
                        } else {
                            student.marks = [{
                                subject_id: id,
                                ct_mark: this.subject.ct_mark || '',
                                cq_mark: this.subject.cq_mark || '',
                                mcq_mark: this.subject.mcq_mark || '',
                                practical_mark: this.subject.practical_mark || '',
                                obtained_mark: '',
                                total_mark: '',
                                is_absent: 0
                            }];
                        }
                    });
                }

                this.notify(`Please select convert mark for mark entry`, "warning");
                this.getStudents();
            },

            marksCalculate(key, field, childKey = 0) {
                this.mark_submit_icon_key = key;
                let newData = this.data.details[key];
                let mark = newData.marks[childKey];

                // Get the correct subject (parent or child)
                let currentSubject = this.hasChildSubjects ?
                    this.childSubjects[childKey] :
                    this.subject;



                // Validate the entered mark doesn't exceed maximum allowed
                if (mark[field] && parseFloat(mark[field]) > parseFloat(currentSubject[field] || 0)) {
                    mark[field] = "";
                    this.notify(
                        `Please enter a mark less than ${currentSubject[field]}`,
                        "error"
                    );
                    return;
                }

                // Calculate total exam mark components
                let totalExamMark =
                    parseFloat(currentSubject.cq_mark || 0) +
                    parseFloat(currentSubject.mcq_mark || 0) +
                    parseFloat(currentSubject.practical_mark || 0);

                console.log('Total exam mark components:', totalExamMark);

                // Get all mark values with defaults
                let ct = mark.ct_mark ? parseFloat(mark.ct_mark) : 0;
                let cq = mark.cq_mark ? parseFloat(mark.cq_mark) : 0;
                let mcq = mark.mcq_mark ? parseFloat(mark.mcq_mark) : 0;
                let prac = mark.practical_mark ? parseFloat(mark.practical_mark) : 0;


                // Calculate obtained marks
                let obtained = cq + mcq + prac;

                // Apply mark conversion if enabled
                if (parseInt(this.convert_mark)) {
                    if (totalExamMark <= 100) {
                        obtained = (obtained * 80) / 100;
                    } else if (totalExamMark > 100) {
                        let examTotalMark = parseFloat(currentSubject.total_mark || 100);
                        obtained = (obtained / examTotalMark) * 100;
                    }
                }

                // Calculate total mark (obtained + CT)
                let total_mark = obtained + ct;

                // Calculate grade and GPA
                let gradeGpa = this.letterGradeGpa(total_mark, mark, currentSubject);

                // Update the mark object
                mark["obtained_mark"] = obtained.toFixed(2);
                mark["total_mark"] = total_mark.toFixed(2);
                mark["letter_grade"] = gradeGpa["grade"];
                mark["gpa"] = gradeGpa["gpa"];

                // Update the data
                this.$set(this.data.details, key, newData);
            },

            letterGradeGpa(total_mark, mark, currentSubject) {
                let gradeGpa = {
                    grade: "F",
                    gpa: 0,
                };

                // check failed or not exam type wise
                let ctPass = parseFloat(currentSubject.ct_pass_mark);
                let ctM = parseFloat(mark.ct_mark ? mark.ct_mark : 0);
                let cqPass = parseFloat(currentSubject.cq_pass_mark);
                let cqM = parseFloat(mark.cq_mark ? mark.cq_mark : 0);
                let mcqPass = parseFloat(currentSubject.mcq_pass_mark);
                let mcqM = parseFloat(mark.mcq_mark ? mark.mcq_mark : 0);
                let prcPass = parseFloat(currentSubject.practical_pass_mark);
                let prcM = parseFloat(mark.practical_mark ? mark.practical_mark : 0);

                if (parseInt(ctPass) && parseFloat(ctM) < parseFloat(ctPass)) {
                    return gradeGpa;
                } else if (parseInt(cqPass) && parseFloat(cqM) < parseFloat(cqPass)) {
                    return gradeGpa;
                } else if (parseInt(mcqPass) && parseFloat(mcqM) < parseFloat(mcqPass)) {
                    return gradeGpa;
                } else if (parseInt(prcPass) && parseFloat(prcM) < parseFloat(prcPass)) {
                    return gradeGpa;
                }

                // define letter grade
                if (this.$root.global.letter_grades) {
                    let find = this.$root.global.letter_grades.find(
                        (e) =>
                            parseFloat(e.from_mark) <= total_mark &&
                            parseFloat(e.to_mark) >= parseFloat(total_mark)
                    );
                    if (find) {
                        gradeGpa.grade = find.grade;
                        gradeGpa.gpa = find.gpa;
                    }
                }

                return gradeGpa;
            },

            async getStudents() {
                this.$validate().then(async (res) => {
                    const error = this.validation.countErrors();
                    if (error > 0) return false;

                    let selectedStudent =
                        parseInt(this.subject.main_subject) || parseInt(this.subject.fourth_subject) ? 1 : 0;

                    // Clear existing details before fetching new ones
                    this.data.details = [];

                    if (this.hasChildSubjects) {
                        this.loadingChildSubjects = true;

                        // Fetch all child subjects first
                        const fetchPromises = this.childSubjects.map((child, index) =>
                            this.fetchStudentsForSubject(child.id, selectedStudent, index)
                        );

                        await Promise.all(fetchPromises);
                        this.loadingChildSubjects = false;
                    } else {
                        await this.fetchStudentsForSubject(this.subject_id, selectedStudent);
                    }
                });
            },

            fetchStudentsForSubject(subjectId, selectedStudent, index = 0) {
                let params = {
                    result_id: this.data.id,
                    academic_session_id: this.data.academic_session_id,
                    academic_class_id: this.data.academic_class_id,
                    department_id: this.data.department_id,
                    academic_qualification_id: this.data.academic_qualification_id,
                    subject_id: subjectId,
                    exam_id: this.data.exam_id,
                    total_exam_subjects: this.data.total_exam_subjects,
                    selected_student: selectedStudent,
                    has_child_subjects: this.hasChildSubjects,
                };

                this.$root.tableSpinner = true;

                return axios.get("/students-for-marks-entry", { params: params })
                    .then((res) => {
                        if (index === 0 || !this.hasChildSubjects) {
                            // Use Vue.set for better reactivity
                            this.$set(this.data, 'details', res.data.details);
                        } else {
                            // Ensure proper array mutation
                            res.data.details.forEach((newStudent, i) => {
                                if (this.data.details[i]) {
                                    const newMark = newStudent.marks[0];
                                    if (newMark) {
                                        this.$set(this.data.details[i].marks, index, newMark);
                                    }
                                }
                            });
                        }
                        this.class_test_marks_added = res.data.class_test_marks_added;
                    })
                    .catch((err) => {
                        console.log(err);
                        if (err.response.data && err.response.data.message) {
                            this.notify(err.response.data.message, "warning");
                        }
                        this.data.details = [];
                    })
                    .finally(() => (this.$root.tableSpinner = false));
            },

            handleArrowNavigation(event, key, fieldType) {
                const fields = ["ct_mark", "cq_mark", "mcq_mark", "practical_mark"];
                let nextKey = key;
                let nextFieldType = fieldType;

                const currentIndex = fields.indexOf(fieldType);

                if (event.key === "ArrowRight" && currentIndex < fields.length - 1) {
                    nextFieldType = fields[currentIndex + 1];
                } else if (event.key === "ArrowLeft" && currentIndex > 0) {
                    nextFieldType = fields[currentIndex - 1];
                } else if (event.key === "ArrowDown") {
                    nextKey = key + 1;
                } else if (event.key === "ArrowUp") {
                    nextKey = key - 1;
                }

                const nextInputId = `${nextFieldType}_${nextKey}`;
                const nextInput = document.getElementById(nextInputId);

                if (nextInput) {
                    nextInput.focus();
                    // nextInput.select();
                }
            },

            reqClass(field) {
                if (this.validation.hasError(field)) {
                    return "form-invalid";
                } else if (this.data[field]) {
                    return "form-valid";
                }
            },
        },

        async created() {
            let page = "create";
            if (this.$route.params.id) {
                page = "edit";
            }
            this.setBreadcrumbs(this.model, page, "Result", addOrBack);

            this.get_paginate_data(
                "get-exam",
                { allData: true, exam_type: "term" },
                "exam_lists"
            );
        },

        async mounted() {
            if (this.$route.params.id) {
                this.$root.spinner = true;
                let url = `${this.model}/${this.$route.params.id}`;
                const res = await this.callApi("get", url);
                if (res.status == 200) {
                    // Ensure data structure matches what you expect
                    this.data = {
                        ...res.data,  // Spread all properties from response
                        details: res.data.details || []  // Ensure details exists
                    };
                    this.getSubjects();

                    // If subject_id is in response, set it
                    if (res.data.subject_id) {
                        this.subject_id = res.data.subject_id;
                        this.selectSubject(res.data.subject_id);
                    }
                }
                this.$root.spinner = false;
            }

            // this.fetchChildSubjects();
        },

        // ================== validation rule for form ==================
        validators: {
            "data.academic_session_id": function (value = null) {
                return Validator.value(value).required("Session is required");
            },
            "data.academic_class_id": function (value = null) {
                return Validator.value(value).required("Academic Class is required");
            },
            "data.exam_id": function (value = null) {
                return Validator.value(value).required("Exam is required");
            },
            subject_id: function (value = null) {
                return Validator.value(value).required("Subject is required");
            },
            "data.total_exam_subjects": function (value = null) {
                return Validator.value(value).required("Exam subjects is required");
            },
        },
    };
</script>

<style scoped>
    .align-center input {
        margin: auto;
    }

    .btn-disable {
        background: #cacaca;
    }
</style>
