<template>
  <div class="top-menu ms-auto">
    <ul class="navbar-nav align-items-center">
      <li class="nav-item mobile-search-icon">
        <a class="nav-link" href="#"> <i class="bx bx-search"></i> </a>
      </li>

      <!-- Icon -->
      <li class="nav-item dropdown dropdown-large d-none">
        <a
          class="nav-link dropdown-toggle dropdown-toggle-nocaret"
          href="#"
          role="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="bx bx-category" title="System Administrator"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
          <div class="row row-cols-3 g-3 p-3">
            <div class="col text-center">
              <router-link :to="{ name: 'admin.index' }" class="text-dark">
                <div class="app-box mx-auto bg-gradient-cosmic text-white">
                  <i class="bx bxs-user-account"></i>
                </div>
                <div class="app-title">Admin</div>
              </router-link>
            </div>
            <div class="col text-center">
              <router-link :to="{ name: 'role.index' }" class="text-dark">
                <div class="app-box mx-auto bg-gradient-burning text-white">
                  <i class="bx bx-user-pin"></i>
                </div>
                <div class="app-title">Role</div>
              </router-link>
            </div>
            <div class="col text-center">
              <div class="app-box mx-auto bg-gradient-lush text-white">
                <i class="bx bx-shield"></i>
              </div>
              <div class="app-title">Site Settings</div>
            </div>
            <div class="col text-center">
              <div class="app-box mx-auto bg-gradient-kyoto text-dark">
                <i class="bx bx-notification"></i>
              </div>
              <div class="app-title">Menus</div>
            </div>
            <div class="col text-center">
              <div class="app-box mx-auto bg-gradient-blues text-dark">
                <i class="bx bx-file"></i>
              </div>
              <div class="app-title">Module Craete</div>
            </div>
            <div class="col text-center">
              <div class="app-box mx-auto bg-gradient-moonlit text-white">
                <i class="bx bx-filter-alt"></i>
              </div>
              <div class="app-title">Site Map</div>
            </div>
          </div>
        </div>
      </li>

      <!-- Notifications -->
      <li class="nav-item dropdown dropdown-large">
        <a
          class="
            nav-link
            dropdown-toggle dropdown-toggle-nocaret
            position-relative
          "
          href="#"
          role="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <span class="alert-count">
            {{ notification_count }}
          </span>
          <i class="bx bx-bell" title="Notifications"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
          <a href="javascript:;">
            <div class="msg-header">
              <p class="msg-header-title">Notifications</p>
              <p
                @click="notify('read all notification', 'confirm')"
                class="msg-header-clear ms-auto"
              >
                Marks all as read
              </p>
            </div>
          </a>
          <div
            v-if="Object.keys(notifications).length > 0"
            class="header-notifications-list"
          >
            <a
              v-for="(noti, key) in notifications"
              :key="key"
              href="javascript:void(0)"
              @click="viewNotification(noti.id, key)"
              class="dropdown-item"
            >
              <div class="d-flex align-items-center">
                <div class="notify bg-light-danger text-danger">
                  <i class="bx bx-message-detail"></i>
                </div>
                <div class="flex-grow-1">
                  <h6 class="msg-name">
                    {{ noti.title.substring(0, 25) }}
                    <span
                      v-if="noti.duration && !noti.new"
                      class="msg-time float-end"
                    >
                      {{ noti.duration }}
                    </span>
                    <span
                      v-else
                      class="
                        float-end
                        badge
                        rounded-pill
                        text-danger
                        bg-light-danger
                      "
                    >
                      <i style="font-size: 8px" class="bx bxs-circle"></i>
                      new
                    </span>
                  </h6>
                  <p class="msg-info">
                    {{ noti.description.substring(0, 40) }}...
                  </p>
                </div>
              </div>
            </a>
          </div>
          <div v-else class="header-notifications-list text-center py-5">
            No notifications yet !!
          </div>
          <router-link :to="{ name: 'notification.index' }">
            <div class="text-center msg-footer">View All Notifications</div>
          </router-link>
        </div>
      </li>

      <!-- Message -->
      <li class="nav-item dropdown dropdown-large">
        <a
          class="
            nav-link
            dropdown-toggle dropdown-toggle-nocaret
            position-relative
          "
          href="#"
          role="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <span class="alert-count"> {{ message_count }}</span>
          <i class="bx bx-comment" title="Messages"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
          <a href="javascript:;">
            <div class="msg-header">
              <p class="msg-header-title">Messages</p>
              <p class="msg-header-clear ms-auto">Marks all as read</p>
            </div>
          </a>
          <div class="header-message-list">
            <a class="dropdown-item d-none" href="javascript:;">
              <div class="d-flex align-items-center">
                <div class="user-online">
                  <img
                    :src="$root.userimage"
                    class="msg-avatar"
                    alt="user avatar"
                  />
                </div>
                <div class="flex-grow-1">
                  <h6 class="msg-name">
                    Daisy Anderson
                    <span class="msg-time float-end">5 sec ago</span>
                  </h6>
                  <p class="msg-info">The standard chunk of lorem</p>
                </div>
              </div>
            </a>
            <div class="header-notifications-list text-center py-5">
              No messages yet !!
            </div>
          </div>
          <a href="javascript:;">
            <div class="text-center msg-footer">View All Messages</div>
          </a>
        </div>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  data() {
    return {
      notifications: [],
      notification_count: 0,
      message_count: 0,
    };
  },
  methods: {
    callBack() {
      axios.get("/read-notifications").then((res) => {
        this.notify(res.data.message, "success");
        this.getNotificatios();
      });
    },
    getNotificatios() {
      axios.get("/get-notifications").then((res) => {
        this.notifications = res.data.notifications;
        this.notification_count = res.data.count;
      });
    },
    viewNotification(id, key) {
      this.notifications.splice(key, 1);
      this.notification_count -= 1;
      this.$router.push({ name: "notification.show", params: { id: id } });
    },
  },
  mounted() {
    Echo.channel("order-notify-channel").listen("OrderNotify", (e) => {
      this.notifications.unshift(e.message);
      this.notification_count += 1;
    });

    this.getNotificatios();
  },
};
</script>