import ToastConfirm from "./../../components/backend/elements/ToastDelete";
import moment from "moment";

Vue.mixin({
    data() {
        return {
            momentFormat: {
                parse: (value) => {
                    if (value) {
                        let date = new Date(value);
                        return moment(date, "YYYY-M-DD").toDate();
                    }
                    return null;
                },
            },
        }
    },
    methods: {
        /*-----Set Breadcrumbs-----*/
        setBreadcrumbs(model, type, title = null, addOrBack) {
            var breadcrumb = [];
            let brTitle = title ? title : model;
            if (type == 'index') {
                breadcrumb = [
                    { route: model + ".index", title: brTitle + " List" }
                ];
            } else if (type == 'create') {
                breadcrumb = [
                    { route: model + ".index", title: brTitle + " List" },
                    { route: model + ".create", title: brTitle + " Create" }
                ];
            } else if (type == 'edit') {
                breadcrumb = [
                    { route: model + ".index", title: brTitle + " List" },
                    { route: model + ".edit", title: brTitle + " Edit" }
                ];
            } else if (type == 'view') {
                breadcrumb = [
                    { route: model + ".index", title: brTitle + " List" },
                    { route: model + ".show", title: brTitle + " View" }
                ];
            }
            let obj = {
                addOrBack: addOrBack,
                breadcrumb: breadcrumb,
            }
            breadcrumbs.dispatch("setBreadcrumbs", obj);
        },

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

        /*-----scroll top-----*/
        scrollTop(x = 0, y = 0) {
            window.scrollTo(x, y)
        },

        /*-----SHOW IMAGE-----*/
        showImage(e, imagePath, dataPath, fileClass = null, pdf = null) {
            $("." + fileClass).children('.custom-file-label').html("File attached");
            let files = e.target.files || e.dataTransfer.files;
            if (files.length) {
                this.images[imagePath] = e.target.files[0];
                if (dataPath) {
                    if (pdf) {
                        this.data[dataPath] = this.$root.attach;
                    } else {
                        this.data[dataPath] = URL.createObjectURL(e.target.files[0]);
                    }
                }
            }
        },

        /*-----SHOW GLOBALLY IMAGE-----*/
        showImageGlobal(e, imagePath, dataPath, fileClass = null, pdf = null) {
            $("." + fileClass).children('.custom-file-label').html("File attached");
            let files = e.target.files || e.dataTransfer.files;
            if (files.length) {
                this.$parent.data[dataPath] = e.target.files[0];
                if (dataPath) {
                    if (pdf) {
                        this.$parent.images[dataPath] = this.$root.attach;
                    } else {
                        this.$parent.images[dataPath] = URL.createObjectURL(e.target.files[0]);
                    }
                }
            }
        },
        /*-----SHOW CROP IMAGE-----*/
        showCropImage(event, field, fileClass = null) {
            $("." + fileClass).children('.custom-file-label').html("File attached");
            var input = event.target;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = e => {
                    this.$parent.images[field] = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
            this.$bvModal.show('canvas')
        },

        /*-----print html page-----*/
        print: function (elementId, documentTitle) {
            $(".p-show").show();
            $(".p-none").css("display", "none");
            $(".print-btn").css("display", "none");
            $(".action").css("display", "none");
            $(".base-table-thead").removeClass("bg-purple text-white");
            $(".table-responsive").removeClass("table-fixed");
            $(".printArea").removeClass("table-fixed");
            setTimeout(() => {
                $(".action").show();
                $(".print-btn").show();
                $(".p-none").show();
                $(".p-show").css("display", "none");
                $(".base-table-thead").addClass("bg-purple text-white");
                $(".table-responsive").addClass("table-fixed");
                $(".printArea").addClass("table-fixed");
            }, 500);
            const prtHtml = document.getElementById(elementId).innerHTML;
            let customStyle = `
                <style>
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    tr, th, td {
                        border: 1px solid black;
                        padding: 8px;
                    }
                </style>
            `;
            let stylesHtml = '';
            for (const node of [...document.querySelectorAll('link[rel="stylesheet"], style')]) {
                stylesHtml += node.outerHTML;
            }
            const WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            WinPrint.document.write(`<!DOCTYPE html>
                <html>
                <head>
                <title>${documentTitle}</title>
                ${stylesHtml}
                ${customStyle}
                </head>
                <body>
                    ${prtHtml}
                </body>
                </html>`);
            setTimeout(() => {
                WinPrint.document.close();
                WinPrint.focus();
                WinPrint.print();
            }, 300);
            // WinPrint.close();
        }
    }
})
