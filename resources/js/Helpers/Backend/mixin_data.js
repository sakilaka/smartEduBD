
Vue.mixin({
    methods: {
        /*-----Sessions Filters-----*/
        sessions_filter: function (insCID) {
            if (this.$root.global.sessions) {
                return this.$root.global.sessions.filter((e) => e.institution_category_id == insCID)
            }
            return [];
        },

        exam_filter: function (insCID) {
            if (this.$root.global.sessions) {
                return this.$root.global.exam.filter((e) => e.institution_category_id == insCID)
            }
            return [];
        },

        /*-----Campus Filters-----*/
        campuses_filter: function (insID) {
            if (this.$root.global.institutions) {
                let institution = this.$root.global.institutions.find((e) => e.id == insID)
                return institution ? institution.campuses : [];
            }
            return [];
        },

        /*-----Class mappings-----*/
        class_mappings: function (insID) {
            if (this.$root.global.institutions) {
                let institution = this.$root.global.institutions.find((e) => e.id == insID)
                let mappings = institution ? institution.class_mappings : [];

                return mappings.map(e => {
                    let findShift = this.$root.global.shifts.find((item) => item.id == e.shift_id)
                    let findMedium = this.$root.global.mediums.find((item) => item.id == e.medium_id)
                    let findClass = this.$root.global.classes.find((item) => item.id == e.academic_class_id)
                    let findGroup = this.$root.global.groups.find((item) => item.id == e.group_id)
                    let findSection = this.$root.global.sections.find((item) => item.id == e.section_id)

                    let item = e;
                    item.shift_name = findShift ? findShift.name : ''
                    item.medium_name = findMedium ? findMedium.name : ''
                    item.group_name = findGroup ? findGroup.name : ''
                    item.class_name = findClass ? findClass.name : ''
                    item.institution_category_id = findClass ? findClass.institution_category_id : ''
                    item.section_name = findSection ? findSection.name : ''

                    return item;
                });
            }
            return [];
        },

        /*-----Shift mappings-----*/
        shift_filter: function (insID, dataSource = 'data') {
            let data = this[dataSource]

            let classMappings = this.class_mappings(insID);
            if (classMappings) {
                let commonSihftIds = {};
                return classMappings.filter(item => {
                    if (!commonSihftIds[item.shift_id] && item.campus_id == data.campus_id) {
                        commonSihftIds[item.shift_id] = true;
                        return true;
                    }
                    return false;
                });
            }
            return [];
        },

        /*-----Medium mappings-----*/
        medium_filter: function (insID, dataSource = 'data') {
            let data = this[dataSource]

            let classMappings = this.class_mappings(insID);
            if (classMappings) {
                let commonMediumIds = {};
                return classMappings.filter(item => {
                    if (!commonMediumIds[item.medium_id] && item.campus_id == data.campus_id && item.shift_id == data.shift_id) {
                        commonMediumIds[item.medium_id] = true;
                        return true;
                    }
                    return false;
                });
            }
            return [];
        },

        /*-----Class Filters-----*/
        class_filter: function (insID, dataSource = 'data') {
            let data = this[dataSource]

            let classMappings = this.class_mappings(insID);
            if (classMappings) {
                let commonAcademicClassIds = {};
                return classMappings.filter(item => {
                    if (!commonAcademicClassIds[item.academic_class_id] &&
                        item.campus_id == data.campus_id &&
                        item.shift_id == data.shift_id &&
                        item.medium_id == data.medium_id
                    ) {
                        commonAcademicClassIds[item.academic_class_id] = true;
                        return true;
                    }
                    return false;
                });
            }
            return [];
        },

        /*-----Category wise Class Filters-----*/
        category_classes_filter: function (insID, categoryID, dataSource = 'data') {
            let classes = this.class_filter(insID, dataSource);
            return classes.filter(e => e.institution_category_id == categoryID);
        },

        /*-----Group Filters-----*/
        group_filter: function (insID, dataSource = 'data') {
            let data = this[dataSource]

            let classMappings = this.class_mappings(insID);
            let commonGroupIds = {};
            if (classMappings) {
                return classMappings.filter(item => {
                    if (!commonGroupIds[item.group_id] &&
                        item.campus_id == data.campus_id &&
                        item.shift_id == data.shift_id &&
                        item.medium_id == data.medium_id &&
                        item.academic_class_id == data.academic_class_id
                    ) {
                        commonGroupIds[item.group_id] = true;
                        return true;
                    }
                    return false;
                });
            }
            return [];
        },

        /*-----Section Filters-----*/
        section_filter: function (insID, dataSource = 'data') {
            let data = this[dataSource]

            let classMappings = this.class_mappings(insID);
            let commonSectionIds = {};
            if (classMappings) {
                return classMappings.filter(item => {
                    if (!commonSectionIds[item.section_id] &&
                        item.campus_id == data.campus_id &&
                        item.shift_id == data.shift_id &&
                        item.medium_id == data.medium_id &&
                        item.academic_class_id == data.academic_class_id &&
                        item.group_id == data.group_id
                    ) {
                        commonSectionIds[item.section_id] = true;
                        return true;
                    }
                    return false;
                });
            }
            return [];
        },
    },

    watch: {
        "data.institution_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.campus_id = null;
                this.data.shift_id = null;
                this.data.medium_id = null;
                this.data.academic_class_id = null;
                this.data.group_id = null;
                this.data.section_id = null;
            }
        },
        "data.institution_category_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.academic_session_id = null;
                this.data.academic_class_id = null;
            }
        },
        "data.campus_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.shift_id = null;
                this.data.medium_id = null;
                this.data.academic_class_id = null;
                this.data.group_id = null;
                this.data.section_id = null;
            }
        },
        "data.shift_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.medium_id = null;
                this.data.academic_class_id = null;
                this.data.group_id = null;
                this.data.section_id = null;
            }
        },
        "data.medium_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.academic_class_id = null;
                this.data.group_id = null;
                this.data.section_id = null;
            }
        },
        "data.academic_class_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.group_id = null;
                this.data.section_id = null;
            }
        },
        "data.group_id": function (id) {
            if (!id) return false;
            if (this.$route.name.includes('create') || this.$route.name.includes('import')) {
                this.data.section_id = null;
            }
        },
    }
})
