<template>
  <div class="wrapper">
    <!--Header-->
    <Header />

    <!--Nav Menu-->
    <Nav />

    <!-- Page Content-->
    <div class="page-wrapper">
      <div class="page-content">
        <!--Breadcrumbs-->
        <Breadcrumbs />

        <!-- Validation Message -->
        <ValidationMessage />
        <!-- Validation Message -->

        <router-view></router-view>

        <div class="box box-success p-5" v-if="$root.spinner">
          <div class="row d-flex align-items-center justify-content-center">
            <Spinner />
          </div>
        </div>
      </div>
    </div>

    <!--footer-->
    <Footer />
  </div>
</template>

<script>
import Header from "./../../components/backend/Header";
import Nav from "./../../components/backend/Nav";
import Footer from "./../../components/backend/Footer";
import Breadcrumbs from "./../../components/backend/elements/Breadcrumbs";
import ValidationMessage from "./../../components/backend/elements/ValidationMessage";
export default {
  components: {
    Breadcrumbs,
    Header,
    Nav,
    Footer,
    ValidationMessage,
  },
  beforeCreate() {
    if (!Admin.id()) {
      Admin.logout();
    }
  },
};
</script>
