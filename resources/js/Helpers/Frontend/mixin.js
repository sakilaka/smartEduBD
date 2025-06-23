Vue.mixin({
    methods: {
        /*-----notification-----*/
        notify(msg, type, id = null) {
            if (type == 'validate') {
                this.$toast.warning("You need to fill " + msg + " more empty mandatory fields", { timeout: 5000 });
            } else if (type == 'confirm') {
                this.$toast.info({
                    component: ToastConfirm, props: { msg: msg },
                    listeners: { confirm: () => this.callBack(id) },
                }, { position: "top-center", timeout: 0, closeButton: false });
            } else {
                this.$toast[type](msg);
            }
        },

        /**
        * Validations error & exceptions
        * 
        * @param {Array} err 
        * @returns array
        */
        catchErrors(err) {
            let errors = {};
            if (err.response && err.response.data && err.response.data.errors) {
                if (err.response.status == 422) {
                    this.notify(err.response.data.message, "error");
                    errors = err.response.data.errors;
                } else if (err.response.data.data.message) {
                    this.notify(err.response.data.data.message, "error");
                } else if (err.response.data.message) {
                    this.notify(err.response.data.message, "error");
                }
            } else if (err.response && err.response.data.message) {
                this.notify(err.response.data.message, "error");
            } else if (err.response && err.response.statusText) {
                this.notify(err.response.statusText, "error");
            } else {
                this.notify(err.message, "error");
            }
            return errors;
        },

        // ================= scroll top =================
        scrollTop(x = 0, y = 0) {
            window.scrollTo(x, y)
        },
    }
})
