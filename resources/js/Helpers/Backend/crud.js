Vue.mixin({
    methods: {
        // =================  get paginate data =================
        get_paginate_data(url, params = null, allData = null, id = null) {
            try {
                this.$root.tableSpinner = true;
                if (id) {
                    url = url + '/' + id;
                } else if (this.$route.query.page) {
                    url = url + "?page=" + this.$route.query.page;
                }
                axios
                    .get(url, { params: params })
                    .then(res => {
                        if (allData) {
                            this.extraData[allData] = res.data;
                        } else {
                            this.table['datas'] = res.data.data;
                            this.table['meta'] = res.data.meta;
                            this.table['links'] = res.data.links;
                        }
                    })
                    .catch(error => console.log(error))
                    .finally(alw => this.$root.tableSpinner = false);
                // .finally(alw => setTimeout(() => this.$root.tableSpinner = false, 200));
            } catch (e) {
                return e.response;
            }
        },

        // Get Promise Request
        callApi(method, url, dataObj = null) {
            try {
                return axios({
                    method: method,
                    url: url,
                    data: dataObj
                })
            } catch (e) {
                return e.response;
            }
        },

        /**
         * get single data
         */
        get_data(model, dataVar = "data", params, spinner = true) {
            try {
                this.$root.spinner = spinner;
                axios.get(`/${model}`, { params: params })
                    .then(res => this[dataVar] = res.data)
                    .catch(error => console.log(error))
                    .finally(alw => {
                        this.$root.spinner = false
                        this.$root.tableSpinner = false
                    });
            } catch (e) {
                return e.response;
            }
        },


        // =================  get sorting =================
        get_sorting(namespace) {
            try {
                axios.get("/get-last-sorting", { params: { model: namespace } })
                    .then(res => {
                        if (res.data) {
                            this.data['sorting'] = res.data;
                        }
                    })
            } catch (e) {
                return e.response;
            }
        },


        // =================  store data =================
        store(model_name, data, redirect = null) {
    this.$root.validation_errors = {};
    this.$root.submit = true;

    console.log("➡️ Attempting to store:", model_name);
    console.log("📦 Payload data:", data);

    axios.post("/" + model_name, data)
        .then(res => {
            console.log("✅ Server response:", res.data);

            if (res.data.message) {
                this.notify(res.data.message, "success");
            } else if (res.data.error) {
                this.notify(res.data.error, "error");
                return false;
            } else if (res.data.warning) {
                this.notify(res.data.warning, "warning");
                return false;
            }

            if (res.data.message && redirect != 'no') {
                if (redirect == 'edit' && res.data.id) {
                    console.log(`🔁 Redirecting to edit page: ${model_name}.edit`, res.data.id);
                    this.$router.push({
                        name: model_name + '.edit',
                        params: { id: res.data.id },
                        query: this.$route.query
                    });
                } else {
                    let url = redirect ? redirect : model_name + '.index';
                    console.log(`🔁 Redirecting to: ${url}`);
                    this.$router.push({ name: url, query: this.$route.query });
                }
            }
        })
        .catch(error => {
            console.error("❌ Error occurred during store():", error);

            if (error.response.status === 422) {
                this.$bvModal.show("validate-error");
                if (error.response.data.exception) {
                    this.$root.exception_errors = error.response.data.exception;
                    console.warn("⚠️ Backend exception:", error.response.data.exception);
                } else {
                    this.$root.validation_errors = error.response.data.errors || {};
                    console.warn("⚠️ Validation errors:", error.response.data.errors);
                }

                this.notify("You need to fill empty mandatory fields", "warning");
            } else {
                console.error("🔴 Unexpected error response:", error.response);
            }
        })
        .finally(alw => setTimeout(() => (this.$root.submit = false), 400));
},


        // =================  update data =================
        update(model_name, data, id, image = null, redirect = null) {
            this.$root.validation_errors = {};
            this.$root.submit = true;
            if (image) {
                data.append("_method", "put");
                axios.post("/" + model_name + "/" + id, data)
                    .then(res => {
                        if (res.data.message) {
                            this.notify(res.data.message, "success");
                        } else if (res.data.error) {
                            this.notify(res.data.error, "error");
                        } else if (res.data.warning) {
                            this.notify(res.data.warning, "warning");
                        }
                        setTimeout(() => $(".update_item" + id).addClass("sorting-success"), 1000);
                        setTimeout(() => $(".update_item" + id).removeClass("sorting-success"), 6000);

                        let url = redirect ? redirect : model_name + '.index';
                        this.$router.push({ name: url, query: { page: this.$route.query.page, id: id }, })
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.$bvModal.show("validate-error");
                            if (error.response.data.exception) {
                                this.$root.exception_errors = error.response.data.exception
                            } else {
                                this.$root.validation_errors = error.response.data.errors || {};
                            }
                            this.notify("You need to fill empty mandatory fields", "warning");
                        }
                    })
                    .finally(alw => setTimeout(() => (this.$root.submit = false), 400));
            }
            else {
                axios.put("/" + model_name + "/" + id, data)
                    .then(res => {
                        if (res.data.message) {
                            this.notify(res.data.message, "success");
                        } else if (res.data.error) {
                            this.notify(res.data.error, "error");
                        } else if (res.data.warning) {
                            this.notify(res.data.warning, "warning");
                        }
                        setTimeout(() => $(".update_item" + id).addClass("sorting-success"), 1000);
                        setTimeout(() => $(".update_item" + id).removeClass("sorting-success"), 6000);

                        if (redirect != 'no') {
                            let url = redirect ? redirect : model_name + '.index';
                            let queries = this.$route.query;
                            queries.id = id;
                            this.$router.push({
                                name: url,
                                query: queries,
                            })
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.$bvModal.show("validate-error");
                            if (error.response.data.exception) {
                                this.$root.exception_errors = error.response.data.exception
                            } else {
                                this.$root.validation_errors = error.response.data.errors || {};
                            }
                            this.notify("You need to fill empty mandatory fields", "warning");
                        }
                    })
                    .finally(alw => setTimeout(() => (this.$root.submit = false), 400));
            }
        },

        // ================= destroy data =================
        destroy_data(model, id, search_data) {
            this.$root.tableSpinner = true;
            axios
                .delete(`/${model}/${id}`)
                .then(res => {
                    this.get_paginate_data(model, search_data)
                    if (res.data.message) {
                        this.notify(res.data.message, "success");
                    } else if (res.data.error) {
                        this.notify(res.data.error, "error");
                    }
                })
                .catch(error => console.log(error))
                .finally(alw => setTimeout(() => (this.$root.tableSpinner = false), 200));
        },

        // ================= get route name =================
        getRouteName(model) {
            this.table.routes = {
                view: model + ".show",
                edit: model + ".edit",
                destroy: model + ".destroy"
            };
        },
    }
});
