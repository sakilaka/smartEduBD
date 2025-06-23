import AppStroage from './AdminAppStroage'
class Admin {
    login(data) {
        AppStroage.store(data);
    }
    loggedIn() {
        return AppStroage.logged();
    }
    id() {
        if (this.loggedIn()) {
            if (AppStroage.getAdmin()) {
                return AppStroage.getAdmin()['id'];
            } else {
                return "";
            }
        }
    }
    info() {
        if (this.loggedIn()) {
            if (AppStroage.getAdmin()) {
                return AppStroage.getAdmin();
            } else {
                return "";
            }
        }
    }
    logout() {
        axios.post("/logout").then(res => {
            AppStroage.clear();
            window.location = laravel.baseurl + '/admin/loginme'
        }).catch(error => console.log(error));
    }
}

export default Admin = new Admin();