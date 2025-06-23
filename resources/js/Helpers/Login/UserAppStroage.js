class UserAppStroage {
    store(data) {
        this.storeData(data);
    }
    storeData(data) {
        localStorage.setItem('loggedUser', true);
        localStorage.setItem('user', JSON.stringify(data));
    }
    clear() {
        localStorage.removeItem('loggedUser')
        localStorage.removeItem('user')
    }
    getUser() {
        if (localStorage.getItem('user')) {
            try {
                return JSON.parse(localStorage.getItem('user'));
            } catch (e) {
                return [];
            }
        }
    }
    logged() {
        return localStorage.getItem('loggedUser')
    }
}

export default UserAppStroage = new UserAppStroage()