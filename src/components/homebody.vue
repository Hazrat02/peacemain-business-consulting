<template>
  <section class="slider-area">
    <div id="particles-js">
      <bgs />

      <div
        id="carousel-example-generic"
        class="carousel slide"
        data-ride="carousel"
      >
        <div class="carousel-inner" role="listbox">
          <div class="item active">
            <div class="carousel-captions caption-1">
              <div class="container">
                <div class="row">
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="slider-content">
                      <h2 v-animate data-animation="animated bounceInLeft">
                        B2B Lending & Investment Platform Debt Exchange Trade
                        Fund -
                        <br />
                        Fixed Income Securities Systems
                      </h2>
                      <div class="buttons">
                        <RouterLink
                          v-if="!authUser"
                          to="/contact"
                          class="btn2"
                          v-animate
                          data-animation="animated bounceInUp"
                          ><i class="fa fa-user"> </i
                          ><span> Contact Us</span></RouterLink
                        >

                        <div v-if="authUser">
                          <RouterLink
                            v-if="authUser.role == '0'"
                            to="/admin/dashboard"
                            class="btn2"
                            v-animate
                            data-animation="animated bounceInUp"
                            >Dashboard</RouterLink
                          >
                          <RouterLink
                            v-else
                            to="/dashboard"
                            class="btn2"
                            v-animate
                            data-animation="animated bounceInUp"
                            >Dashboard</RouterLink
                          >
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="hero-area animated bounceInDown">
                      <div class="">
                        <div class="row">
                          <div class="">
                            <div class="hero-image">
                              <img
                                class="main-image"
                                src="./../assets/frontend/images/hero/home2-bg.png"
                                alt="#"
                              />
                              <img
                                class="h2-move-1"
                                src="./../assets/frontend/images/hero/h2-bit-l.png"
                                alt="#"
                              />
                              <img
                                class="h2-move-2"
                                src="./../assets/frontend/images/hero/h2-bit-m.png"
                              />
                              <img
                                class="h2-move-3"
                                style="width: 20%"
                                src="./../assets/frontend/images/icons/4.png"
                                alt="#"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>


<script>
import bgs from "./Bgs.vue";
import { isAuthenticated } from "./../middleware/index";
import { useAuthUserStore } from "./../stores/user";

export default {
  components: {
    bgs,
  },

  data() {
    return {
      authUser: [],
    };
  },
  async created() {
    if (isAuthenticated() == true) {
      // auth user data +++++++++++++++++++++++++++++

      const userStore = useAuthUserStore();
      const authUser = userStore.authUser;

      if (authUser) {
        this.authUser = authUser;
      } else {
        // userStore.reSetAuthUser();
        this.authUser = await userStore.reSetAuthUser();
      }
    } else {
      this.authUser = "";
    }
  },
};
</script>
<style scoped>
</style>
