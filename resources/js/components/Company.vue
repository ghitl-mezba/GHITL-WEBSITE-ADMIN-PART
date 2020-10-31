<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card" v-if="$gate.isAdmin()">
            <div
              class="card-header p-2 b text-white"
              style="background-color: #6cb3ff"
            >
              Company information
            </div>
            <div class="card-body">
              <form
                @submit.prevent="form.id ? updateCompany() : createCompany()"
              >
                <div class="form-group">
                  <label>Name <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.name"
                    type="text"
                    name="name"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }"
                    placeholder="Enter name"
                    required
                  />
                  <has-error :form="form" field="name"></has-error>
                </div>
                <div class="form-group">
                  <label>Address <span style="color: #fb0606">*</span></label>
                  <textarea
                    v-model="form.address"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('address') }"
                    placeholder="Enter address"
                    required
                    style="resize:none;"
                  ></textarea>
                  <has-error :form="form" field="name"></has-error>
                </div>
                <div class="form-group">
                  <label>Email <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.email"
                    type="email"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('email') }"
                    placeholder="Enter email"
                    required
                  />
                  <has-error :form="form" field="email"></has-error>
                </div>

                <div class="form-group">
                  <label
                    >Web address <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.web"
                    type="text"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('web') }"
                    placeholder="Enter web address"
                    autocomplete="false"
                    required
                  />
                  <has-error :form="form" field="web"></has-error>
                </div>

                <div class="form-group">
                  <label
                    >Mobile NO <span style="color: #fb0606">*</span></label
                  >
                  <input
                    type="text"
                    v-model="form.mobile"
                    placeholder="Enter mobile no."
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('mobile') }"
                    required
                  />

                  <has-error :form="form" field="mobile"></has-error>
                </div>
                <div class="form-group">
                  <label>Phone NO.</label>
                  <input
                    type="text"
                    v-model="form.phone"
                    placeholder="Enter phone no."
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('phone') }"
                  />

                  <has-error :form="form" field="type"></has-error>
                </div>
                <div class="form-group">
                  <label>Fax</label>
                  <input
                    type="text"
                    v-model="form.fax"
                    placeholder="Enter fax"
                    class="form-control"
                  />

                  <has-error :form="form" field="type"></has-error>
                </div>
                <div class="form-group">
                  <label>Office time </label>
                  <input
                    type="text"
                    v-model="form.office_time"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('office_time') }"
                    placeholder="Enter office time"
                  />
                  <has-error :form="form" field="office_time"></has-error>
                </div>
                <div class="form-group">
                  <label>Image</label>
                  <div>
                    <input
                      type="file"
                      @v-model="form.logo"
                      @change="browse_image"
                      accept=".png, .jpg, .jpeg"
                    />
                    <input type="hidden" @v-model="form.hidden_image" />
                  </div>
                  <div v-if="show_image !== null">
                    <img
                      v-bind:src="show_image"
                      style="width: 90px; height: 90px"
                    />
                  </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">
                      <i class="fa fa-save"></i> Save
                    </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      editmode: false,
      company: {},
      show_image: null,
      form: new Form({
        id: "",
        name: "",
        email: "",
        phone: "",
        mobile: "",
        fax: "",
        address: "",
        web: "",
        office_time: "",
        logo: "",
        hidden_image: "",
      }),
    };
  },
  methods: {
    loadCompany() {
      if (this.$gate.isAdmin()) {
        axios
          .get("/api/company")
          .then(({ data }) => (this.company = data.data));
      }
    },
    browse_image(e) {
      let file = e.target.files[0];
      let reader = new FileReader();

      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.logo = reader.result;
          this.show_image = this.form.logo;
          this.form.hidden_image = "";
        };
        reader.readAsDataURL(file);
      } else {
        Toast.fire({
          icon: "error",
          title: "File size can not be bigger than 2 MB",
        });
      }
    },
    updateCompany() {
      this.$Progress.start();
      this.form
        .put("api/company/" + this.form.id)
        .then((response) => {
          // success
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    createCompany() {
      this.form
        .post("/api/company")
        .then((response) => {
          Toast.fire({
            icon: "success",
            title: "Company Created Successfully",
          });

          this.$Progress.finish();
          this.loadCompany();
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
  },

  mounted() {
    console.log("Company Component mounted.");
  },
  created() {
    this.$Progress.start();
    axios.get("api/company").then(({ data }) => {
      this.form.fill(data.data);
      console.log(data.data);
      if (this.form.logo !== null) {
        this.show_image = "upload/img/" + this.form.logo;
      } else {
        this.show_image = null;
      }
    });

    this.$Progress.finish();
  },
};
</script>
