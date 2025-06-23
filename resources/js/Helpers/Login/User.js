import AppStroage from './UserAppStroage'
class User {
    login(data) {
        AppStroage.store(data);
    }
    loggedIn() {
        return AppStroage.logged();
    }
    name() {
        if (this.loggedIn()) {
            if (AppStroage.getUser()) {
                return AppStroage.getUser()['name'];
            } else {
                return "";
            }
        }
    }
    id() {
        if (this.loggedIn()) {
            if (AppStroage.getUser()) {
                return AppStroage.getUser()['id'];
            } else {
                return "";
            }
        }
    }
    logout() {
        axios.post("/logout").then(res => {
            AppStroage.clear();
            window.location = laravel.baseurl + '/login'
        }).catch(error => console.log(error));
    }
}

export default User = new User();