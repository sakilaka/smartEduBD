<template>
    <div class="col-12">
        <table class="table table-sm table-bordered table-striped mb-0">
            <thead>
                <tr>
                    <th>Institute Category</th>
                    <th>Campus</th>
                    <th>Shift</th>
                    <th>Medium</th>
                    <th>Class</th>
                    <th>Group</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="(item, key) in $parent.data.subject_assigns" :key="key">
                    <td>
                        <v-select v-model="item.institution_categories_id" label="name" :reduce="(val) => val.id"
                            :options="$root.global.institution_categories" placeholder="--Select--"
                            @input="handleCategoryChange(item)">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.campus_id" label="name" :reduce="(val) => val.id"
                            :options="campuses_filter(institutionId)" placeholder="--Select--"
                            @input="handleCampusChange(item)">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.shift_id" label="name" :reduce="(val) => val.id"
                            :options="$root.global.shifts" placeholder="--Select--" @input="handleShiftChange(item)">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.medium_id" label="name" :reduce="(val) => val.id"
                            :options="$root.global.mediums" placeholder="--Select--" @input="handleMediumChange(item)">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.academic_class_id" label="name" :reduce="(val) => val.id"
                            :options="filteredClasses(item.institution_categories_id)" placeholder="--Select--"
                            @input="handleClassChange(item)">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.group_id" label="name" :reduce="(val) => val.id"
                            :options="item.availableGroups || []" placeholder="--Select--"
                            @input="handleGroupChange(item)">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.section_id" label="section_name" :reduce="(val) => val.section_id"
                            :options="section_filter(institutionId, item)" placeholder="--Select--">
                        </v-select>
                    </td>

                    <td>
                        <v-select v-model="item.subject_id" label="name_en" :reduce="(val) => val.id"
                            :options="item.filteredSubjects || []" placeholder="--Select--">
                        </v-select>
                    </td>

                    <td>
                        <select v-model="item.status" class="form-select form-select-sm">
                            <option value="active">Active</option>
                            <option value="deactive">Deactive</option>
                        </select>
                    </td>

                    <td>
                        <i v-if="$parent.data.subject_assigns.length > 1" @click="removeRow(key)"
                            class="bx bx-minus btn btn-sm btn-danger">
                        </i>
                        <i v-if="$parent.data.subject_assigns.length === key + 1" @click="addNewRow"
                            class="bx bx-plus btn btn-sm btn-primary ml-2">
                        </i>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        props: {
            institutionId: {
                type: [Number, String],
                default: null
            }
        },

        data() {
            return {
                defaultSubjectAssign: {
                    campus_id: null,
                    shift_id: null,
                    medium_id: null,
                    group_id: null,
                    section_id: null,
                    institution_categories_id: null,
                    academic_class_id: null,
                    subject_id: null,
                    status: "active",
                    filteredSubjects: [],
                    availableGroups: []
                }
            };
        },


        watch: {
            institutionId: {
                immediate: true,
                handler(newVal, oldVal) {
                    // Only proceed if the value actually changed and we have subject_assigns
                    if (newVal && newVal !== oldVal && this.$parent.data.subject_assigns && this.$parent.data.subject_assigns.length > 0) {
                        this.$parent.data.subject_assigns.forEach(item => {
                            // Only fetch groups if we have a valid academic_class_id
                            if (item.academic_class_id && item.institution_categories_id) {
                                this.fetchGroups(item);
                            }
                        });
                    }
                }
            }
        },


        methods: {
            handleCategoryChange(item) {
                item.academic_class_id = null;
                item.group_id = null;
                item.section_id = null;
                item.subject_id = null;
                item.filteredSubjects = [];
                item.availableGroups = [];
            },

            handleCampusChange(item) {
                item.shift_id = null;
                item.medium_id = null;
                item.academic_class_id = null;
                item.group_id = null;
                item.section_id = null;
                item.subject_id = null;
                item.filteredSubjects = [];
                item.availableGroups = [];
            },

            handleShiftChange(item) {
                item.medium_id = null;
                item.academic_class_id = null;
                item.group_id = null;
                item.section_id = null;
                item.subject_id = null;
                item.filteredSubjects = [];
                item.availableGroups = [];
            },

            handleMediumChange(item) {
                item.academic_class_id = null;
                item.group_id = null;
                item.section_id = null;
                item.subject_id = null;
                item.filteredSubjects = [];
                item.availableGroups = [];
            },

            async handleClassChange(item) {
                item.group_id = null;
                item.section_id = null;
                item.subject_id = null;
                item.filteredSubjects = [];

                // Fetch groups when class changes - only if we have both required IDs
                if (item.academic_class_id && this.institutionId && item.institution_categories_id) {
                    await this.fetchGroups(item);
                }
            },

            handleGroupChange(item) {
                item.section_id = null;
                item.subject_id = null;
                item.filteredSubjects = [];

                // Fetch subjects if both class and group are selected
                if (item.academic_class_id && item.group_id) {
                    this.fetchSubjects(item);
                }
            },

            filteredClasses(categoryId) {
                if (!categoryId || !this.$root.global.classes) return [];
                return this.$root.global.classes.filter(
                    cls => cls.institution_category_id == categoryId
                );
            },

            class_mappings(insID) {
                if (!this.$root.global.institutions) return [];

                let institution = this.$root.global.institutions.find((e) => e.id == insID);
                let mappings = institution ? institution.class_mappings : [];

                return mappings.map(e => {
                    let findShift = this.$root.global.shifts.find((item) => item.id == e.shift_id);
                    let findMedium = this.$root.global.mediums.find((item) => item.id == e.medium_id);
                    let findClass = this.$root.global.classes.find((item) => item.id == e.academic_class_id);
                    let findGroup = this.$root.global.groups.find((item) => item.id == e.group_id);
                    let findSection = this.$root.global.sections.find((item) => item.id == e.section_id);

                    let item = e;
                    item.shift_name = findShift ? findShift.name : '';
                    item.medium_name = findMedium ? findMedium.name : '';
                    item.group_name = findGroup ? findGroup.name : '';
                    item.class_name = findClass ? findClass.name : '';
                    item.institution_category_id = findClass ? findClass.institution_category_id : '';
                    item.section_name = findSection ? findSection.name : '';

                    return item;
                });
            },

            section_filter(insID, item) {
                let classMappings = this.class_mappings(insID);
                let commonSectionIds = {};

                if (classMappings) {
                    return classMappings.filter(mapping => {
                        if (!commonSectionIds[mapping.section_id] &&
                            mapping.campus_id == item.campus_id &&
                            mapping.shift_id == item.shift_id &&
                            mapping.medium_id == item.medium_id &&
                            mapping.academic_class_id == item.academic_class_id &&
                            mapping.group_id == item.group_id
                        ) {
                            commonSectionIds[mapping.section_id] = true;
                            return true;
                        }
                        return false;
                    });
                }
                return [];
            },

            async fetchGroups(item) {
                // Skip if we already have groups for this class
                if (item.availableGroups && item.availableGroups.length > 0 &&
                    item.availableGroups[0].classId === item.academic_class_id) {
                    return;
                }

                if (!this.institutionId || !item.academic_class_id) return;

                try {
                    const response = await axios.get(`/get-groups/${this.institutionId}/${item.academic_class_id}`);
                    let groups = response.data || [];

                    // Format groups if they come as strings
                    if (groups.length > 0 && typeof groups[0] !== 'object') {
                        groups = groups.map(group => ({
                            id: group,
                            name: group,
                            classId: item.academic_class_id // Store class ID for reference
                        }));
                    } else {
                        // Add classId reference if not already present
                        groups = groups.map(group => ({
                            ...group,
                            classId: item.academic_class_id
                        }));
                    }

                    item.availableGroups = groups;

                    // Preserve existing selection if not in the new list
                    if (item.group_id && !groups.some(g => g.id === item.group_id)) {
                        groups.push({
                            id: item.group_id,
                            name: item.group_id,
                            classId: item.academic_class_id
                        });
                        item.availableGroups = groups;
                    }
                } catch (error) {
                    console.error(error);
                    this.notify('Failed to load groups', 'error');
                    item.availableGroups = [];
                } finally {
                    this.$root.spinner = false;
                }
            },

            fetchSubjects(item) {
                if (!item.academic_class_id || !item.group_id) return;

                axios.get(`/get-subjects-by-class/${item.academic_class_id}/${item.group_id}`)
                    .then(res => {
                        this.$set(item, 'filteredSubjects', res.data || []);
                        item.subject_id = null;
                    })
                    .catch(err => {
                        console.error("Failed to load subjects", err);
                        this.$set(item, 'filteredSubjects', []);
                    });
            },

            addNewRow() {
                this.$parent.data.subject_assigns.push({
                    ...JSON.parse(JSON.stringify(this.defaultSubjectAssign))
                });
            },

            removeRow(index) {
                this.$parent.data.subject_assigns.splice(index, 1);
            },

            campuses_filter(institution_id) {
                if (!institution_id || !this.$root.global.campuses) return [];
                return this.$root.global.campuses.filter(campus => campus.institution_id == institution_id);
            }
        }
    };
</script>
