export function authGuard(to, from, next) {
    if (Boolean(Admin.loggedIn())) {
        next();
    } else {
        next({ name: 'admin.loginme' });
    }
}